import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ProductDTO } from '../../models/product';
import { resource } from 'src/app/app.config';
import { map } from 'rxjs/internal/operators/map';
import { ProductVariantDTO } from '../../models/product-variant';

@Injectable({
    providedIn: 'root'
})
export class ProductService {

    constructor(private http: HttpClient) { }
    getProductsByCategoryId(id: number) {
        return this.http.post(resource.PRODUCTS, { categoryid: id }).pipe(
            map((response: any) => {
                if (response.code === 200) {
                    return response.data as ProductDTO[];
                }
                return [] as ProductDTO[];
            })
        );
    }

    getProductVariantByProductId(id: string) {
        return this.http.post(resource.PRODUCT_DETAILS, { productid: id }).pipe(
            map((response: any) => {
                if (response) {
                    return response.data.product_details as ProductVariantDTO[];
                }
                return [] as ProductVariantDTO[];
            })
        );
    }

    getProductVariantDetailBySKUId(id: string) {
        return this.http.post(resource.PRODUCT_VARIANT_DETAIL, { skuid: id }).pipe(
            map((response: any) => {
                if (response && response.data && response.data.length > 0) {
                    return response.data[0];
                }
                return [];
            })
        );
    }

    addMasterProduct(payload: any) {
        return this.http.post(resource.PRODUCT_ADD, payload);
    }

    updateMasterProduct(payload: any) {
        return this.http.post(resource.PRODUCT_UPDATE, payload);
    }

    addProductVariant(payload: ProductVariantDTO) {
        return this.http.post(resource.PRODUCT_VARIANT_ADD, payload);
    }

    updateVariant(skuId: string, unit: number) {
        return this.http.post(resource.PRODUCT_VARIANT_UPDATE, { "skuid": skuId, "no_of_unit": unit });
    }

    deleteProductVariant(payload: any) {
        return this.http.post(resource.PRODUCT_VARIANT_DELETE, payload);
    }
}
