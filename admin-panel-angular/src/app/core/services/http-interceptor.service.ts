import { Injectable } from '@angular/core';
import { HttpEvent, HttpInterceptor, HttpHandler, HttpRequest, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from "../../../environments/environment";
import { AuthService } from './auth.service';


@Injectable({
    providedIn: 'root'
})
export class HttpInterceptorService implements HttpInterceptor {
    constructor(private authService: AuthService) { }
    intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        const apiReq = req.clone({
            url: `${environment.apiUrl}/${req.url}`,
            headers: new HttpHeaders({
                'Authorization': 'Bearer ' + this.authService.getTokenId()
            })
        });
        return next.handle(apiReq);
    }
}
