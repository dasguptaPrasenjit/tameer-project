import { Component, OnInit } from '@angular/core';
import { CategoryDTO } from 'src/app/shared/models/category';
import { ProductDTO } from 'src/app/shared/models/product';

const existingTabCount = 1;
@Component({
    selector: 'app-product-management',
    templateUrl: './product-management.component.html',
    styleUrls: ['./product-management.component.scss']
})
export class ProductManagementComponent implements OnInit {
    tab: CategoryDTO;
    tabs: ProductDTO[] = [];
    selected: number = 0;
    constructor() { }

    ngOnInit(): void {
        this.tab = null;
    }

    setTabIndex(index: number) {
        this.selected = index;
    }

    handleOnTabAdd(selectTab: ProductDTO) {
        let tabExistIdx = this.tabs.findIndex((tab: ProductDTO) => tab.product_id == selectTab.product_id);
        if (tabExistIdx !== -1) {
            this.setTabIndex(existingTabCount + tabExistIdx);
        } else {
            this.tabs.push(selectTab);
            this.setTabIndex(existingTabCount + this.tabs.length - 1);
        }
    }

    removeTab(index: number) {
        this.tabs.splice(index, 1);
    }

}
