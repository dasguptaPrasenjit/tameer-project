import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { CarrierDTO } from 'src/app/shared/models/carrier';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';

export interface DialogData {
  carrier: CarrierDTO
}
@Component({
  selector: 'app-carrier-doc-viewer',
  templateUrl: './carrier-doc-viewer.component.html',
  styleUrls: ['./carrier-doc-viewer.component.scss']
})
export class CarrierDocViewerComponent implements OnInit {

  constructor(
    public _ProgressBarService: ProgressBarService,
    public dialogRef: MatDialogRef<CarrierDocViewerComponent>,
    @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
    private _toastService: ToastService,
    public  _UploaderService: UploaderService
  ) { }

  ngOnInit(): void {
  }

}
