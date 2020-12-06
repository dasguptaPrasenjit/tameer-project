import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListAdminUserComponent } from './list-admin-user.component';

describe('ListAdminUserComponent', () => {
  let component: ListAdminUserComponent;
  let fixture: ComponentFixture<ListAdminUserComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListAdminUserComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListAdminUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
