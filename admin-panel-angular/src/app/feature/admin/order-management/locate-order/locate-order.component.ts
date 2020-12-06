import { Component, OnInit, Inject, OnDestroy } from '@angular/core';
import { OrderService } from 'src/app/shared/services/api/order.service';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { OrderDTO } from 'src/app/shared/models/order';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';

interface DialogData {
  order: OrderDTO
}

@UntilDestroy()
@Component({
  selector: 'app-locate-order',
  templateUrl: './locate-order.component.html',
  styleUrls: ['./locate-order.component.scss']
})
export class LocateOrderComponent implements OnInit, OnDestroy {
  lat = 22.5726;
  lng = 88.3639;
  timer = null;
  constructor(
    private _OrderService: OrderService,
    @Inject(MAT_DIALOG_DATA) public modalData: DialogData
  ) { }

  ngOnInit(): void {
    this.timer = setInterval(() => {
      this._OrderService.getLocation({transactionid: this.modalData.order.transaction_id}).pipe(untilDestroyed(this)).subscribe((result: any) => {
        if(result?.data?.latitude && result?.data?.longitude){
          this.lat = parseFloat(result?.data?.latitude);
          this.lng = parseFloat(result?.data?.longitude);
        }        
      });      
    }, 5000);    
  }

  ngOnDestroy(){
    clearInterval(this.timer);
  }

}
