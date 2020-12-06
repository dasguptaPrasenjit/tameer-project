<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class OrderMaster extends Model
{
    use Notifiable;
    protected $table = 'order_master';
	protected $table_details = 'order_details';
    
	/**
     * Get Order Details
     *
    */
    public function getOrderById($order_id){
		$order = DB::select("SELECT * FROM order_master LEFT JOIN order_details ON order_details.order_id = order_master.id WHERE order_master.id = '".$order_id."'");

        return $order;
    }
	
	/**
     * Get All Orders
     *
    */
    public function getAllOrders(){
		$orders = DB::table($this->table)
				  ->join($this->table_details, $this->table.".id", '=', $this->table_details.".order_id")
				  ->select('*')
				  ->orderBy($this->table.".id","DESC")
				  ->get();

        return $orders;
	}
	
	/**
     * Get All Orders
     *
    */
    public function getAllOrdersForUser($user_id){
		$orders = DB::table($this->table)
				  ->join($this->table_details, $this->table.".id", '=', $this->table_details.".order_id")
				  ->join('cart', "cart.cart_id", '=', $this->table_details.".cart_id")
				  ->select('*')
				  ->where("customer_id",$user_id)
				  ->get();

        return $orders;
	}
		
	/**
     * Get Not Accepted Orders
     *
    */
    public function getOrdersNotAccepted(){
		$orders = DB::table($this->table)
				  ->join($this->table_details, $this->table.".id", '=', $this->table_details.".order_id")
				  ->select('*')
				  ->where("is_accepted",0)
				  ->where("transaction_status","Success")
				  ->get();

        return $orders;
	}
	
	/**
     * Get Accepted Orders
     *
    */
    public function getOrdersAccepted(){
		$orders = DB::table($this->table)
				  ->join($this->table_details, $this->table.".id", '=', $this->table_details.".order_id")
				  ->select('*')
				  ->where("is_accepted",1)
				  ->where("transaction_status","Success")
				  ->get();

        return $orders;
	}
	/**
     * Get Rejected Orders
     *
    */
    public function getOrdersRejected(){
		$orders = DB::table($this->table)
				  ->join($this->table_details, $this->table.".id", '=', $this->table_details.".order_id")
				  ->select('*')
				  ->where("is_accepted",2)
				  ->where("transaction_status","Success")
				  ->get();

        return $orders;
	}
	
	/**
     * Get Orders By Vendor
     *
    */
    public function getOrdersByVendor($vendorid){
		$orders = DB::select("SELECT order_master.id AS id, order_master.order_id AS order_id, order_master.transaction_id AS transaction_id, order_master.transaction_status AS transaction_status, order_master.transaction_amount AS transaction_amount, order_master.created_at AS transaction_date, order_master.is_accepted AS is_accepted, order_master.is_assigned AS is_assigned,
order_details.tax_amount, order_details.delivery_amount, order_details.delivery_address, order_details.order_amount, cart_sale.cart_id, cart_sale.vendor_id, cart_sale.cart_amount As vendor_amount,order_delivery_status.delivery_status, order_master.payment_type
								FROM order_master
								INNER JOIN order_details ON order_details.order_id = order_master.id
								INNER JOIN (
												SELECT cart_id, SUM(price*quantity) AS cart_amount, skus.vendor_id AS vendor_id FROM cart_items 
												INNER JOIN skus ON cart_items.sku_id = skus.id
												WHERE skus.vendor_id = ".$vendorid."
												GROUP BY cart_id
										   ) AS cart_sale ON cart_sale.cart_id = order_details.cart_id
								LEFT JOIN order_delivery_status ON order_delivery_status.order_id = order_master.id
								WHERE transaction_status = 'Success' OR transaction_status = 'Cod'								
								ORDER BY id DESC");

        return $orders;
	}	
	
	/**
     * Get Accepted Not Assigned Orders
     *
    */
    public function getOrdersAcceptedNotAssigned(){
		$orders = DB::select(
							'select * from `order_master` 
							inner join `order_details` on `order_master`.`id` = `order_details`.`order_id`
							WHERE 
							`is_accepted` = 1 and `transaction_status` = "Success" AND 
							order_master.id NOT IN (select order_master.id from `order_master` inner join `order_details` on `order_master`.`id` = `order_details`.`order_id` inner join
							`order_delivery_status` on `order_delivery_status`.`order_id` = `order_details`.`order_id` where `is_accepted` = 1 and
							`transaction_status` = "Success")'		
							);
        return $orders;
	}
	
	
	
	/**
     * Get Orders By Id
     *
    */
    public function getOrdersById($order_id){
		$orders = DB::table($this->table)
				  ->join($this->table_details, $this->table.".id", '=', $this->table_details.".order_id")
				  ->select('*')
				  ->where($this->table.".id",$order_id)
				  ->get();

        return $orders;
	}
	
	/**
     * Update Status
     *
    */
    public function updateOrderStatusByTransId($transid,$status){
		$orders = DB::table($this->table)
				  ->where('transaction_id', $transid)
				  ->update(['transaction_status' => $status]);

        return $orders;
	}
	public function getRecentOrdersForUser($user_id){
		$orders = DB::table($this->table)
				  ->join($this->table_details, $this->table.".id", '=', $this->table_details.".order_id")
				  ->join('cart', "cart.cart_id", '=', $this->table_details.".cart_id")
				  ->select('*')
				  ->where("customer_id",$user_id)
				  ->orderBy($this->table_details.'.id', 'DESC')
				  ->limit(3)
				  ->get();

        return $orders;
	}
	public function getUserIdByOrderId($orderId){		
		$orders = DB::select(
								"SELECT customer_id FROM cart INNER JOIN order_details ON cart.cart_id = order_details.cart_id WHERE order_details.order_id = $orderId"		
							);
		if(sizeof($orders)>0){
			return $orders[0]->customer_id;
		}else{
			return '';
		}
	}
}
