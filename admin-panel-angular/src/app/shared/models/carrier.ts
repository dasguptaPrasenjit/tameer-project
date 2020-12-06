export interface CarrierDTO {
    carrier_id?: number,
    created_at: string,
    device_id?: string,
    email?: string,
    email_verified_at?: string,
    email_verified_flag?: number,
    id: number,
    is_active: number,
    is_available: number,
    mobile_number?: string,
    mobile_verified_at?: string,
    mobile_verified_flag?: number,
    name?: string,
    no_of_active_orders?: number,
    updated_at: string,
    vendor_id: number,
    proof_vehicle_no?: string,
    proof_photo?: string,
    proof_address?: string,
    user_email?: string
    user_id?: number
}