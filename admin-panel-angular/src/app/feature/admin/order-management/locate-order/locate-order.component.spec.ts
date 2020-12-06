import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LocateOrderComponent } from './locate-order.component';

describe('LocateOrderComponent', () => {
  let component: LocateOrderComponent;
  let fixture: ComponentFixture<LocateOrderComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LocateOrderComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LocateOrderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
