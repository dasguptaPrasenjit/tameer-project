import { Component, AfterViewInit, OnDestroy, ElementRef } from '@angular/core';

@Component({
    selector: 'app-auth',
    templateUrl: './auth.component.html',
    styleUrls: ['./auth.component.scss']
})
export class AuthComponent implements AfterViewInit, OnDestroy {
    bgColor: string | "";
    constructor(
        private elementRef: ElementRef,
    ) { }

    ngAfterViewInit() {
        this.bgColor = this.elementRef.nativeElement.ownerDocument.body.style.backgroundColor;
        this.elementRef.nativeElement.ownerDocument.body.style.backgroundColor = '#ededed';
    }

    ngOnDestroy() {
        this.elementRef.nativeElement.ownerDocument.body.style.backgroundColor = this.bgColor;
    }

}
