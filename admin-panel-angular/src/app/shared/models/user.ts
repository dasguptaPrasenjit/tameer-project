import { CreationDetail } from './creation-detail';

//Tables: User
export interface UserDTO extends CreationDetail {
    id: number,
    email: string,
    name: string,
    email_verified_at?: string
    email_verified_flag?: string
    email_verified_token?: string,
    mobile_number?: string
    mobile_verified_at?: string
    mobile_verified_flag?: string
    mobile_verified_token?: string,
    vendor_id?: number
}

export interface UserVendorDTO extends UserDTO {
    vendor_id?: number,
    user_id?: number,
    category_id: number,
    mobile_number?: string,
    shop_name?: string,
    address?: string,
    city?: string,
    state?: string,    
    zip?: number,
    vendor_image?: string,
    is_active: number
}