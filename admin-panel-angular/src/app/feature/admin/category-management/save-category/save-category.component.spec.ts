import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SaveCategoryComponent } from './save-category.component';

describe('SaveCategoryComponent', () => {
  let component: SaveCategoryComponent;
  let fixture: ComponentFixture<SaveCategoryComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SaveCategoryComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SaveCategoryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
