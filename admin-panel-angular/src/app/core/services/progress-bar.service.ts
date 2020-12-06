import { Injectable } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';

const ProgressTypes = {
    Linier: 'Linier',
    Circular: 'Circular'
}
@Injectable({
    providedIn: 'root'
})
export class ProgressBarService {
    private isDisplayed: boolean = false;
    private progressType: string = ProgressTypes.Linier;
    
    constructor(private dialog: MatDialog) { }

    show() {
        this.progressType = this.dialog.openDialogs && this.dialog.openDialogs.length > 0 ? ProgressTypes.Circular : ProgressTypes.Linier;
        setTimeout(() => this.isDisplayed = true);
    }

    hide() {
        setTimeout(() => this.isDisplayed = false);
    }

    inProgress() {
        return this.isDisplayed;
    }

    isModal(){
        return ProgressTypes.Circular === this.progressType;
    }
}
