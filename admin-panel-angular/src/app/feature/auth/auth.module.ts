import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthRoutingModule } from './auth-routing.module';
import { SigninComponent } from './signin/signin.component';
import { SharedModule } from '../../shared/shared.module';
import { AuthComponent } from './auth.component';

@NgModule({
    declarations: [SigninComponent, AuthComponent],
    imports: [
        CommonModule,
        AuthRoutingModule,
        SharedModule.forRoot()
    ]
})
export class AuthModule { }
