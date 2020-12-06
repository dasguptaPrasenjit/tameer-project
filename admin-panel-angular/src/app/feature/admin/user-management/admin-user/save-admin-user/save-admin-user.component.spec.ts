import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SaveAdminUserComponent } from './save-admin-user.component';

describe('SaveAdminUserComponent', () => {
  let component: SaveAdminUserComponent;
  let fixture: ComponentFixture<SaveAdminUserComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SaveAdminUserComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SaveAdminUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
