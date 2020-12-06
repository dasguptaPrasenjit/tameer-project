import { Component, OnInit, Inject } from '@angular/core';
import { OrderDTO } from 'src/app/shared/models/order';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { OrderService } from 'src/app/shared/services/api/order.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { CarrierService } from 'src/app/shared/services/api/carrier.service';
import { CarrierDTO } from 'src/app/shared/models/carrier';
import { FormControl, FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';

interface DialogData {
  order: OrderDTO
}

@Component({
  selector: 'app-assign-order',
  templateUrl: './assign-order.component.html',
  styleUrls: ['./assign-order.component.scss']
})
export class AssignOrderComponent implements OnInit {
  form: FormGroup;
  carriers: CarrierDTO[] = [];
  constructor(
    public dialogRef: MatDialogRef<AssignOrderComponent>,
    @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
    private _formBuilder: FormBuilder,
    private _OrderService: OrderService,
    private _CarrierService: CarrierService,
    private _ToastService: ToastService,
    public _ProgressBarService: ProgressBarService
  ) {
    this.form = this._formBuilder.group({
      orderid: [this.modalData.order.order_id, Validators.required],
      carrierid: ["", Validators.required]
    });
    _CarrierService.getAvailableCarriers().subscribe((result: CarrierDTO[]) => {
      this.carriers = result;
    });
  }

  ngOnInit(): void {
  }

  submit(e) {
    e.preventDefault();
    if (this.form.valid) {
      const payload = this.form.value;
      this._ProgressBarService.show();
      this._OrderService.assignOrder(payload).subscribe((result: any) => {
        if (result.code === 200) {
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
