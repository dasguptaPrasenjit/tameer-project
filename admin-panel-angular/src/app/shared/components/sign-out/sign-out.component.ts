import { Component } from '@angular/core';
import { AuthService } from '../../../core/services/auth.service';
import { Router } from '@angular/router';

@Component({
    selector: 'app-sign-out',
    templateUrl: './sign-out.component.html',
    styleUrls: ['./sign-out.component.scss']
})
export class SignOutComponent {

    constructor(
        private authService: AuthService,
        public router: Router
    ) { }

    signOut() {
        this.authService.signOut();
    }

}
