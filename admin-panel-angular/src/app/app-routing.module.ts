import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuard } from './shared/guards/auth.guard';
import { LoginGuard } from './shared/guards/login.guard';

const routes: Routes = [
    {
        path: "",
        loadChildren: () => import('./feature/intro/intro.module').then(m => m.IntroModule),
    },
    {
        path: "auth",
        canActivate: [LoginGuard],
        canLoad: [LoginGuard],
        loadChildren: () => import('./feature/auth/auth.module').then(m => m.AuthModule)
    },
    {
        path: "admin",
        canActivate: [AuthGuard],
        canLoad: [AuthGuard],
        loadChildren: () => import('./feature/admin/admin.module').then(m => m.AdminModule)
    },
    {
        path: '**',
        redirectTo: "page-not-found"
    }
];

@NgModule({
    imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule]
})
export class AppRoutingModule { }
