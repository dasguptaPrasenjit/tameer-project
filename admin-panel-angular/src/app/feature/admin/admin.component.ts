import { Component, OnInit } from '@angular/core';
import { MenuItem } from 'src/app/shared/models/menu-item';
import { UserDTO } from 'src/app/shared/models/user';
import { AuthService } from 'src/app/core/services/auth.service';

@Component({
    selector: 'app-admin',
    templateUrl: './admin.component.html',
    styleUrls: ['./admin.component.scss']
})
export class AdminComponent implements OnInit {
    menuItems: MenuItem[] = [
        {
            link: "/admin/dashboard",
            label: "Dashboard",
            enable: true,
            display: true
        },
        {
            link: "/admin/category",
            label: "Category & Sub Category",
            enable: true,
            display: true
        },
        {
            link: "/admin/product",
            label: "Product & Variants",
            enable: true,
            display: true
        },
        {
            link: "/admin/coupon",
            label: "Coupons",
            enable: true,
            display: true
        },
        {
            link: "/admin/order",
            label: "Orders",
            enable: true,
            display: true
        },
        {
            link: "/admin/pickup",
            label: "Pickups",
            enable: true,
            display: true
        },
        {
            link: "/admin/user",
            label: "User Management",
            enable: true,
            display: true
        },
        /* {
            link: "/admin/banner",
            label: "Banner",
            enable: true,
            display: true
        },  */
        /* {
            link: "/admin/account/change-password",
            label: "Change Password",
            enable: true,
            display: true
        } */
    ];
    user: UserDTO = null;
    profileUrl: string = "./assets/profile.png";
    constructor(
        private _AuthService: AuthService
    ) { }

    ngOnInit(): void {
        this.user = this._AuthService.getUser();
    }

}
