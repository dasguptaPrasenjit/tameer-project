import { Injectable } from '@angular/core';
import { UserDTO } from '../../models/user';
import { map } from 'rxjs/internal/operators/map';
import { resource } from '../../../app.config';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { UserVendorDTO } from '../../models/user';
import { VendorDTO } from '../../models/vendor';

@Injectable({
    providedIn: 'root'
})
export class UserService {
    constructor(
        private http: HttpClient
    ) { }

    register(payload) {
        return this.http.post(resource.REGISTER, payload);
    }

    signIn(payload) {
        return this.http.post(resource.LOGIN, payload);
    }

    getAllVendors() {
        return this.http.post(resource.VENDORS, {}).pipe(
            map((response: any) => {
                if (response.status === 200) {
                    return response.data as VendorDTO[];
                } else {
                    return [] as VendorDTO[];
                }
            })
        );
    }

    removeVendor(payload) {
        return this.http.post(resource.DELETE_VENDOR, payload);
    }

    addUser(payload: UserDTO) {
        return new Observable<UserDTO[]>();
    }

    updateUser(id: number, payload: UserVendorDTO) {
        return this.http.post(resource.VENDOR_UPDATE + "/" + id, payload);
    }

    updateUserProp(id: number, payload) {
        return new Observable<UserDTO[]>();
    }

    getEmailBySignupToken(token: string) {
        return new Observable<UserDTO[]>();
    }

    getUserByEmail(email: string) {
        return new Observable<UserDTO[]>();
    }

    getVendorByCategoryId(payload){
      return this.http.post(resource.VENDORBYCATEGORYID, payload);
    }
}
