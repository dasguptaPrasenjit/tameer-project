export interface CategoryDTO {
    category_id: string,
    created_at: string,
    description: string,
    id: number,
    meta_description: string,
    meta_keywords: string,
    meta_title: string,
    name: string,
    parent_id: string,
    position: string,
    remark: string,
    slug: string,
    status: string,
    updated_at: string
}

export interface CategorySubCategoryDTO {
    category_image: string,
    categoryname: string,
    description: string,
    id: number,
    status: string,
    isParent: boolean,
    parent_id: number,
    slug?: string,
    meta_title?: string,
    meta_description?: string,
    meta_keywords?: string
}