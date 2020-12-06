import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SaveBannerComponent } from './save-banner.component';

describe('SaveBannerComponent', () => {
  let component: SaveBannerComponent;
  let fixture: ComponentFixture<SaveBannerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SaveBannerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SaveBannerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
