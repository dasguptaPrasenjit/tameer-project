import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { CarrierDTO } from 'src/app/shared/models/carrier';
import { PickupDTO } from 'src/app/shared/models/pickup';
import { CarrierService } from 'src/app/shared/services/api/carrier.service';
import { PickupService } from 'src/app/shared/services/api/pickup.service';

interface DialogData {
  pickup: PickupDTO
}
@Component({
  selector: 'app-assign-pickup',
  templateUrl: './assign-pickup.component.html',
  styleUrls: ['./assign-pickup.component.scss']
})
export class AssignPickupComponent implements OnInit {
  form: FormGroup;
  carriers: CarrierDTO[] = [];
  constructor(
    public dialogRef: MatDialogRef<AssignPickupComponent>,
    @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
    private _formBuilder: FormBuilder,
    private _PickupService: PickupService,
    private _CarrierService: CarrierService,
    private _ToastService: ToastService,
    public _ProgressBarService: ProgressBarService
  ) { 
    _CarrierService.getAvailableCarriers().subscribe((result: CarrierDTO[]) => {
      this.carriers = result;
    });        
  }

  ngOnInit(): void {
    this.form = this._formBuilder.group({
      id: [this.modalData.pickup.id, Validators.required],
      carrier_id: ["", Validators.required]
    });
  }

  submit(e) {
    e.preventDefault();
    if (this.form.valid) {
      const payload = this.form.value;
      this._ProgressBarService.show();
      this._PickupService.assignPickup(payload).subscribe((result: any) => {
        if (result.success) {
          this._ToastService.info(result.message);
          this.dialogRef.close(true);
        } else {
          this._ToastService.error(result.message);
        }
        this._ProgressBarService.hide();
      });
    }

  }

}
