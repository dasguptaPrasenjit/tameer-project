import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TrackCarrierComponent } from './track-carrier.component';

describe('TrackCarrierComponent', () => {
  let component: TrackCarrierComponent;
  let fixture: ComponentFixture<TrackCarrierComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TrackCarrierComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TrackCarrierComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
