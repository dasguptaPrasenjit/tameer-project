import { Component, Input } from '@angular/core';
import { BreakpointObserver, Breakpoints } from '@angular/cdk/layout';
import { Observable } from 'rxjs';
import { map, shareReplay } from 'rxjs/operators';
import { Router, ActivatedRoute } from '@angular/router';
import { MenuItem } from '../../models/menu-item';
import { AuthService } from 'src/app/core/services/auth.service';
import { MatDialog } from '@angular/material/dialog';
import { NotificationComponent } from '../notification/notification.component';
import { ConfigService } from '../../services/api/config.service';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { Subject } from 'rxjs/internal/Subject';

@UntilDestroy()
@Component({
    selector: 'app-layout-top-left',
    templateUrl: './layout-top-left.component.html',
    styleUrls: ['./layout-top-left.component.scss']
})
export class LayoutTopLeftComponent {
    user: any;
    timer = null;
    notiCount = 0;
    lastNotifId = 0;
    notiList$: Subject<[]> = new Subject();
    @Input('menuItems') menuItems: MenuItem[];
    isHandset$: Observable<boolean> = this.breakpointObserver.observe(Breakpoints.Handset)
        .pipe(
            map(result => result.matches),
            shareReplay()
        );
    constructor(
        private breakpointObserver: BreakpointObserver,
        public router: Router,
        public activatedRoute: ActivatedRoute,
        public _authService: AuthService,
        private dialog: MatDialog,
        private _ConfigService: ConfigService
    ) {
        this.user = this._authService.getUser();
        if (this.user) {
            this.getNotifications();
            this.timer = setInterval(() => {
                this.getNotifications();
            }, 5000);
        }
    }

    getNotifications() {
        this._ConfigService.getNotifications().pipe(untilDestroyed(this)).subscribe((result: any) => {
            if (result.status === 200) {
                this.notiCount = result.data?.notification_count;
                if (this.notiCount > 0) {
                    const max = result.data?.notifications.reduce(function(prev, current) {
                        return (parseInt(prev.id) > parseInt(current.id)) ? prev : current
                    });

                    if (parseInt(max.id) > this.lastNotifId) {
                        this.lastNotifId = parseInt(max.id);
                        this.notifyUser(max);
                    }
                    this.notiList$.next(result.data?.notifications);
                }
            }
        });
    }

    notifyUser(notifObj) {
        if (!("Notification" in window)) {
            alert("This browser does not support desktop notification");
        } else if (Notification.permission === "granted") {
            let notification = new Notification(notifObj.title, {
                body: notifObj.message,
                icon: "favicon.ico"
            });
            notification.onclick = function(event) {
                window.focus();
            }            
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(function (permission) {
                if (permission === "granted") {
                    let notification = new Notification(notifObj.title, {
                        body: notifObj.message,
                        icon: "favicon.ico"
                    });
                    notification.onclick = function(event) {
                        window.focus();
                    }
                }
            });
        }
    }

    openNotifications() {
        this.getNotifications();
        this.dialog.open(NotificationComponent, {
            width: '600px',
            disableClose: true,
            data: {
                notiList$: this.notiList$
            }
        });
    }

    ngOnDestroy() {
        clearInterval(this.timer);
    }

}
