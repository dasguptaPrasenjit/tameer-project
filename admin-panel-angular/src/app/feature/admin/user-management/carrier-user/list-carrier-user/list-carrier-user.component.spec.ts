import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListCarrierUserComponent } from './list-carrier-user.component';

describe('ListCarrierUserComponent', () => {
  let component: ListCarrierUserComponent;
  let fixture: ComponentFixture<ListCarrierUserComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListCarrierUserComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListCarrierUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
