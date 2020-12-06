import { Component, OnInit, ViewChild } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatTableDataSource } from '@angular/material/table';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { ToastService } from 'src/app/core/services/toast.service';
import { ConfirmComponent } from 'src/app/shared/components/confirm/confirm.component';
import { CarrierDTO } from 'src/app/shared/models/carrier';
import { CarrierService } from 'src/app/shared/services/api/carrier.service';
import { CarrierDocViewerComponent } from '../carrier-doc-viewer/carrier-doc-viewer.component';

@Component({
    selector: 'app-list-carrier-user',
    templateUrl: './list-carrier-user.component.html',
    styleUrls: ['./list-carrier-user.component.scss']
})
export class ListCarrierUserComponent implements OnInit {
    displayedColumns = ['carrier_id', 'name', 'email', 'mobile_number', 'documents', 'action'];
    length = 0;
    pageSize = 10;
    pageSizeOptions: number[] = [5, 10, 25, 100];
    pageEvent: PageEvent;
    dataSource: MatTableDataSource<CarrierDTO>;
    @ViewChild(MatPaginator) paginator: MatPaginator;

    constructor(
        private dialog: MatDialog,
        private _ProgressBarService: ProgressBarService,
        private _ToastService: ToastService,
        private _CarrierService: CarrierService
    ) { }

    ngOnInit(): void {
        this.dataSource = new MatTableDataSource([]);
        this.getCarrier();
    }

    getCarrier(){
        this._ProgressBarService.show();
        this._CarrierService.getAllCarriers().subscribe((result: CarrierDTO[]) => {
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

    changeIsActive(carrier: CarrierDTO){
        const ref = this.dialog.open(ConfirmComponent, {
            width: '600px',
            disableClose: true,
            data: { message: (carrier.is_active == 1 ? 'Block ':'Unblock ') + "the carrier? Click Ok to continue." }
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this._ProgressBarService.show();
                if(carrier.is_active == 1){
                    this._CarrierService.deleteCarrier({ carrierid: carrier.carrier_id }).subscribe((result: any) => {
                        this._ToastService.info(result.message);
                        this.getCarrier();
                    });
                } else {
                    this._CarrierService.restoreCarrier({ carrierid: carrier.carrier_id }).subscribe((result: any) => {
                        this._ToastService.info(result.message);
                        this.getCarrier();
                    });
                }
            }
        });
    }

    viewDocuments(carrier: CarrierDTO){
        const ref = this.dialog.open(CarrierDocViewerComponent, {
            width: '100%',
            disableClose: true,
            data: { carrier }
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this.getCarrier();
            }
        });
    }

}
