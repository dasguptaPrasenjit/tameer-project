import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AdminComponent } from './admin.component';
import { CouponComponent } from '../admin/coupon/coupon.component';

const routes: Routes = [
    {
        path: "",
        component: AdminComponent,
        children: [
            {
                path: "",
                redirectTo: "dashboard",
            },
            {
                path: "dashboard",
                loadChildren: () => import('./dashboard/dashboard.module').then(m => m.DashboardModule)
            },
            {
                path: "user",
                loadChildren: () => import('./user-management/user-management.module').then(m => m.UserManagementModule)
            },
            {
                path: "category",
                loadChildren: () => import('./category-management/category-management.module').then(m => m.CategoryManagementModule)
            },
            {
                path: "product",
                loadChildren: () => import('./product-management/product-management.module').then(m => m.ProductManagementModule)
            },
            {
                path: "banner",
                loadChildren: () => import('./banner-management/banner-management.module').then(m => m.BannerManagementModule)
            },
            {
                path: "order",
                loadChildren: () => import('./order-management/order-management.module').then(m => m.OrderManagementModule)
            },
            {
                path: "account",
                loadChildren: () => import('./account/account.module').then(m => m.AccountModule)
            },
            {
              path: "coupon",
              component: CouponComponent
            },
            {
                path: "pickup",
                loadChildren: () => import('./pickup-management/pickup-management.module').then(m => m.PickupManagementModule)
            },
        ]
    }
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class AdminRoutingModule { }
