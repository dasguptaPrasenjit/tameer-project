export interface OrderDTO {
    cart_id: number,
    created_at: string,
    delivery_address: string,
    delivery_amount: number,
    id: number,
    is_accepted: number,
    is_assigned: number,
    order_amount: number,
    order_id: number,
    tax_amount: number,
    transaction_amount: number,
    transaction_id: string,
    transaction_status: string,
    updated_at: string,
    vendor_id: number,
}