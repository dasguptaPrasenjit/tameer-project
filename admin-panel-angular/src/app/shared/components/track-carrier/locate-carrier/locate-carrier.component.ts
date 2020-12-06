import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { CarrierService } from 'src/app/shared/services/api/carrier.service';

interface DialogData {
  carrierId: number,
  label: string
}

@UntilDestroy()
@Component({
  selector: 'app-locate-carrier',
  templateUrl: './locate-carrier.component.html',
  styleUrls: ['./locate-carrier.component.scss']
})
export class LocateCarrierComponent {
  timer = null;
  lat: number = 22.5726;
  lng: number = 88.3639;
  constructor(
    @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
    private _CarrierService: CarrierService
  ) { 
    this.timer = setInterval(() => {
      this._CarrierService.getCarrierLocation(this.modalData.carrierId).pipe(untilDestroyed(this)).subscribe((result: any) => {
        if (result?.data?.latitude && result?.data?.longitude) {
          this.lat = parseFloat(result?.data?.latitude);
          this.lng = parseFloat(result?.data?.longitude);
        }
      });
    }, 5000);
  }

  ngOnDestroy() {
    clearInterval(this.timer);
  }

}
