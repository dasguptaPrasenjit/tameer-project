import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SavePickupComponent } from './save-pickup.component';

describe('SavePickupComponent', () => {
  let component: SavePickupComponent;
  let fixture: ComponentFixture<SavePickupComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SavePickupComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SavePickupComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
