import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ProductManagementRoutingModule } from './product-management-routing.module';
import { ProductManagementComponent } from './product-management.component';
import { SharedModule } from 'src/app/shared/shared.module';
import { ProductListComponent } from './product-list/product-list.component';
import { SaveProductComponent } from './save-product/save-product.component';
import { ProductVariantListComponent } from './product-variant-list/product-variant-list.component';
import { SaveProductVariantComponent } from './save-product-variant/save-product-variant.component';
import { ProductVariantDetailComponent } from './product-variant-detail/product-variant-detail.component';


@NgModule({
    declarations: [ProductManagementComponent, ProductListComponent, SaveProductComponent, ProductVariantListComponent, SaveProductVariantComponent, ProductVariantDetailComponent],
    imports: [
        CommonModule,
        ProductManagementRoutingModule,
        SharedModule.forRoot()
    ]
})
export class ProductManagementModule { }
