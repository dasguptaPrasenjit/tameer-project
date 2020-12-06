import { Component, OnInit, ViewChild, Output, EventEmitter } from '@angular/core';
import { PageEvent, MatPaginator } from '@angular/material/paginator';
import { MatTableDataSource } from '@angular/material/table';
import { MatSort } from '@angular/material/sort';
import { CategorySubCategoryDTO } from 'src/app/shared/models/category';
import { CategoryService } from 'src/app/shared/services/api/category.service';
import { ProductDTO } from 'src/app/shared/models/product';
import { ProgressBarService } from 'src/app/core/services/progress-bar.service';
import { FormControl } from '@angular/forms';
import { untilDestroyed, UntilDestroy } from '@ngneat/until-destroy';
import { MatDialog } from '@angular/material/dialog';
import { SaveProductComponent } from '../save-product/save-product.component';
import { ProductService } from 'src/app/shared/services/api/product.service';
import { UploaderService } from 'src/app/shared/services/api/uploader.service';

@UntilDestroy()
@Component({
    selector: 'app-product-list',
    templateUrl: './product-list.component.html',
    styleUrls: ['./product-list.component.scss']
})
export class ProductListComponent implements OnInit {
    @Output() onTabAdd = new EventEmitter<ProductDTO>();
    displayedColumns = ['product_name', 'product_image', 'manufacturer_name', 'action'];
    pageSize = 10;
    pageSizeOptions: number[] = [5, 10, 25, 100];
    pageEvent: PageEvent;
    dataSource: MatTableDataSource<ProductDTO>;
    @ViewChild(MatPaginator) paginator: MatPaginator;
    @ViewChild(MatSort, { static: true }) sort: MatSort;
    caterories: CategorySubCategoryDTO[] = [];
    categoryList = new FormControl();
    constructor(
        private _CategoryService: CategoryService,
        private _ProductService: ProductService,
        private _ProgressBarService: ProgressBarService,
        private dialog: MatDialog,
        public _UploaderService: UploaderService
    ) {
        this._ProgressBarService.show();
        this._CategoryService.getNestedCategories().subscribe((result: CategorySubCategoryDTO[]) => {
            this.caterories = result;
            this._ProgressBarService.hide();
        });

        this.categoryList.valueChanges.pipe(untilDestroyed(this)).subscribe(value => {
            this.getProducts(value);
        });
    }

    ngOnInit(): void {
        this.dataSource = new MatTableDataSource([]);
    }

    getProducts(id: number) {
        this._ProgressBarService.show();
        this._ProductService.getProductsByCategoryId(id).subscribe((result: ProductDTO[]) => {
            this.dataSource.data = result;
            this.dataSource.paginator = this.paginator;
            this.dataSource.sort = this.sort;
            this._ProgressBarService.hide();
        })
    }

    view(product) {
        this.onTabAdd.emit(product);
    }

    applyFilter(event) {
        const filterValue = (event.target as HTMLInputElement).value;
        this.dataSource.filter = filterValue.trim().toLowerCase();
        if (this.dataSource.paginator) {
            this.dataSource.paginator.firstPage();
        }
    }

    add() {
        const ref = this.dialog.open(SaveProductComponent, {
            width: '400px',
            disableClose: true
        });
        ref.afterClosed().subscribe(res => {
            if (res) {                
                this.getProducts(this.categoryList.value);
            }
        });
    }

    edit(product) {
        const ref = this.dialog.open(SaveProductComponent, {
            width: '600px',
            disableClose: true,
            data: { product }
        });

        ref.afterClosed().subscribe(res => {
            if (res) {
                this.getProducts(this.categoryList.value);
            }
        });
    }

}
