import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { BannerManagementComponent } from './banner-management.component';


const routes: Routes = [
  {
    path: "",
    component: BannerManagementComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BannerManagementRoutingModule { }
