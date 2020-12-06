import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { Subject } from 'rxjs';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { ConfigService } from '../../services/api/config.service';

interface DialogData {
    notiList$: Subject<[]>
}

@UntilDestroy()
@Component({
    selector: 'app-notification',
    templateUrl: './notification.component.html',
    styleUrls: ['./notification.component.scss']
})
export class NotificationComponent implements OnInit {
    notiList: [] = [];
    constructor(
        @Inject(MAT_DIALOG_DATA) public modalData: DialogData,
        private _ConfigService: ConfigService,
        private _ProgressBarService: ProgressBarService
    ) { }

    ngOnInit(): void {
        this._ProgressBarService.show();
        this.modalData.notiList$.pipe(untilDestroyed(this)).subscribe(result => {
            this.notiList = result;
            this._ProgressBarService.hide();
        });
    }

    readNoti(noti) {
        this._ConfigService.readNotifications(noti.id).pipe(untilDestroyed(this)).subscribe((result: any) => {
            if (result.status === 200) {
            }
        });
    }
}