import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SaveProductVariantComponent } from './save-product-variant.component';

describe('SaveProductVariantComponent', () => {
  let component: SaveProductVariantComponent;
  let fixture: ComponentFixture<SaveProductVariantComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SaveProductVariantComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SaveProductVariantComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
