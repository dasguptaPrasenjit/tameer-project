import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'app-modal-action',
  templateUrl: './modal-action.component.html',
  styleUrls: ['./modal-action.component.scss']
})
export class ModalActionComponent implements OnInit {
  @Input() useCancel: boolean = true;
  constructor() { }

  ngOnInit(): void {
  }

}
