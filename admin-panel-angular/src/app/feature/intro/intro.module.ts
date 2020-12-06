import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from '../../shared/shared.module';
import { IntroRoutingModule } from './intro-routing.module';
import { HomeComponent } from './home/home.component';
import { LayoutComponent } from './layout/layout.component';
import { UnauthorizedComponent } from './unauthorized/unauthorized.component';
import { PageNotFoundComponent } from './page-not-found/page-not-found.component';

@NgModule({
  declarations: [HomeComponent, LayoutComponent, UnauthorizedComponent, PageNotFoundComponent],
  imports: [
    CommonModule,
    IntroRoutingModule,
    SharedModule.forRoot()
  ]
})
export class IntroModule { }
