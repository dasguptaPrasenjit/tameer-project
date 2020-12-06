import { Component, ElementRef, NgZone, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef } from '@angular/material/dialog';
import { MapsAPILoader } from '@agm/core';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { PickupService } from 'src/app/shared/services/api/pickup.service';

@Component({
    selector: 'app-save-pickup',
    templateUrl: './save-pickup.component.html',
    styleUrls: ['./save-pickup.component.scss']
})
export class SavePickupComponent implements OnInit {
    @ViewChild('searchSender') public searchSenderElementRef: ElementRef;
    @ViewChild('searchReceiver') public searchReceiverElementRef: ElementRef;
    form: FormGroup;
    paymentMethods = ['COD', 'ONLINE'];
    payerTypes = ['SENDER', 'RECEIVER'];
    latitudeSender: number;
    longitudeSender: number;
    zoomSender: number;
    latitudeReceiver: number;
    longitudeReceiver: number;
    zoomReceiver: number;
    private geoCoder;
    constructor(
        public dialogRef: MatDialogRef<SavePickupComponent>,
        private _formBuilder: FormBuilder,
        public _ProgressBarService: ProgressBarService,
        private _ToastService: ToastService,
        private _PickupService: PickupService,
        private mapsAPILoader: MapsAPILoader,
        private ngZone: NgZone
    ) { }

    ngOnInit(): void {
        this.createForm();
        this.mapsAPILoader.load().then(() => {
            this.setCurrentLocation();
            this.geoCoder = new google.maps.Geocoder;

            let autocompleteSender = new google.maps.places.Autocomplete(this.searchSenderElementRef.nativeElement);
            autocompleteSender.addListener("place_changed", () => {
                this.ngZone.run(() => {
                    let place: google.maps.places.PlaceResult = autocompleteSender.getPlace();

                    if (place.geometry === undefined || place.geometry === null) {
                        return;
                    }

                    this.latitudeSender = place.geometry.location.lat();
                    this.longitudeSender = place.geometry.location.lng();
                    this.zoomSender = 12;
                    this.getAddress(this.latitudeSender, this.longitudeSender, "SENDER");
                });
            });

            let autocompleteReceiver = new google.maps.places.Autocomplete(this.searchReceiverElementRef.nativeElement);
            autocompleteReceiver.addListener("place_changed", () => {
                this.ngZone.run(() => {
                    let place: google.maps.places.PlaceResult = autocompleteReceiver.getPlace();

                    if (place.geometry === undefined || place.geometry === null) {
                        return;
                    }

                    this.latitudeReceiver = place.geometry.location.lat();
                    this.longitudeReceiver = place.geometry.location.lng();
                    this.zoomReceiver = 12;
                    this.getAddress(this.latitudeReceiver, this.longitudeReceiver, "RECEIVER");
                });
            });
        });
    }

    createForm() {
        this.form = this._formBuilder.group({
            payment_method: ["COD", Validators.required],
            payer: ["SENDER", Validators.required],
            item_type: ["Goods", Validators.maxLength(45)],
            product_name: ["", Validators.maxLength(255)],
            weight: ["5KG", Validators.maxLength(100)],
            distance: [0, [Validators.required, Validators.min(1), Validators.maxLength(10)]],
            cost_per_km: [10.00, [Validators.required, Validators.min(1), Validators.maxLength(10)]],
            payable_amount: [0, [Validators.required, Validators.min(1), Validators.maxLength(10)]],
            sender_name: ["", Validators.maxLength(100)],
            sender_mobile: ["", Validators.maxLength(10)],
            sender_address: ["", Validators.maxLength(255)],
            sender_pin: ["", Validators.maxLength(20)],
            sender_landmark: ["", Validators.maxLength(255)],
            sender_latitude: ["", Validators.required],
            sender_longitude: ["", Validators.required],
            receiver_name: ["", Validators.maxLength(100)],
            receiver_mobile: ["", Validators.maxLength(10)],
            receiver_address: ["", Validators.maxLength(255)],
            receiver_pin: ["", Validators.maxLength(20)],
            receiver_landmark: ["", Validators.maxLength(255)],
            receiver_latitude: ["", Validators.required],
            receiver_longitude: ["", Validators.required]
        });
    }

    submit(e) {
        e.preventDefault();
        if (this.form.valid) {
            const payload = this.form.value;
            if (payload.receiver_mobile) {
                payload.receiver_mobile = "+91" + payload.receiver_mobile;
            }
            if (payload.sender_mobile) {
                payload.sender_mobile = "+91" + payload.sender_mobile;
            }
            this._ProgressBarService.show();
            this._PickupService.addPickup(payload).subscribe((result: any) => {
                this._ProgressBarService.hide();
                if (result.success) {
                    this._ToastService.info(result.message);
                    this.dialogRef.close(true);
                    this.form.reset();
                } else {
                    this._ToastService.error(result.message);
                }
            });
        }
    }

    private setCurrentLocation() {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition((position) => {
                this.latitudeSender = position.coords.latitude;
                this.longitudeSender = position.coords.longitude;
                this.zoomSender = 8;
                this.getAddress(this.latitudeSender, this.longitudeSender, "SENDER");

                this.latitudeReceiver = position.coords.latitude;
                this.longitudeReceiver = position.coords.longitude;
                this.zoomReceiver = 8;
                this.getAddress(this.latitudeReceiver, this.longitudeReceiver, "RECEIVER");
            });
        }
    }

    markerDragEnd($event: google.maps.MouseEvent, map) {
        if (map === "SENDER") {
            this.latitudeSender = $event.latLng.lat();
            this.longitudeSender = $event.latLng.lng();
            this.getAddress(this.latitudeSender, this.longitudeSender, map);
        } else if (map === "RECEIVER") {
            this.latitudeReceiver = $event.latLng.lat();
            this.longitudeReceiver = $event.latLng.lng();
            this.getAddress(this.latitudeReceiver, this.longitudeReceiver, map);
        }

    }

    getPin(address_components: []) {
        let pin: any = address_components.find((item: any) => {
            return item.types.includes("postal_code");
        });
        return pin?.long_name ? pin?.long_name : null;
    }

    getAddress(latitude, longitude, map) {
        //this._ProgressBarService.show();
        this.geoCoder.geocode({ 'location': { lat: latitude, lng: longitude } }, (results, status) => {            
            if (status === 'OK') {
                if (results[0]) {
                    if (map === "SENDER") {
                        this.zoomSender = 12;
                        this.form.patchValue({
                            sender_address: results[0].formatted_address,
                            sender_latitude: latitude,
                            sender_longitude: longitude,
                            sender_pin: this.getPin(results[0].address_components)
                        });
                    } else if (map === "RECEIVER") {
                        this.zoomReceiver = 12;
                        this.form.patchValue({
                            receiver_address: results[0].formatted_address,
                            receiver_latitude: latitude,
                            receiver_longitude: longitude,
                            receiver_pin: this.getPin(results[0].address_components)
                        });
                    }
                    this.calculateDistance();
                } else {
                    this._ToastService.error('No results found');
                }
            } else {
                this._ToastService.error('Geocoder failed due to: ' + status);
            }
            //this._ProgressBarService.hide();
        });
    }

    calculateDistance() {
        const sender = new google.maps.LatLng(this.latitudeSender, this.longitudeSender);
        const receiver = new google.maps.LatLng(this.latitudeReceiver, this.longitudeReceiver);
        const service = new google.maps.DistanceMatrixService();
        service.getDistanceMatrix({
            origins: [sender],
            destinations: [receiver],
            travelMode: google.maps.TravelMode.TWO_WHEELER
        }, (response, status) => {
            if (status === 'OK' && response && response.rows[0]) {
                const distance = response.rows[0]?.elements[0]?.distance.value;
                const distanceToKM = distance / 1000;
                this.calculateAmount(null, distanceToKM);
            }
        });
    }

    calculateAmount(e = null, distance) {
        e && e.preventDefault();        
        this.form.patchValue({
            distance: distance,
            payable_amount: (distance * parseFloat(this.form.value.cost_per_km)).toFixed(2)
        });
    }

}
