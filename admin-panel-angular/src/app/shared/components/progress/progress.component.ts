import { Component } from '@angular/core';
import { ProgressBarService } from '../../../core/services/progress-bar.service';

@Component({
    selector: 'app-progress',
    templateUrl: './progress.component.html',
    styleUrls: ['./progress.component.scss']
})
export class ProgressComponent {

    constructor(public progress: ProgressBarService) {}

}
