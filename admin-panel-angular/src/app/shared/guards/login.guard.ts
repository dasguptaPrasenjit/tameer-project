import { Injectable } from '@angular/core';
import { CanActivate, CanLoad, Route, UrlSegment, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { AuthService } from 'src/app/core/services/auth.service';
import { UserDTO } from '../models/user';

@Injectable({
    providedIn: 'root'
})
export class LoginGuard implements CanActivate, CanLoad {
    constructor(private _AuthService: AuthService, private router: Router) { }

    validate() {
        const user: UserDTO = this._AuthService.getUser();
        if (user) {
            this.router.navigate(['admin']);
            return false;
        } else {
            return true;
        }
    }

    canActivate(
        next: ActivatedRouteSnapshot,
        state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
        return this.validate();
    }
    canLoad(
        route: Route,
        segments: UrlSegment[]): Observable<boolean> | Promise<boolean> | boolean {
        return this.validate();
    }
}
