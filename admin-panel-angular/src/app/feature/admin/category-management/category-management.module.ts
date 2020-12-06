import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CategoryManagementRoutingModule } from './category-management-routing.module';
import { CategoryManagementComponent } from './category-management.component';
import { SaveCategoryComponent } from './save-category/save-category.component';
import { CategoryListComponent } from './category-list/category-list.component';
import { SharedModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [CategoryManagementComponent, CategoryListComponent, SaveCategoryComponent],
  imports: [
    CommonModule,
    CategoryManagementRoutingModule,
    SharedModule.forRoot()
  ]
})
export class CategoryManagementModule { }
