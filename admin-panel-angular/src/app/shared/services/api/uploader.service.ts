import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { resource } from 'src/app/app.config';
import { map } from 'rxjs/internal/operators/map';
import { environment } from "../../../../environments/environment";
@Injectable({
    providedIn: 'root'
})
export class UploaderService {
    constructor(
        private http: HttpClient
    ) { }

    upload(formDate: FormData) {
        return this.http.post(resource.UPLOAD, formDate).pipe(
            map((response: any) => {
                if (response.code === 200) {
                    return response.data[0];
                } else {
                    return null;
                }
            })
        );
    }

    getImage(path){
        return path ? environment.assetUrl + path : "";
    }
}
