import { Injectable } from '@angular/core';
import { UserService } from '../api/user.service';
import { debounceTime, take, switchMap, map } from 'rxjs/operators';
import { of, Observable } from 'rxjs';
import { AsyncValidatorFn, AbstractControl } from '@angular/forms';
import { ProgressBarService } from '../../../core/services/progress-bar.service';

@Injectable({
    providedIn: 'root'
})
export class EmailValidatorService {

    constructor(
        private _userService: UserService,
        private _progressBarService: ProgressBarService
    ) { }

    existingEmailValidator(initialEmail: string = ""): AsyncValidatorFn {
        return (control: AbstractControl): | Promise<{ [key: string]: any } | null> | Observable<{ [key: string]: any } | null> => {
            if (control.value === "") {
                return of(null);
            } else if (control.value === initialEmail) {
                return of(null);
            } else {
                return control.valueChanges.pipe(
                    debounceTime(500),
                    take(1),
                    switchMap(() => {
                        this._progressBarService.show();
                        return this._userService
                            .getUserByEmail(control.value)
                            .pipe(
                                map(user => {
                                    this._progressBarService.hide();
                                    return user ? { existingEmail: "Email address already in use" } : null
                                })
                            )
                    })
                );
            }
        };
    }
}
