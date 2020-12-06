import { Component, OnInit, ViewChild } from '@angular/core';
import { CategoryService } from 'src/app/shared/services/api/category.service';
import { CategorySubCategoryDTO } from 'src/app/shared/models/category';
import { PageEvent, MatPaginator } from '@angular/material/paginator';
import { MatTableDataSource } from '@angular/material/table';
import { MatSort } from '@angular/material/sort';
import { SaveCategoryComponent } from '../save-category/save-category.component';
import { MatDialog } from '@angular/material/dialog';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';
import { ConfirmComponent } from 'src/app/shared/components/confirm/confirm.component';
import { ToastService } from 'src/app/core/services/toast.service';

@Component({
    selector: 'app-category-list',
    templateUrl: './category-list.component.html',
    styleUrls: ['./category-list.component.scss']
})
export class CategoryListComponent implements OnInit {
    displayedColumns = ['categoryname', 'category_image', 'description', 'status', 'action'];
    length = 0;
    pageSize = 10;
    pageSizeOptions: number[] = [5, 10, 25, 100];
    pageEvent: PageEvent;
    dataSource: MatTableDataSource<CategorySubCategoryDTO>;
    @ViewChild(MatPaginator) paginator: MatPaginator;
    @ViewChild(MatSort, { static: true }) sort: MatSort;
    constructor(
        private _CategoryService: CategoryService,
        private dialog: MatDialog,
        private _ProgressBarService: ProgressBarService,
        public _UploaderService: UploaderService,
        private _ToastService: ToastService
    ) { }

    ngOnInit(): void {
        this.dataSource = new MatTableDataSource([]);
        this.getCategories();
    }

    getCategories() {
        this._ProgressBarService.show();
        this._CategoryService.getNestedCategories().subscribe((result: CategorySubCategoryDTO[]) => {
            this.length = result.length;
            this.dataSource.data = result;
            this.dataSource.paginator = this.paginator;
            this.dataSource.sort = this.sort;
            this._ProgressBarService.hide();
        })
    }

    applyFilter(event) {
        const filterValue = (event.target as HTMLInputElement).value;
        this.dataSource.filter = filterValue.trim().toLowerCase();
        if (this.dataSource.paginator) {
            this.dataSource.paginator.firstPage();
        }
    }

    add() {
        const ref = this.dialog.open(SaveCategoryComponent, {
            width: '600px',
            disableClose: true
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this.getCategories();
            }
        });
    }

    addSubcategory(category) {
        const ref = this.dialog.open(SaveCategoryComponent, {
            width: '600px',
            disableClose: true,
            data: { parent: category }
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this.getCategories();
            }
        });
    }

    edit(category) {
        const ref = this.dialog.open(SaveCategoryComponent, {
            width: '600px',
            disableClose: true,
            data: { category }
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this.getCategories();
            }
        });
    }

    deleteCategory(category) {
        const ref = this.dialog.open(ConfirmComponent, {
            width: '600px',
            disableClose: true,
            data: { message: "Deleting this category. Click Ok to continue." }
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this._ProgressBarService.show();
                this._CategoryService.deleteCategory({ "id": category.id }).subscribe((result: any) => {
                    this._ToastService.info(result.message);
                    this.getCategories();
                });
            }
        });
    }

}
