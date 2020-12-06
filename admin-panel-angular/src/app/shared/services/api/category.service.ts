import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { resource } from 'src/app/app.config';
import { map } from 'rxjs/internal/operators/map';
import { CategoryDTO, CategorySubCategoryDTO } from '../../models/category';

@Injectable({
    providedIn: 'root'
})
export class CategoryService {
    constructor(private http: HttpClient) { }

    addCategory(payload: any) {
        return this.http.post(resource.CATEGORY_ADD, payload);
    }

    updateCategory(payload: any) {
        return this.http.post(resource.CATEGORY_UPDATE, payload);
    }

    deleteCategory(payload: any) {
        return this.http.post(resource.CATEGORY_DELETE, payload);
    }

    getNestedCategories() {
        return this.http.post(resource.CATEGORY, {}).pipe(
            map((response: any) => {
                let list: CategorySubCategoryDTO[] = [];
                if (response.status === 200) {
                    response.data.forEach((category: any) => {
                        list.push({
                            ...category,
                            isParent: true,
                            parent_id: 0
                        });
                        if (category.subcategory && category.subcategory.length) {
                            category.subcategory.forEach(subcategory => {
                                list.push({
                                    ...subcategory,
                                    categoryname: subcategory.subcategoryname,
                                    isParent: false,
                                    parent_id: category.id
                                });
                            });
                        }
                    });
                }
                return list;
            })
        );
    }

    getCategories() {
        return this.http.post(resource.CATEGORY_ALL, {}).pipe(
            map((response: any) => {
                let list: CategoryDTO[] = [];
                if (response.status === 200) {
                    list = response.data;
                }
                return list;
            })
        );
    }

    getParentCategories() {
        return this.http.post(resource.PARENT_CATEGORY, {}).pipe(
            map((response: any) => {
                let list: CategoryDTO[] = [];
                if (response.status === 200) {
                    response.data.forEach(category => {
                        list.push(category);
                    });
                }
                return list;
            })
        );
    }
}
