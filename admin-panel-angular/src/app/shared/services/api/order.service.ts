import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { resource } from 'src/app/app.config';
import { map } from 'rxjs/operators';
import { OrderDTO } from '../../models/order';

@Injectable({
  providedIn: 'root'
})
export class OrderService {

  constructor(private http: HttpClient) { }

  getAllOrders(value: string) {
    const api = resource.ORDER[value];
    return this.http.post(api, {}).pipe(
      map((response: any) => {
        let list: OrderDTO[] = [];
        if (response.code === 200) {
          list = response.data;
        }
        return list;
      })
    );
  }

  exportAllOrders(value: string) {
    const api = resource.ORDER[value];
    return this.http.post(api, {export: 1}, {
      responseType: 'blob'
    });
  }

  acceptOrder(payload: any) {
    return this.http.post(resource.ORDER_ACCEPT, payload);
  }

  assignOrder(payload: any) {
    return this.http.post(resource.ORDER_ASSIGN, payload);
  }

  getLocation(payload: any){
    return this.http.post(resource.ORDER_LOCATION, payload);
  }
}
