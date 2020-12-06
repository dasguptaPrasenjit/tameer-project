import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../core/services/auth.service';

@Component({
    selector: 'app-menu',
    templateUrl: './menu.component.html',
    styleUrls: ['./menu.component.scss']
})
export class MenuComponent implements OnInit {
    user: any;
    constructor(public _authService: AuthService) { }

    ngOnInit(): void {
        this.user = this._authService.getUser();
    }
}
