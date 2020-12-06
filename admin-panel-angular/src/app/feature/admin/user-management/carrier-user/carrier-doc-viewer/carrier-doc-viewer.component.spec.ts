import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CarrierDocViewerComponent } from './carrier-doc-viewer.component';

describe('CarrierDocViewerComponent', () => {
  let component: CarrierDocViewerComponent;
  let fixture: ComponentFixture<CarrierDocViewerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CarrierDocViewerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CarrierDocViewerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
