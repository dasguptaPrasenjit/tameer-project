import { Component, Input, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { LocateCarrierComponent } from './locate-carrier/locate-carrier.component';

@Component({
  selector: 'app-track-carrier',
  templateUrl: './track-carrier.component.html',
  styleUrls: ['./track-carrier.component.scss']
})
export class TrackCarrierComponent implements OnInit {
  @Input('carrierId') carrierId: number;
  @Input('disabled') disabled: boolean = false;
  @Input('label') label: string = "";
  constructor(
    private dialog: MatDialog
  ) { }

  ngOnInit(): void {
  }

  track(){
    const ref = this.dialog.open(LocateCarrierComponent, {
      width: '600px',
      disableClose: true,
      data: {
        carrierId: this.carrierId,
        label: this.label
      }
    });
  }

}
