export interface ProductVariantDTO {
    productid: string,
    vendorid: string,
    price: string,
    no_of_unit: number,
    filenames: string[],
    variant?: object,
    sku?: string,
    sku_id?: string,
    sku_name?: string,
    is_veg?: string
}