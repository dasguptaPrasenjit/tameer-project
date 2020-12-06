import { Injectable } from '@angular/core';
import { AppConfig } from '../../models/app-config';
import { Observable } from 'rxjs/internal/Observable';
import { HttpClient } from '@angular/common/http';
import { resource } from 'src/app/app.config';

@Injectable({
    providedIn: 'root'
})
export class ConfigService {
    constructor(private http: HttpClient) { 
    }

    getAppConfig() {
        return new Observable<AppConfig[]>();
    }

    getNotifications() {
        return this.http.get(resource.NOTIFICATION);
    }

    readNotifications(id: number) {
        return this.http.put(resource.NOTIFICATION_OPEN, {id});
    }
}
