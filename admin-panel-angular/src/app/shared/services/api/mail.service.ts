import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
    providedIn: 'root'
})
export class MailService {

    constructor(private http: HttpClient) { }

    sendMail(email: string, subject: string, message: string) {
        return this.http.post('mail', { email, subject, message });
    }
}
