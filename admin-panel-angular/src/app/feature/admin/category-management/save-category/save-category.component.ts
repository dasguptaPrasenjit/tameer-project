import { Component, OnInit, Inject } from '@angular/core';
import { CategorySubCategoryDTO, CategoryDTO } from 'src/app/shared/models/category';
import { FormBuilder, Validators, FormGroup } from '@angular/forms';
import { CategoryService } from 'src/app/shared/services/api/category.service';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';

interface DialogData {
    parent: CategorySubCategoryDTO,
    category: CategorySubCategoryDTO
}
@Component({
    selector: 'app-save-category',
    templateUrl: './save-category.component.html',
    styleUrls: ['./save-category.component.scss']
})
export class SaveCategoryComponent implements OnInit {
    form: FormGroup;
    constructor(
        public dialogRef: MatDialogRef<SaveCategoryComponent>,
        @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
        private _formBuilder: FormBuilder,
        private _CategoryService: CategoryService,
        public _ProgressBarService: ProgressBarService,
        private _UploaderService: UploaderService,
        private _ToastService: ToastService
    ) {
        this.createForm();
        if (this.modalData?.category) {
            this.form.patchValue({
                name: this.modalData.category.categoryname,
                description: this.modalData.category.description,
                parentid: this.modalData.category.parent_id,
                category_image: this.modalData.category.category_image,
                slug: this.modalData.category.slug,
                meta_title: this.modalData.category.meta_title,
                meta_description: this.modalData.category.meta_description,
                meta_keywords: this.modalData.category.meta_keywords,
            });
        }
    }

    ngOnInit(): void {
    }

    createForm() {
        this.form = this._formBuilder.group({
            name: ["", Validators.required],
            description: ["", Validators.required],
            parentid: [this.modalData?.parent ? this.modalData?.parent?.id : ""],
            slug: ["", Validators.required],
            meta_title: ["", Validators.required],
            meta_description: ["", Validators.required],
            meta_keywords: ["", Validators.required],
            category_image: ["", Validators.required]
        });
    }

    fileChanged(f) {
        let formDate = new FormData();
        formDate.append("filenames[]", f.target.files[0]);
        formDate.append("type", "category");
        this._ProgressBarService.show();
        this._UploaderService.upload(formDate).subscribe(result => {
            this._ProgressBarService.hide();
            if (result) {
                this.form.patchValue({ category_image: result });
            }
        })
    }

    removeFile() {
        this.form.patchValue({ category_image: "" });
    }

    submit(e) {
        e.preventDefault();
        if (this.form.valid) {
            this._ProgressBarService.show();
            const payload: CategoryDTO = this.form.value;            
            if (this.modalData?.category) {
                payload.id = this.modalData.category.id;
                this._CategoryService.updateCategory(payload).subscribe((result: any) => {
                    this._ProgressBarService.hide();
                    if (result.status === 200) {
                        this._ToastService.info(result.message);
                        this.dialogRef.close(true);
                        this.form.reset();
                    } else {
                        this._ToastService.error(result.message);
                    }
                });
            } else {                
                this._CategoryService.addCategory(payload).subscribe((result: any) => {
                    this._ProgressBarService.hide();
                    if (result.status === 200) {
                        this._ToastService.info(result.message);
                        this.dialogRef.close(true);
                        this.form.reset();
                    } else {
                        this._ToastService.error(result.message);
                    }
                });
            }
        }
    }

}
