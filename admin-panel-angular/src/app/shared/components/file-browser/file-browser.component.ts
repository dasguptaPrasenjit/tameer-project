import { Component, OnInit, Input, ViewChild, ElementRef, forwardRef } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';

@Component({
    selector: 'app-file-browser',
    templateUrl: './file-browser.component.html',
    styleUrls: ['./file-browser.component.scss'],
    providers: [
        {
            provide: NG_VALUE_ACCESSOR,
            useExisting: forwardRef(() => FileBrowserComponent),
            multi: true
        }
    ]
})
export class FileBrowserComponent implements OnInit, ControlValueAccessor {
    @ViewChild('fileField') fileField: ElementRef;
    @Input() fileTypes: [] = [];
    @Input() required: boolean = false;
    file: File;
    constructor() { }

    ngOnInit(): void {
    }

    propagateChange = (_: any) => { };

    writeValue(value: any) {
        if (value !== undefined) {
            this.file = value;
            this.propagateChange(this.file);
        }
    }

    registerOnChange(fn) {
        this.propagateChange = fn;
    }

    registerOnTouched() { }

    selectFile(event) {
        this.file = event.target.files[0];
        this.propagateChange(this.file);
    }

    clear() {
        this.file = null;
        this.fileField.nativeElement.value = null;
        this.propagateChange(this.file);
    }

}
