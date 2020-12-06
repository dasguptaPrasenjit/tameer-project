import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LocateCarrierComponent } from './locate-carrier.component';

describe('LocateCarrierComponent', () => {
  let component: LocateCarrierComponent;
  let fixture: ComponentFixture<LocateCarrierComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LocateCarrierComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LocateCarrierComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
