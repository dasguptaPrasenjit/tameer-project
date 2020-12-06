import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LayoutTopLeftSideContentComponent } from './layout-top-left-side-content.component';

describe('LayoutTopLeftSideContentComponent', () => {
  let component: LayoutTopLeftSideContentComponent;
  let fixture: ComponentFixture<LayoutTopLeftSideContentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LayoutTopLeftSideContentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LayoutTopLeftSideContentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
