import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserManagementRoutingModule } from './user-management-routing.module';
import { SharedModule } from '../../../shared/shared.module';
import { ListAdminUserComponent } from './admin-user/list-admin-user/list-admin-user.component';
import { SaveAdminUserComponent } from './admin-user/save-admin-user/save-admin-user.component';
import { UserManagementComponent } from './user-management.component';
import { ListCarrierUserComponent } from './carrier-user/list-carrier-user/list-carrier-user.component';
import { CarrierDocViewerComponent } from './carrier-user/carrier-doc-viewer/carrier-doc-viewer.component';
import { AllCarrierLocatorComponent } from './carrier-user/all-carrier-locator/all-carrier-locator.component';


@NgModule({
  declarations: [ListAdminUserComponent, SaveAdminUserComponent, UserManagementComponent, ListCarrierUserComponent, CarrierDocViewerComponent, AllCarrierLocatorComponent],
  imports: [
    CommonModule,
    UserManagementRoutingModule,
    SharedModule.forRoot()
  ]
})
export class UserManagementModule { }
