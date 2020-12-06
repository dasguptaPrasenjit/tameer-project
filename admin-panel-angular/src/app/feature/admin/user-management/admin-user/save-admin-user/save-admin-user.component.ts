import { Component, OnInit, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { FormGroup, Validators, FormBuilder, FormControl } from '@angular/forms';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { UserDTO, UserVendorDTO } from "src/app/shared/models/user";
import { GenderOptions } from 'src/app/app.config';
import { ToastService } from 'src/app/core/services/toast.service';
import { UserService } from 'src/app/shared/services/api/user.service';
import { UtilityService } from 'src/app/shared/services/utility.service';
import { EmailValidatorService } from 'src/app/shared/services/validators/email-validator.service';
import { CategoryService } from 'src/app/shared/services/api/category.service';
import { CategoryDTO } from 'src/app/shared/models/category';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';

export interface DialogData {
    user: UserVendorDTO
}

@Component({
    selector: 'app-save-admin-user',
    templateUrl: './save-admin-user.component.html',
    styleUrls: ['./save-admin-user.component.scss']
})
export class SaveAdminUserComponent implements OnInit {
    user: UserVendorDTO;
    form: FormGroup;
    error: object = null;
    genderOptions = GenderOptions;
    categories: CategoryDTO[] = [];
    constructor(
        private fb: FormBuilder,
        public progressBarService: ProgressBarService,
        public dialogRef: MatDialogRef<SaveAdminUserComponent>,
        @Inject(MAT_DIALOG_DATA) public userToEdit: DialogData,
        private _toastService: ToastService,
        private _userService: UserService,
        private _utilityService: UtilityService,
        private _emailValidatorService: EmailValidatorService,
        private _CategoryService: CategoryService,
        private _UploaderService: UploaderService,
    ) {
        this.getParentCategories();
        this.createForm();
        if (this.userToEdit) {
            let user = this.userToEdit.user;
            if(user.mobile_number){
                user.mobile_number = user.mobile_number.substring(3); //remove +91
            }            
            this.form.patchValue(user);
        }
    }

    createForm() {
        this.form = this.fb.group({
            name: ["", Validators.required],
            mobile_number: ["", Validators.required],
            shop_name: ["", Validators.required],
            category_id: ["", Validators.required],
            address: ["", Validators.required],
            city: ["", Validators.required],
            state: ["", Validators.required],
            zip: ["", Validators.required],
            vendor_image: ["", Validators.required]
        });

        if (!this.userToEdit) {
            this.form.addControl('email', new FormControl("", [Validators.required, Validators.email]));
            this.form.addControl('password', new FormControl("", Validators.required));
            this.form.addControl('confirm_password', new FormControl("", Validators.required));
        }
    }

    ngOnInit(): void {

    }

    getParentCategories(){
        this.progressBarService.show();
        this._CategoryService.getParentCategories().subscribe((result: CategoryDTO[]) => {
            this.categories = result;
            this.progressBarService.hide();
        });
    }

    fileChanged(f) {
        let formDate = new FormData();
        formDate.append("filenames[]", f.target.files[0]);
        formDate.append("type", "vendor");
        this.progressBarService.show();
        this._UploaderService.upload(formDate).subscribe(result => {
            this.progressBarService.hide();
            if (result) {
                this.form.patchValue({ vendor_image: result });
            }
        })
    }

    removeFile() {
        this.form.patchValue({ vendor_image: "" });
    }

    submit(e) {
        e.preventDefault();
        if (this.form.valid) {
            this.user = this.form.value;
            this.progressBarService.show();
            if (this.userToEdit) {
                this._userService.updateUser(this.userToEdit.user.user_id, this.user).subscribe((result: any) => {
                    this.progressBarService.hide();
                    if (result.error) {
                        this._toastService.info(result.message);
                    } else {
                        this.form.reset();
                        this.dialogRef.close(true);
                        this._toastService.info("User updated successfully");
                    }
                }, response => {
                    this.progressBarService.hide();
                    this._toastService.error(response?.message || "Unable to update user");
                });
            } else {
                this._userService.register(this.user).subscribe((result: any) => {
                    this.progressBarService.hide();
                    if (result.error) {
                        this._toastService.info(result.message);
                    } else {
                        this.form.reset();
                        this.dialogRef.close(true);
                        this._toastService.info("User created successfully");
                    }
                }, response => {
                    this.progressBarService.hide();
                    this._toastService.error(response?.message || "Unable to create user");
                });
            }
        }
    }

    reset() {
        this.createForm();
        this.form.markAsPristine();
    }
}