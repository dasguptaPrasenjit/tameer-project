import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { resource } from 'src/app/app.config';

@Injectable({
  providedIn: 'root'
})
export class CouponService {

  constructor(private http: HttpClient) { }

  getAllCoupon(){
    return this.http.post(resource.COUPON.GETALLCOUPON, {});
  }

  saveCoupon(payload){
    return this.http.post(resource.COUPON.SAVECOUPON, payload);
  }

  deleteCoupon(payload){
    return this.http.post(resource.COUPON.DELETECOUPON, payload);
  }
}
