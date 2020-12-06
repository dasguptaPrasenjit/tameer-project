import { NgModule, Optional, SkipSelf } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpInterceptorService } from './services/http-interceptor.service';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { AuthService } from './services/auth.service';
import { MatDialogModule } from '@angular/material/dialog';
@NgModule({
    declarations: [],
    imports: [
        CommonModule,
        HttpClientModule,
        MatSnackBarModule,
        MatDialogModule
    ],
    providers: [
        {
            provide: HTTP_INTERCEPTORS,
            useClass: HttpInterceptorService,
            multi: true,
        },
        AuthService
    ]
})
export class CoreModule {
    constructor(@Optional() @SkipSelf() parentModule?: CoreModule) {
        if (parentModule) {
            throw new Error(
                'CoreModule is already loaded. Import it in the AppModule only');
        }
    }
}
