import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ToastService } from '../../../../core/services/toast.service';
import { ProgressBarService } from '../../../../core/services/progress-bar.service';
import { AuthService } from '../../../../core/services/auth.service';
import { PasswordPattern } from '../../../../app.config';

@Component({
    selector: 'app-change-password',
    templateUrl: './change-password.component.html',
    styleUrls: ['./change-password.component.scss']
})
export class ChangePasswordComponent implements OnInit {
    form: FormGroup;
    constructor(
        private formBuilder: FormBuilder,
        private _toastService: ToastService,
        public _progressBarService: ProgressBarService,
        private authService: AuthService
    ) { 
        this.form = this.formBuilder.group({
            password: ["", [Validators.required, Validators.pattern(PasswordPattern)]],
            confirm_password: ["", [Validators.required]]
        }, { validator: this.confirmedValidator('password', 'confirm_password') });
    }

    ngOnInit(): void {
    }

    confirmedValidator(controlName: string, matchingControlName: string){
        return (formGroup: FormGroup) => {
            const control = formGroup.controls[controlName];
            const matchingControl = formGroup.controls[matchingControlName];
            if (matchingControl.errors && !matchingControl.errors.confirmedValidator) {
                return;
            }
            if (control.value !== matchingControl.value) {
                matchingControl.setErrors({ confirmedValidator: true });
            } else {
                matchingControl.setErrors(null);
            }
        }
    }

    submit(event) {
        event.preventDefault();
        if (this.form.valid) {
            this._progressBarService.show();
            this.authService.changePassword(this.form.value.password, () => {
                this._toastService.error('Password changed successfully');
                this._progressBarService.hide();
            }, error => {
                this._progressBarService.hide();
                this._toastService.error(error || 'Unable to change your password. Try after sometime.');
            });
        }
    }

}
