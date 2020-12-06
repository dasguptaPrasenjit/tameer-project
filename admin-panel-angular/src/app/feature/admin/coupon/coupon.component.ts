import { Component, OnInit, Inject } from '@angular/core';
import { CouponService } from './coupon.service'
import { CategoryService } from '../../../shared/services/api/category.service';
import { UserService } from '../../../shared/services/api/user.service';
import { UploaderService } from '../../../shared/services/api/uploader.service';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { MatDialog, MatDialogModule, MAT_DIALOG_DATA, MatDialogConfig, MatDialogRef, MatDialogContent} from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-coupon',
  templateUrl: './coupon.component.html',
  styleUrls: ['./coupon.component.scss']
})
export class CouponComponent implements OnInit {

  constructor(private CouponService: CouponService, private _ProgressBarService: ProgressBarService, public dialog: MatDialog, private CategoryService: CategoryService, public _UploaderService: UploaderService) { }

  public displayedColumns: string[] = ['coupon_code', 'coupon_valid_from', 'coupon_valid_to', 'discount_amount', 'coupon_banner_url', 'action'];
  public dataSource;
  ngOnInit(): void {
    this.getAllCoupon();
  }

  getAllCoupon(){
    this._ProgressBarService.show();
    this.CouponService.getAllCoupon().subscribe((result) =>{
      console.log(result);
      this.dataSource = result['data'];
      this._ProgressBarService.hide();
    })
  }

  addCoupon(){
    this.CategoryService.getParentCategories().subscribe((result) =>{
      console.log(result);
      const dialogRef = this.dialog.open(DialogAddCoupon,
        {
        data: {
          'category': result
        }
      });

      dialogRef.afterClosed().subscribe(result => {
        this.getAllCoupon();
        console.log(result);
      });
    })

  }

  deleteCoupon(elem){
    console.log(elem);
    this.CouponService.deleteCoupon({'coupon_code': elem.coupon_code}).subscribe((result) =>{
      this.getAllCoupon();
    });
  }

}


@Component({
  selector: 'dialog-add-coupon',
  templateUrl: 'dialog-add-coupon.html',
  styleUrls: ['./coupon.component.scss']
})
export class DialogAddCoupon {

  form: FormGroup;
  public formAllData;
  public allCategory;
  public vendorByCatID;

  constructor(private CouponService: CouponService, private fb: FormBuilder, @Inject(MAT_DIALOG_DATA) public data, private dialogRef: MatDialogRef<DialogAddCoupon>, private UserService: UserService, private _ProgressBarService: ProgressBarService, private UploaderService: UploaderService) {

    this.allCategory = data.category;
    this.form = fb.group({
      coupon_code: ['', Validators.required],
      coupon_description: ['', Validators.required],
      coupon_valid_from: ['', Validators.required],
      coupon_valid_to: ['', Validators.required],
      coupon_usage_count: ['', Validators.required],
      discount_type: ['Percent', Validators.required],
      discount_amount: ['', Validators.required],
      category_id: ['', Validators.required],
      vendorid: [''],
      coupon_banner_url: ['', Validators.required],
      is_visible: ['1']
  });
  }

  saveCoupon(formData) {
    console.log(formData.value);
    this.CouponService.saveCoupon(formData.value).subscribe((result) =>{
      console.log(result);
      if(result['error']){
        alert(result['message']);
      }
      this.dialogRef.close();
    },
      error => {
        alert("error.message");
      }
    )

  }

  getVendorByCategory(e){
    this._ProgressBarService.show();
    console.log(e.value);
    this.UserService.getVendorByCategoryId({'categoryid': e.value}).subscribe((result) =>{
      console.log(result);
      this.vendorByCatID = result['data'];
      this._ProgressBarService.hide();
    });
  }

  fileChanged(f) {
    let formDate = new FormData();
    formDate.append("filenames[]", f.target.files[0]);
    formDate.append("type", "coupon");
    this._ProgressBarService.show();
    this.UploaderService.upload(formDate).subscribe(result => {
      console.log(result);
        this._ProgressBarService.hide();
        if (result) {
          this.form.patchValue({ coupon_banner_url: result });
        }
    })
}

 removeFile(file) {
  this.form.patchValue({ coupon_banner_url: "" });
}

  close() {
      this.dialogRef.close();
  }
}
