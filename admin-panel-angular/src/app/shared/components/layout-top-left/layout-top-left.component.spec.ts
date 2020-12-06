import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LayoutTopLeftComponent } from './layout-top-left.component';

describe('LayoutTopLeftComponent', () => {
  let component: LayoutTopLeftComponent;
  let fixture: ComponentFixture<LayoutTopLeftComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LayoutTopLeftComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LayoutTopLeftComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
