import { Component, OnInit, ViewChild } from '@angular/core';
import { PageEvent, MatPaginator } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';
import { SaveAdminUserComponent } from '../save-admin-user/save-admin-user.component';
import { UserService } from 'src/app/shared/services/api/user.service';
import { VendorDTO } from 'src/app/shared/models/vendor';
import { MatTableDataSource } from '@angular/material/table';
import { MatSort } from '@angular/material/sort';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { ConfirmComponent } from 'src/app/shared/components/confirm/confirm.component';
import { ToastService } from 'src/app/core/services/toast.service';

@Component({
    selector: 'app-list-admin-user',
    templateUrl: './list-admin-user.component.html',
    styleUrls: ['./list-admin-user.component.scss']
})
export class ListAdminUserComponent implements OnInit {
    displayedColumns = ['name', 'user_email', 'shop_name', 'category_name', 'address', 'action'];
    length = 0;
    pageSize = 10;
    pageSizeOptions: number[] = [5, 10, 25, 100];
    pageEvent: PageEvent;
    dataSource: MatTableDataSource<VendorDTO>;
    @ViewChild(MatPaginator) paginator: MatPaginator;
    @ViewChild(MatSort) sort: MatSort;
    constructor(
        private _userService: UserService,
        private dialog: MatDialog,
        private _ProgressBarService: ProgressBarService,
        private _ToastService: ToastService
    ) {

    }

    ngOnInit(): void {
        this.dataSource = new MatTableDataSource([]);
        this.getVendors();
    }

    getVendors() {
        this._ProgressBarService.show();
        this._userService.getAllVendors().subscribe((result: VendorDTO[]) => {
            this.length = result.length;
            this.dataSource.data = result;
            this.dataSource.paginator = this.paginator;
            this.dataSource.sort = this.sort;
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

    addUser() {
        const ref = this.dialog.open(SaveAdminUserComponent, {
            width: '600px',
            disableClose: true
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this.getVendors();
            }
        });
    }

    editUser(user) {
        const ref = this.dialog.open(SaveAdminUserComponent, {
            width: '600px',
            disableClose: true,
            data: { user }
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this.getVendors();
            }
        });
    }

    remove(user) {
        const ref = this.dialog.open(ConfirmComponent, {
            width: '600px',
            disableClose: true,
            data: {
                message: "Removing the vendor. Click Ok to continue."
            }
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this._ProgressBarService.show();
                this._userService.removeVendor({ "vendorid": user.vendor_id }).subscribe((result: any) => {
                    this._ToastService.info(result.message);
                    this.getVendors();
                });
            }
        });
    }

}