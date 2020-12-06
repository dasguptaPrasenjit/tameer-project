import { Injectable, NgZone } from '@angular/core';
import { Router } from '@angular/router';
import { UserDTO } from '../../shared/models/user';
import { map } from 'rxjs/internal/operators/map';
import { HttpClient } from '@angular/common/http';
import { resource } from 'src/app/app.config';

@Injectable({
    providedIn: 'root'
})
export class AuthService {
    constructor(
        private router: Router,
        private ngZone: NgZone,
        private http: HttpClient
    ) {
    }

    private clearSession() {
        sessionStorage.setItem('user', null);
        sessionStorage.setItem('token', null);
    }

    private setSession(authData) {
        sessionStorage.setItem('user', JSON.stringify(authData.data));
        sessionStorage.setItem('token', authData.success.token);
    }

    private getUserByAuthId(id: string) {

    }

    getTokenId() {
        let tokenData = sessionStorage.getItem('token');
        if (tokenData) {
            return tokenData;
        }
        return "";
    }

    getUser() {
        let user = sessionStorage.getItem('user');
        if (user) {
            let userData: UserDTO = JSON.parse(user);
            return userData;
        }
        return null;
    }

    signIn(payload) {
        return this.http.post(resource.LOGIN, payload).pipe(
            map((authData: any) => {
                if (authData.status === 200 && authData.success && this.isAdmin(authData)) {
                    this.setSession(authData);
                    this.ngZone.run(() => {
                        this.router.navigate(['admin']);
                    });
                }
            })
        );
    }

    isAdmin(authData) : boolean{
        if(authData.role && authData.role.length > 0){
            return authData.role.find(role => role.role_name === "admin");
        }
        return false;
    }

    changePassword(password, onSuccess, onError) {

    }

    signOut() {
        this.clearSession();
        this.ngZone.run(() => {
            this.router.navigate(['auth']);
        });
    }
}