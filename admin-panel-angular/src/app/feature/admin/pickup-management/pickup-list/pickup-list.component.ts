import { Component, OnInit, ViewChild } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatTableDataSource } from '@angular/material/table';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { PickupDTO } from 'src/app/shared/models/pickup';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { PickupService } from 'src/app/shared/services/api/pickup.service';
import { SavePickupComponent } from '../save-pickup/save-pickup.component';
import { AssignPickupComponent } from '../assign-pickup/assign-pickup.component';

@UntilDestroy()
@Component({
  selector: 'app-pickup-list',
  templateUrl: './pickup-list.component.html',
  styleUrls: ['./pickup-list.component.scss']
})
export class PickupListComponent implements OnInit {

  displayedColumns = ['id', 'sender_name', 'receiver_name', 'payable_amount', 'item_type', 'status', 'dates', 'action'];
  length = 0;
  pageSize = 10;
  pageSizeOptions: number[] = [5, 10, 25, 100];
  pageEvent: PageEvent;
  dataSource: MatTableDataSource<PickupDTO>;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  constructor(
    private dialog: MatDialog,
    private _ProgressBarService: ProgressBarService,
    private _PickupService: PickupService,
    private _ToastService: ToastService
  ) { }

  ngOnInit(): void {
    this.dataSource = new MatTableDataSource([]);
    this.getAllPickups();
  }

  applyFilter(event) {
    const filterValue = (event.target as HTMLInputElement).value;
    this.dataSource.filter = filterValue.trim().toLowerCase();
    if (this.dataSource.paginator) {
      this.dataSource.paginator.firstPage();
    }
  }

  getAllPickups() {
    this._ProgressBarService.show();
    this._PickupService.getAllPickups().subscribe((result: PickupDTO[]) => {
      this.length = result.length;
      this.dataSource.data = result;
      this.dataSource.paginator = this.paginator;
      this._ProgressBarService.hide();
    });
  }

  addPickup() {
    const ref = this.dialog.open(SavePickupComponent, {
      width: '800px',
      disableClose: true
    });

    ref.afterClosed().subscribe(res => {
      if (res) {
        this.getAllPickups();
      }
    });
  }

  assign(pickup: PickupDTO) {
    const ref = this.dialog.open(AssignPickupComponent, {
      width: '600px',
      disableClose: true,
      data: {
        pickup
      }
    });

    ref.afterClosed().subscribe(res => {
      if (res) {
        this.getAllPickups();
      }
    });
  }

}
