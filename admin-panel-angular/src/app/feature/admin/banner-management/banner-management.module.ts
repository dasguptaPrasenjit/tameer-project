import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BannerManagementRoutingModule } from './banner-management-routing.module';
import { BannerManagementComponent } from './banner-management.component';
import { SaveBannerComponent } from './save-banner/save-banner.component';
import { BannerListComponent } from './banner-list/banner-list.component';
import { SharedModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [BannerManagementComponent, SaveBannerComponent, BannerListComponent],
  imports: [
    CommonModule,
    BannerManagementRoutingModule,
    SharedModule.forRoot()
  ]
})
export class BannerManagementModule { }
