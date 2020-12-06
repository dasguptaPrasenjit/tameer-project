import { Component, OnInit } from '@angular/core';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';

@Component({
  selector: 'app-modal-body',
  templateUrl: './modal-body.component.html',
  styleUrls: ['./modal-body.component.scss']
})
export class ModalBodyComponent implements OnInit {

  constructor(public _progressBarService: ProgressBarService) { }

  ngOnInit(): void {
  }

}
