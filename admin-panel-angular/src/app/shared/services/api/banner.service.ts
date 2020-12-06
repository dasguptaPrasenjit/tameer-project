import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { resource } from 'src/app/app.config';
import { BannerDTO } from '../../models/banner';

@Injectable({
  providedIn: 'root'
})
export class BannerService {

  constructor(private http: HttpClient) { }

  addBanner(payload: any) {
    return this.http.post(resource.BANNER_ADD, payload);
  }

  updateBanner(payload: any) {
    return this.http.post(resource.BANNER_UPDATE, payload);
  }

  deleteBanner(payload: any) {
    return this.http.post(resource.BANNER_DELETE, payload);
  }

  getAllBanners() {
    return this.http.post(resource.BANNER_ALL, {}).pipe(
      map((response: any) => {
        let list: BannerDTO[] = [];
        if (response.status === 200) {
          list = response.data;
        }
        return list;
      })
    );
  }
}
