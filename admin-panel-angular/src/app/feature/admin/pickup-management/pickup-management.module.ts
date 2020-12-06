import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PickupManagementRoutingModule } from './pickup-management-routing.module';
import { PickupManagementComponent } from './pickup-management.component';
import { PickupListComponent } from './pickup-list/pickup-list.component';
import { SavePickupComponent } from './save-pickup/save-pickup.component';
import { AssignPickupComponent } from './assign-pickup/assign-pickup.component';
import { SharedModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [PickupManagementComponent, PickupListComponent, SavePickupComponent, AssignPickupComponent],
  imports: [
    CommonModule,
    PickupManagementRoutingModule,
    SharedModule.forRoot()
  ]
})
export class PickupManagementModule { }
