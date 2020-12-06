import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { resource } from 'src/app/app.config';
import { map } from 'rxjs/operators';
import { CarrierDTO } from '../../models/carrier';

@Injectable({
  providedIn: 'root'
})
export class CarrierService {

  constructor(private http: HttpClient) { }

  getAllCarriers() {
    return this.http.post(resource.ALL_CARRIER, {}).pipe(
      map((response: any) => {
        let list: CarrierDTO[] = [];
        if (response.status === 200) {
          list = response.data;
        }
        return list;
      })
    );
  }

  getAvailableCarriers() {
    return this.http.post(resource.CARRIER_LIST, {}).pipe(
      map((response: any) => {
        let list: CarrierDTO[] = [];
        if (response.status === 200) {
          list = response.data;
        }
        return list;
      })
    );
  }

  getCarrierLocation(carrier_id: number){
    return this.http.get(resource.CARRIER_LOCATION(carrier_id));
  }

  deleteCarrier(payload){
    return this.http.post(resource.CARRIER_DELETE, payload);
  }

  restoreCarrier(payload){
    return this.http.post(resource.CARRIER_RESTORE, payload);
  }
}
