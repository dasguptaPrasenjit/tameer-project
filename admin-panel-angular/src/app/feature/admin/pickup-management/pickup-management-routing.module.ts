import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PickupManagementComponent } from './pickup-management.component';


const routes: Routes = [
  {
    path: "",
    component: PickupManagementComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PickupManagementRoutingModule { }
