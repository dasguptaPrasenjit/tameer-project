<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class CouponVendorMap extends Model
{
    use Notifiable;
    protected $table = 'coupon_vendor_map';
    
	public function getVendorIdByCouponId($id){
		$coupon_vendor = DB::select("SELECT vendor_id FROM coupon_vendor_map WHERE coupon_id = $id");

        return $coupon_vendor;
	}
	
	public function getVendorsByCouponId($id){		
		$coupon_vendor = DB::select("SELECT vendors.id, vendors.shop_name, vendors.address, vendors.city, vendors.state, vendors.zip, vendors.mobile_number FROM vendors INNER JOIN users ON users.vendor_id = vendors.id WHERE vendors.id IN (SELECT vendor_id FROM coupon_vendor_map WHERE coupon_vendor_map.coupon_id = $id) AND vendors.is_active = 1");

        return $coupon_vendor;
	}
}
