import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AccountComponent } from './account.component';
import { AccountDashboardComponent } from './account-dashboard/account-dashboard.component';
import { ChangePasswordComponent } from './change-password/change-password.component';


const routes: Routes = [
    {
        path: "",
        redirectTo: "dashboard"
    },
    {
        path: "dashboard",
        component: AccountDashboardComponent
    },
    {
        path: "change-password",
        component: ChangePasswordComponent
    }
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class AccountRoutingModule { }
