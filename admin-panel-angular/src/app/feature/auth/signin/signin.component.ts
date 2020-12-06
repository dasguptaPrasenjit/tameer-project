import { Component } from '@angular/core';
import { FormGroup, FormControl } from '@angular/forms';
import { AuthService } from '../../../core/services/auth.service';
import { ToastService } from '../../../core/services/toast.service';
import { ProgressBarService } from '../../../core/services/progress-bar.service';
import { UserService } from 'src/app/shared/services/api/user.service';

@Component({
    selector: 'app-signin',
    templateUrl: './signin.component.html',
    styleUrls: ['./signin.component.scss']
})
export class SigninComponent {
    
    constructor(
        private _authService: AuthService,
        private _userService: UserService,
        private _toastService: ToastService,
        public _progressBarService: ProgressBarService
    ) {
    }

    form: FormGroup = new FormGroup({
        email: new FormControl(''),
        password: new FormControl(''),
    });

    submit(event) {
        event.preventDefault();
        if (this.form.valid) {
            this._progressBarService.show();
            this._authService.signIn(this.form.value).subscribe(() => {
                this._progressBarService.hide();
            }, error => {
                this._progressBarService.hide();
                error && this._toastService.error(error.error.error);
            });
        }
    }
}
