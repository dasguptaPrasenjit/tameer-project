import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { resource } from 'src/app/app.config';
import { map } from 'rxjs/operators';
import { PickupDTO } from '../../models/pickup';

@Injectable({
  providedIn: 'root'
})
export class PickupService {

  constructor(private http: HttpClient) { }

  getAllPickups() {
    return this.http.get(resource.PICKUP.PICKUP).pipe(
      map((response: any) => {
        let list: PickupDTO[] = [];
        if (response.success) {
          list = response.data;
        }
        return list;
      })
    );
  }

  addPickup(payload){
    return this.http.post(resource.PICKUP.PICKUP_CREATE, payload);
  }

  assignPickup(payload){
    return this.http.post(resource.PICKUP.PICKUP_ASSIGN, payload);
  }
}
