<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class ProductCoupon extends Model
{
    use Notifiable;
    protected $table = 'product_coupon';
    
	public function getActiveCoupons(){
		$coupons = DB::table($this->table)
				   ->select('*')
				   ->where("is_deleted",0)
				   ->get();

        return $coupons;
	}
	
	public function getCouponByCode($coupon_code){
		$coupons = DB::table($this->table)
				   ->select('*')
				   ->where("is_deleted",0)
				   ->where("coupon_code",$coupon_code)
				   ->get();

        return $coupons;
	}
	
	public function updateCouponByCode($coupon_code,$column,$value){
		DB::table($this->table)
                ->where('coupon_code', $coupon_code)
                ->update([$column => $value]);

        return true;
	}
	
	
}
