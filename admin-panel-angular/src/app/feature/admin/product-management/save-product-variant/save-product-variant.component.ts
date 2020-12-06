import { Component, OnInit, Inject } from '@angular/core';
import { FormGroup, FormBuilder, Validators, FormArray } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { ProductVariantDTO } from 'src/app/shared/models/product-variant';
import { ProductDTO } from 'src/app/shared/models/product';
import { ProductService } from 'src/app/shared/services/api/product.service';
import { VariantPropertyOptions, FoodTypes } from 'src/app/app.config';
import { UserService } from 'src/app/shared/services/api/user.service';
import { VendorDTO } from 'src/app/shared/models/vendor';

interface DialogData {
    product: ProductDTO,
    productVariant: ProductVariantDTO
}

@Component({
    selector: 'app-save-product-variant',
    templateUrl: './save-product-variant.component.html',
    styleUrls: ['./save-product-variant.component.scss']
})
export class SaveProductVariantComponent implements OnInit {
    form: FormGroup;
    productVariant: ProductVariantDTO;
    filteredOptions: string[] = VariantPropertyOptions;
    foodTypes: any[] = FoodTypes;
    vendors: VendorDTO[] = [];
    constructor(
        public dialogRef: MatDialogRef<SaveProductVariantComponent>,
        @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
        private _formBuilder: FormBuilder,
        public _ProgressBarService: ProgressBarService,
        private _UploaderService: UploaderService,
        private _ToastService: ToastService,
        private _ProductService: ProductService,
        private _userService: UserService,
    ) {
        if (!(this.modalData.product && this.modalData.product.product_id)) {
            this.dialogRef.close(false);
        }
        this.getAllVendors();
        this.createForm();
        if (this.modalData.productVariant) {
            this.form.patchValue(this.modalData.productVariant);
        }
    }

    ngOnInit(): void {
    }

    getAllVendors(){
        this._ProgressBarService.show();
        this._userService.getAllVendors().subscribe((result: VendorDTO[]) => {
            this.vendors = result;
            this._ProgressBarService.hide();
        });
    }

    filterOptions(event) {
        if(event.target.value){
            const filterValue = event.target.value.toLowerCase();
            this.filteredOptions = VariantPropertyOptions.filter(option => option.toLowerCase().includes(filterValue));
        } else {
            this.filteredOptions = VariantPropertyOptions;
        }        
    }

    resetAutocomplete(){
        this.filteredOptions = VariantPropertyOptions;
    }

    createForm() {
        this.form = this._formBuilder.group({
            productid: [this.modalData.product.product_id, Validators.required],
            vendorid: [null, Validators.required],
            sku_name: ["", Validators.required],
            is_veg: [0, Validators.required],
            price: ["", Validators.required],
            no_of_unit: [0, [Validators.required, Validators.min(1)]],
            filenames: [[], Validators.required],
            variant: this._formBuilder.array([])
        });
        this.addProperty();
    }

    addProperty() {
        let variantArray = this.form.controls.variant as FormArray;
        let arraylen = variantArray.length;

        let formGroup: FormGroup = this._formBuilder.group({
            key: ['', [Validators.required, Validators.maxLength(20), Validators.pattern(/^[a-zA-Z_$][0-9a-zA-Z_$ ]*$/)]],
            value: ['', Validators.required]
        })

        variantArray.insert(arraylen, formGroup);
    }

    removeProperty(i) {
        let variantArray = this.form.controls.variant as FormArray;
        if (variantArray.length > 1) {
            variantArray.removeAt(i);
        }
    }

    variants(): FormArray {
        return this.form.get("variant") as FormArray
    }

    fileChanged(f) {
        let formDate = new FormData();
        formDate.append("filenames[]", f.target.files[0]);
        formDate.append("type", "product");
        this._ProgressBarService.show();
        this._UploaderService.upload(formDate).subscribe(result => {
            this._ProgressBarService.hide();
            if (result) {
                let filenames = this.form.get('filenames').value;
                filenames.push(result);
                this.form.patchValue({ filenames });
            }
        })
    }

    removeFile(file) {
        let filenames = this.form.get('filenames').value;
        const index = filenames.indexOf(file);
        if (index >= 0) {
            filenames.splice(index, 1);
        }
        this.form.patchValue({ filenames });
    }

    convertToVariantFormat(variants: []) {
        let variantObj = {};
        variants.forEach((variant: any) => {
            variantObj[variant.key] = variant.value
        });
        return variantObj;
    }

    submit(e) {
        e.preventDefault();
        if (this.form.valid) {
            const { productid, vendorid, filenames, no_of_unit, price, sku_name, is_veg } = this.form.value;

            let payload: ProductVariantDTO = {
                productid,
                vendorid,
                filenames,
                no_of_unit,
                price,
                sku_name, 
                is_veg,
                variant: this.convertToVariantFormat(this.form.value.variant)
            }
            this._ProgressBarService.show();
            /* if (this.modalData.user) {
                this._utilityService.addUpdatedBy(this.user);
                this._userService.updateUser(this.modalData.user.id, this.user).then(result => {
                    this._progressBarService.hide();
                    this._toastService.info("User updated successfully");
                    this.form.reset();
                    this.dialogRef.close(true);
                }, response => {
                    this._progressBarService.hide();
                    this._toastService.error(response?.message || "Unable to update user");
                });
            } else { */
            this._ProductService.addProductVariant(payload).subscribe((result: any) => {
                this._ProgressBarService.hide();
                if (result.code === 200) {
                    this._ToastService.info(result.message);
                    this.form.reset();
                    this.dialogRef.close(true);
                } else {
                    this._ToastService.error(result.message);
                }
            }, response => {
                this._ProgressBarService.hide();
                this._ToastService.error(response.message);
            });
            //}
        }
    }

}
