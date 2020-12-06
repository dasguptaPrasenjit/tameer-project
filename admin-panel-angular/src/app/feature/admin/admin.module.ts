import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AdminRoutingModule } from './admin-routing.module';
import { AdminComponent } from './admin.component';
import { SharedModule } from 'src/app/shared/shared.module';
import { CouponComponent, DialogAddCoupon } from './coupon/coupon.component';

@NgModule({
  declarations: [AdminComponent, CouponComponent, DialogAddCoupon],
  imports: [
    CommonModule,
    AdminRoutingModule,
    SharedModule.forRoot()
  ],
  entryComponents:[DialogAddCoupon],
})
export class AdminModule { }
