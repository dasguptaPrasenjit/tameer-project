import { Component, OnInit, ViewChild } from '@angular/core';
import { PageEvent, MatPaginator } from '@angular/material/paginator';
import { MatTableDataSource } from '@angular/material/table';
import { OrderDTO } from 'src/app/shared/models/order';
import { MatDialog } from '@angular/material/dialog';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { OrderService } from 'src/app/shared/services/api/order.service';
import { FormControl } from '@angular/forms';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { ConfirmComponent } from 'src/app/shared/components/confirm/confirm.component';
import { AssignOrderComponent } from '../assign-order/assign-order.component';
import { ToastService } from 'src/app/core/services/toast.service';
import { LocateOrderComponent } from '../locate-order/locate-order.component';

@UntilDestroy()
@Component({
  selector: 'app-order-list',
  templateUrl: './order-list.component.html',
  styleUrls: ['./order-list.component.scss']
})
export class OrderListComponent implements OnInit {
  orderStatusArr = [
    { key: 'ALL', value: 'All' },
    { key: 'NOT_ACCEPTED', value: 'Pending' },
    { key: 'ACCEPTED', value: 'Accepted' },
  ];
  defaultOrderStatus = 'ALL';
  orderStatusList = new FormControl(this.defaultOrderStatus);
  displayedColumns = ['transaction_id', 'delivery_address', 'order_amount', 'delivery_amount', 'tax_amount', 'transaction_amount', 'action', 'location'];
  length = 0;
  pageSize = 10;
  pageSizeOptions: number[] = [5, 10, 25, 100];
  pageEvent: PageEvent;
  dataSource: MatTableDataSource<OrderDTO>;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  constructor(
    private dialog: MatDialog,
    private _ProgressBarService: ProgressBarService,
    private _OrderService: OrderService,
    private _ToastService: ToastService
  ) { }

  ngOnInit(): void {
    this.dataSource = new MatTableDataSource([]);
    this.getAllOrders(this.defaultOrderStatus);
    this.orderStatusList.valueChanges.pipe(untilDestroyed(this)).subscribe(value => {
      this.getAllOrders(value);
    });
  }

  getAllOrders(value: string) {
    this._ProgressBarService.show();
    this._OrderService.getAllOrders(value).subscribe((result: OrderDTO[]) => {
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

  accept(order: OrderDTO) {
    const ref = this.dialog.open(ConfirmComponent, {
      width: '600px',
      disableClose: true,
      data: {
        message: "Accepting the order. Once accepted, assign the order to a delivery boy. Click Ok to continue."
      }
    });

    ref.afterClosed().subscribe(res => {
      if (res) {
        this._ProgressBarService.show();
        this._OrderService.acceptOrder({ "orderid": order.order_id }).subscribe((result: any) => {
          this._ToastService.info(result.message);
          this.orderStatusList.patchValue('ACCEPTED');
          this._ProgressBarService.hide();
        });
      }
    });
  }

  assign(order: OrderDTO) {
    const ref = this.dialog.open(AssignOrderComponent, {
      width: '600px',
      disableClose: true,
      data: {
        order
      }
    });

    ref.afterClosed().subscribe(res => {
      if (res) {
        this.orderStatusList.patchValue('ALL');
      }
    });
  }

  locate(order) {
    const ref = this.dialog.open(LocateOrderComponent, {
      width: '600px',
      disableClose: true,
      data: {
        order
      }
    });
  }

  download(e) {
    e.preventDefault();
    this._ProgressBarService.show();
    this._OrderService.exportAllOrders(this.orderStatusList.value).subscribe((result) => {
      this._ProgressBarService.hide();
      const a = document.createElement('a')
      const objectUrl = URL.createObjectURL(result)
      a.href = objectUrl
      a.download = 'orders.xls';
      a.click();
      URL.revokeObjectURL(objectUrl);
    });
  }

}
