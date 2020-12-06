import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ProductDTO } from 'src/app/shared/models/product';
import { SaveProductVariantComponent } from '../save-product-variant/save-product-variant.component';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatTableDataSource } from '@angular/material/table';
import { ProductVariantDTO } from 'src/app/shared/models/product-variant';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';
import { ProductService } from 'src/app/shared/services/api/product.service';
import { ProductVariantDetailComponent } from '../product-variant-detail/product-variant-detail.component';
import { ConfirmComponent } from 'src/app/shared/components/confirm/confirm.component';
import { ToastService } from 'src/app/core/services/toast.service';

@Component({
    selector: 'app-product-variant-list',
    templateUrl: './product-variant-list.component.html',
    styleUrls: ['./product-variant-list.component.scss']
})
export class ProductVariantListComponent implements OnInit {
    @Input() product: ProductDTO;
    displayedColumns = ['sku', 'sku_name', 'detailed_product_images', 'is_veg', 'price', 'action'];
    pageSize = 10;
    pageSizeOptions: number[] = [5, 10, 25, 100];
    pageEvent: PageEvent;
    dataSource: MatTableDataSource<ProductVariantDTO>;
    @ViewChild(MatPaginator) paginator: MatPaginator;
    imageUrl: string = "";

    constructor(
        private _ProductService: ProductService,
        private _ProgressBarService: ProgressBarService,
        private dialog: MatDialog,
        public _UploaderService: UploaderService,
        private _ToastService: ToastService
    ) { }

    ngOnInit(): void {
        this.dataSource = new MatTableDataSource([]);
        if(this.product.product_image){
            this.imageUrl = this._UploaderService.getImage(this.product.product_image);
        }        
        this.getProductVariantById();
    }

    getProductVariantById(){
        this._ProgressBarService.show();
        this._ProductService.getProductVariantByProductId(this.product.product_id).subscribe(response => {
            if(response){
                this.dataSource.data = response;
                this.dataSource.paginator = this.paginator;
            }            
            this._ProgressBarService.hide();
        });
    }

    applyFilter(event) {
        const filterValue = (event.target as HTMLInputElement).value;
        this.dataSource.filter = filterValue.trim().toLowerCase();
        if (this.dataSource.paginator) {
            this.dataSource.paginator.firstPage();
        }
    }

    add() {
        const ref = this.dialog.open(SaveProductVariantComponent, {
            width: '600px',
            disableClose: true,
            data: { product: this.product }
        });
        ref.afterClosed().subscribe(res => {
            if (res) {
                this.getProductVariantById();
            }
        });
    }

    edit(productVariant) {
        this.dialog.open(ProductVariantDetailComponent, {
            width: '600px',
            disableClose: true,
            data: { productVariant }
        });
    }

    deleteProductVariant(product: ProductVariantDTO) {
        const ref = this.dialog.open(ConfirmComponent, {
            width: '600px',
            disableClose: true,
            data: { message: "Deleting this product. Click Ok to continue." }
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this._ProgressBarService.show();
                this._ProductService.deleteProductVariant({ "id": product.sku_id }).subscribe((result: any) => {
                    this._ToastService.info(result.message);
                    this.getProductVariantById();
                });
            }
        });
    }

}
