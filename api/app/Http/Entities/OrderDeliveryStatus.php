<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class OrderDeliveryStatus extends Model
{
    use Notifiable;
    protected $table = 'order_delivery_status';
	protected $table_order = 'order_master';
	protected $table_details = 'order_details';
	
	/**
     * Get Order Status
     *
    */
    public function findOrderStatusByOrderId($order_id){
		$orders = DB::table($this->table)
				  ->join($this->table_details, $this->table.".order_id", '=', $this->table_details.".order_id")
				  ->join($this->table_order, $this->table_order.".id", '=', $this->table_details.".order_id")
				  ->select('*')
				  ->where($this->table.".order_id",$order_id)
				  ->get();

        return $orders;
	}
	
	/**
     * Get Order Status Details By Id
     *
    */
    public function getOrdersStatusByOrderId($order_id){
		$orders = DB::table($this->table)
				  ->join($this->table_details, $this->table.".order_id", '=', $this->table_details.".order_id")
				  ->join($this->table_order, $this->table_order.".id", '=', $this->table_details.".order_id")
				  ->leftJoin("users", "users.carrier_id", '=', $this->table.".carrier_id")
				  ->select('*')
				  ->where($this->table.".order_id",$order_id)
				  ->get();

        return $orders;
	}
	/**
     * Update Status
     *
    */
    public function updateDeliveryStatusByOrderId($orderid,$status){
		$orders = DB::table($this->table)
				  ->where('order_id', $orderid)
				  ->update(['delivery_status' => $status]);

        return $orders;
	}
}
