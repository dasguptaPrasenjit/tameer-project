import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PickupManagementComponent } from './pickup-management.component';

describe('PickupManagementComponent', () => {
  let component: PickupManagementComponent;
  let fixture: ComponentFixture<PickupManagementComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PickupManagementComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PickupManagementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
