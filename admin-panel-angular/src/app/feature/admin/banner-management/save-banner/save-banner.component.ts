import { Component, OnInit, Inject } from '@angular/core';
import { BannerDTO } from 'src/app/shared/models/banner';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { BannerService } from 'src/app/shared/services/api/banner.service';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';
import { ToastService } from 'src/app/core/services/toast.service';


interface DialogData {
  banner: BannerDTO
}

@Component({
  selector: 'app-save-banner',
  templateUrl: './save-banner.component.html',
  styleUrls: ['./save-banner.component.scss']
})
export class SaveBannerComponent implements OnInit {
  form: FormGroup;
  constructor(
    public dialogRef: MatDialogRef<SaveBannerComponent>,
    @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
    private _formBuilder: FormBuilder,
    private _BannerService: BannerService,
    public _ProgressBarService: ProgressBarService,
    private _UploaderService: UploaderService,
    private _ToastService: ToastService
  ) {
    this.createForm();
  }

  ngOnInit(): void {
  }

  createForm() {
    this.form = this._formBuilder.group({
      name: ["", Validators.required],
      image: ["", Validators.required]
    });
  }

  fileChanged(f) {
    let formDate = new FormData();
    formDate.append("filenames[]", f.target.files[0]);
    formDate.append("type", "banner");
    this._ProgressBarService.show();
    this._UploaderService.upload(formDate).subscribe(result => {
      this._ProgressBarService.hide();
      if (result) {
        this.form.patchValue({ image: result });
      }
    })
  }

  removeFile() {
    this.form.patchValue({ image: "" });
  }

  submit(e) {
    e.preventDefault();
    if (this.form.valid) {
        const payload: BannerDTO = this.form.value;
        this._ProgressBarService.show();
        this._BannerService.addBanner(payload).subscribe((result: any) => {
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
