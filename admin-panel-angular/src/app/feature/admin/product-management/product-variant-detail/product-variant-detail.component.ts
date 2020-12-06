import { Component, OnInit, Inject } from '@angular/core';
import { ProductVariantDTO } from 'src/app/shared/models/product-variant';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { ProductService } from 'src/app/shared/services/api/product.service';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';

interface DialogData {
    productVariant: ProductVariantDTO
}

@Component({
    selector: 'app-product-variant-detail',
    templateUrl: './product-variant-detail.component.html',
    styleUrls: ['./product-variant-detail.component.scss']
})
export class ProductVariantDetailComponent implements OnInit {
    variantDetail: any;
    no_of_unit: number;
    constructor(
        public dialogRef: MatDialogRef<ProductVariantDetailComponent>,
        @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
        public _ProgressBarService: ProgressBarService,
        private _ToastService: ToastService,
        private _ProductService: ProductService,
        public _UploaderService: UploaderService
    ) { }

    ngOnInit(): void {
        this._ProgressBarService.show();
        this._ProductService.getProductVariantDetailBySKUId(this.modalData.productVariant.sku_id).subscribe((result: any) => {
            this.variantDetail = result;
            this._ProgressBarService.hide();
        })

    }

    keys(){
        return this.variantDetail ? Object.keys(this.variantDetail?.variant) : [];
    }

    submit(e){
        if(this.no_of_unit){
            this._ProgressBarService.show();
            this._ProductService.updateVariant(this.modalData.productVariant.sku_id, this.no_of_unit).subscribe((result: any) => {
                if(result){
                    this._ProgressBarService.hide();
                    this._ToastService.info("Unit updated successfully");
                    this.dialogRef.close(true);
                }
            }, response => {
                this._ProgressBarService.hide();
                this._ToastService.error(response.message);
            });
        }
    }

}
