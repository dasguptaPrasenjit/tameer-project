<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    use Notifiable;
    protected $table = 'carrier';

    protected $fillable = ['is_available'];

    public function user()
    {
        return $this->hasOne('App\User', 'carrier_id');
    }

    /**
     * Get Carrier
     *
    */
    public function getAll(){
        $carrier = DB::table($this->table)
					 ->join('users', $this->table.".id", '=', "users.carrier_id")
                     ->select(
						'users.id as user_id', 'users.email as user_email','users.mobile_number as mobile_number', 'carrier.id as carrier_id', 'users.name as name','carrier.is_available as is_available','carrier.is_active as is_active','carrier.device_id as device_id', 'carrier.latitude', 'carrier.longitude', 'carrier.proof_vehicle_no as proof_vehicle_no', 'carrier.proof_photo as proof_photo', 'carrier.proof_address as proof_address'
					 )
                     ->get();

        return $carrier;
    }
	/**
     * Get Carrier By Id
     *
    */
    public function getCarrierById($carrier_id){
			
		$carrier = DB::table($this->table)
					 ->join('users', $this->table.".id", '=', "users.carrier_id")
                     ->select(
						'users.id as user_id', 'users.email as user_email','users.mobile_number as mobile_number', 'carrier.id as carrier_id', 'users.name as name','carrier.is_available as is_available','carrier.is_active as is_active','users.device_id as device_id', 'carrier.latitude', 'carrier.longitude',  'carrier.proof_vehicle_no as proof_vehicle_no', 'carrier.proof_photo as proof_photo', 'carrier.proof_address as proof_address'
					 )
					 ->where($this->table.".id",$carrier_id)
                     ->get();

        return $carrier;
    }
	/**
     * Update Carrier Table
     *
    */
    public function updateCarrierById($carrierid,$column,$value){
		
		DB::table($this->table)
                ->where('id', $carrierid)
                ->update([$column => $value]);

        return true;
    }
	public function getAvailableCarrier(){
		$carrier = DB::select(
							'SELECT carrier.*,users.*, (SELECT count(*) FROM `order_delivery_status` WHERE delivery_status NOT IN("Declined","Delivered") AND carrier_id = carrier.id) AS no_of_active_orders FROM carrier INNER JOIN users ON users.carrier_id = carrier.id
							WHERE is_active = 1 AND is_available = 1'		
						 );
        return $carrier;
		
	}
	public function getCarrierOrderDetails($order_delivery_status, $carrier_id){
		$carrier = DB::select(
							"SELECT * FROM order_master WHERE id IN (SELECT order_id FROM order_delivery_status WHERE delivery_status = '".$order_delivery_status."' AND carrier_id = $carrier_id)"	
						 );
        return $carrier;
	
	}
	public function getCartDeliveryAddress($order_id){
		$carrier = DB::select(
							"SELECT cart_id, delivery_address FROM order_details WHERE order_id = $order_id"	
						 );
        return $carrier;
	
	}
	public function getCustomerDetails($cart_id){
		$carrier = DB::select(
							"SELECT name, email, mobile_number FROM users INNER JOIN cart ON users.id = cart.customer_id WHERE cart_id = $cart_id"	
						 );
        return $carrier;
	
	}
}
