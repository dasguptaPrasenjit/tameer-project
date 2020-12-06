import { Component, OnInit, Inject } from '@angular/core';
import { FormGroup, Validators, FormBuilder } from '@angular/forms';
import { CategoryService } from 'src/app/shared/services/api/category.service';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { CategorySubCategoryDTO } from 'src/app/shared/models/category';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { MatDialogRef, MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ProductService } from 'src/app/shared/services/api/product.service';
import { SaveProductVariantComponent } from '../save-product-variant/save-product-variant.component';
import { ProductDTO } from 'src/app/shared/models/product';

interface DialogData {
    product: ProductDTO
}
@Component({
    selector: 'app-save-product',
    templateUrl: './save-product.component.html',
    styleUrls: ['./save-product.component.scss']
})
export class SaveProductComponent implements OnInit {
    form: FormGroup;
    caterories: CategorySubCategoryDTO[] = [];
    constructor(
        public dialogRef: MatDialogRef<SaveProductComponent>,
        @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
        private dialog: MatDialog,
        private _formBuilder: FormBuilder,
        private _CategoryService: CategoryService,
        private _ProductService: ProductService,
        public _ProgressBarService: ProgressBarService,
        private _UploaderService: UploaderService,
        private _ToastService: ToastService
    ) {
        this.createForm();
        if (this.modalData?.product) {
            this.form.patchValue({
                name: this.modalData.product.product_name,
                categoryid: this.modalData.product.category_id,
                imageurl: [this.modalData.product.product_image]
            });
        }
    }

    ngOnInit(): void {
        this._ProgressBarService.show();
        this._CategoryService.getNestedCategories().subscribe((result: CategorySubCategoryDTO[]) => {
            this.caterories = result;
            this._ProgressBarService.hide();
        });
    }

    createForm() {
        this.form = this._formBuilder.group({
            name: ["", Validators.required],
            categoryid: [null, Validators.required],
            vendorid: [null],
            imageurl: [[], Validators.required]
        });
    }

    fileChanged(f) {
        let formDate = new FormData();
        formDate.append("filenames[]", f.target.files[0]);
        formDate.append("type", "product");
        this._ProgressBarService.show();
        this._UploaderService.upload(formDate).subscribe(result => {
            this._ProgressBarService.hide();
            if (result) {
                let imageurl = this.form.get('imageurl').value;
                imageurl.push(result);
                this.form.patchValue({ imageurl });
            }
        })
    }

    removeFile(file) {
        let filenames = this.form.get('imageurl').value;
        const index = filenames.indexOf(file);
        if (index >= 0) {
            filenames.splice(index, 1);
        }
        this.form.patchValue({ filenames });
    }

    submit(e) {
        e.preventDefault();
        if (this.form.valid) {
            const payload = this.form.value;
            this._ProgressBarService.show();
            if (this.modalData?.product) {
                payload.id = this.modalData.product.product_id;
                this._ProductService.updateMasterProduct(payload).subscribe((result: any) => {
                    this._ProgressBarService.hide();
                    this._ToastService.error(result.message);
                    this.form.reset();
                    this.dialogRef.close(true);
                }, response => {
                    this._ProgressBarService.hide();
                    this._ToastService.error(response.message);
                });
            } else {
                this._ProductService.addMasterProduct(payload).subscribe((result: any) => {
                    this._ProgressBarService.hide();
                    if (result.code === 200) {
                        this._ToastService.info(result.message);
                        this.form.reset();
                        this.dialogRef.close(true);
                        setTimeout(() => {
                            this.dialog.open(SaveProductVariantComponent, {
                                width: '600px',
                                disableClose: true,
                                data: {
                                    product: {
                                        product_name: result.data.name,
                                        product_id: result.data.id
                                    }
                                }
                            });
                        });
                    } else {
                        this._ToastService.error(result.message);
                    }
                }, response => {
                    this._ProgressBarService.hide();
                    this._ToastService.error(response.message);
                });
            }
        }
    }

}
