import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AssignPickupComponent } from './assign-pickup.component';

describe('AssignPickupComponent', () => {
  let component: AssignPickupComponent;
  let fixture: ComponentFixture<AssignPickupComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AssignPickupComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AssignPickupComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
