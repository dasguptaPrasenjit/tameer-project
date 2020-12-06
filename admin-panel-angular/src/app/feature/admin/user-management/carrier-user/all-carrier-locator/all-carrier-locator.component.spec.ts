import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AllCarrierLocatorComponent } from './all-carrier-locator.component';

describe('AllCarrierLocatorComponent', () => {
  let component: AllCarrierLocatorComponent;
  let fixture: ComponentFixture<AllCarrierLocatorComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AllCarrierLocatorComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AllCarrierLocatorComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
