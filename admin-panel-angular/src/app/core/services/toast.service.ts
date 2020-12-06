import { Injectable } from '@angular/core';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material/snack-bar';

@Injectable({
    providedIn: 'root'
})
export class ToastService {
    defaultConfig: MatSnackBarConfig;
    constructor(private snackbar: MatSnackBar) { 
        this.defaultConfig = {
            horizontalPosition: 'right',
            verticalPosition: 'bottom',
            duration: 6000
        }
    }

    info(msg){
        this.snackbar.open(msg, "Okay", { ...this.defaultConfig, panelClass: "primary" });
    }

    error(msg){
        this.snackbar.open(msg, "Okay", { ...this.defaultConfig, panelClass: "warn" });
    }

    warning(msg){
        this.snackbar.open(msg, "Okay", { ...this.defaultConfig, panelClass: "accent" });
    }
}
