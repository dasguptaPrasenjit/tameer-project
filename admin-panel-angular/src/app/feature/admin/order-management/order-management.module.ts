import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrderManagementRoutingModule } from './order-management-routing.module';
import { OrderManagementComponent } from './order-management.component';
import { SharedModule } from 'src/app/shared/shared.module';
import { OrderListComponent } from './order-list/order-list.component';
import { AssignOrderComponent } from './assign-order/assign-order.component';
import { LocateOrderComponent } from './locate-order/locate-order.component';

@NgModule({
  declarations: [OrderManagementComponent, OrderListComponent, AssignOrderComponent, LocateOrderComponent],
  imports: [
    CommonModule,    
    OrderManagementRoutingModule,
    SharedModule.forRoot()
  ]
})
export class OrderManagementModule { }
