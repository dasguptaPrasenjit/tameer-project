import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AccountRoutingModule } from './account-routing.module';
import { AccountComponent } from './account.component';
import { SharedModule } from '../../../shared/shared.module';
import { AccountDashboardComponent } from './account-dashboard/account-dashboard.component';
import { ChangePasswordComponent } from './change-password/change-password.component';


@NgModule({
    declarations: [AccountComponent, AccountDashboardComponent, ChangePasswordComponent],
    imports: [
        CommonModule,
        AccountRoutingModule,
        SharedModule.forRoot()
    ]
})
export class AccountModule { }
