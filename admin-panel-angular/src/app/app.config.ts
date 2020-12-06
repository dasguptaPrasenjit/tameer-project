export const GenderOptions: any[] = [
    { key: 1, value: "Male" },
    { key: 2, value: "Female" }
];

export const PasswordPattern = '(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&].{5,}';

export const FoodTypes = [
    { key: 0, value: "None" },
    { key: 1, value: "Veg" },
    { key: 2, value: "Non Veg" },
    { key: 3, value: "Egg" }
];

export const resource = {
    LOGIN: "login",
    REGISTER: "vendor/registerbyadmin",
    VENDOR_UPDATE: "vendor/updatebyadmin",
    DELETE_VENDOR: "vendor/delete",
    VALIDATE_EMAIL: "verify/email",
    CATEGORY: "categories",
    PARENT_CATEGORY: "parent-categories",
    CATEGORY_ALL: "category/all",
    CATEGORY_ADD: "category/add",
    CATEGORY_UPDATE: "category/edit",
    CATEGORY_DELETE: "category/delete",
    PRODUCTS: "productbycategoryid",
    PRODUCT_DETAILS: "productvariantbyid",
    PRODUCT_VARIANT_DETAIL: "productvariantbyskuid",
    VENDORS: "vendors/all",
    UPLOAD: "upload/image",
    PRODUCT_ADD: "product/master/add",
    PRODUCT_UPDATE: "product/master/update",
    PRODUCT_VARIANT_ADD: "product/add",
    PRODUCT_VARIANT_UPDATE: "product/update",
    PRODUCT_VARIANT_DELETE: "product/delete",
    BANNER_ALL: "banner/all",
    BANNER_ADD: "banner/add",
    BANNER_UPDATE: "banner/update",
    BANNER_DELETE: "banner/delete",
    ORDER: {
        ACCEPTED: "order/list/accepted",
        NOT_ACCEPTED: "order/list/notaccepted",
        ALL: "order/list/all",
    },
    ORDER_ACCEPT: "order/accept",
    ORDER_ASSIGN: "order/assign",
    ORDER_DOWNLOAD: "order/export",
    ALL_CARRIER: "carrier/all",
    CARRIER_LIST: "carrier/available",
    CARRIER_DELETE: "carrier/delete",
    CARRIER_RESTORE: "carrier/restore",
    CARRIER_LOCATION: (id) => `carrier/${id}/location`,
    ORDER_LOCATION: "position/get",
    COUPON: {
        GETALLCOUPON: 'active/coupon',
        SAVECOUPON: 'coupon/add',
        DELETECOUPON: 'coupon/delete'
    },
    VENDORBYCATEGORYID: 'vendorbycatid',
    PICKUP: {
        PICKUP: 'pickup',
        PICKUP_CREATE: 'pickup/create',
        PICKUP_ASSIGN: 'pickup/assign'
    },
    NOTIFICATION: 'notification/admin',
    NOTIFICATION_OPEN: 'notification/open'
}


export const VariantPropertyOptions = [
    'Weight',
    'Length',
    'Launch date',
    'Expiry date'
]
