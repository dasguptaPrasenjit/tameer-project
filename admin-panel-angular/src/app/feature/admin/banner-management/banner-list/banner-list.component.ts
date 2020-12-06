import { Component, OnInit, ViewChild } from '@angular/core';
import { PageEvent, MatPaginator } from '@angular/material/paginator';
import { MatTableDataSource } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';
import { BannerDTO } from 'src/app/shared/models/banner';
import { BannerService } from 'src/app/shared/services/api/banner.service';
import { SaveBannerComponent } from '../save-banner/save-banner.component';
import { ConfirmComponent } from 'src/app/shared/components/confirm/confirm.component';
import { ToastService } from 'src/app/core/services/toast.service';

@Component({
  selector: 'app-banner-list',
  templateUrl: './banner-list.component.html',
  styleUrls: ['./banner-list.component.scss']
})
export class BannerListComponent implements OnInit {

  displayedColumns = ['name', 'image', 'updated_at', 'action'];
  length = 0;
  pageSize = 10;
  pageSizeOptions: number[] = [5, 10, 25, 100];
  pageEvent: PageEvent;
  dataSource: MatTableDataSource<BannerDTO>;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  constructor(
    private dialog: MatDialog,
    private _ProgressBarService: ProgressBarService,
    public _UploaderService: UploaderService,
    private _BannerService: BannerService,
    private _ToastService: ToastService
  ) { }

  ngOnInit(): void {
    this.dataSource = new MatTableDataSource([]);
    this.getAllBanners();
  }

  getAllBanners() {
    this._ProgressBarService.show();
    this._BannerService.getAllBanners().subscribe((result: BannerDTO[]) => {
      this.length = result.length;
      this.dataSource.data = result;
      this.dataSource.paginator = this.paginator;
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
    const ref = this.dialog.open(SaveBannerComponent, {
      width: '600px',
      disableClose: true
    });

    ref.afterClosed().subscribe(res => {
      if (res) {
        this.getAllBanners();
      }
    });
  }

  edit(banner) {
    const ref = this.dialog.open(ConfirmComponent, {
      width: '600px',
      disableClose: true,
      data: { 
        message: (banner.is_active ? "Inactivating" : "Activating") + " this banner. Click Ok to continue."
      }
    });

    ref.afterClosed().subscribe(res => {
      if (res) {
        const status = banner.is_active ? 0 : 1;
        this._ProgressBarService.show();
        this._BannerService.updateBanner({ "bannerid": banner.id, "is_active": status }).subscribe((result: any) => {
          this._ToastService.info(result.message);
          this.getAllBanners();
        });
      }
    });
  }

  remove(banner) {
    const ref = this.dialog.open(ConfirmComponent, {
      width: '600px',
      disableClose: true,
      data: { 
        message: "Removing this banner. Click Ok to continue."
      }
    });

    ref.afterClosed().subscribe(res => {
      if (res) {
        this._ProgressBarService.show();
        this._BannerService.deleteBanner({ "bannerid": banner.id }).subscribe((result: any) => {
          this._ToastService.info(result.message);
          this.getAllBanners();
        });
      }
    });
  }

}
