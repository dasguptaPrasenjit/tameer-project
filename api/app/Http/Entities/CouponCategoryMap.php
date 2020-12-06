<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class CouponCategoryMap extends Model
{
    use Notifiable;
    protected $table = 'coupon_category_map';
    
	public function getCategoriesByCouponId($id){
		$coupon_cat = DB::select("SELECT * FROM categories INNER JOIN category_details ON category_details.category_id = categories.id WHERE categories.id IN(SELECT category_id FROM coupon_category_map WHERE coupon_id = $id) AND status = 1");

        return $coupon_cat;
	}
	
	public function getCategoryIdByCouponId($id){
		$coupon_cat = DB::select("SELECT category_id FROM coupon_category_map WHERE coupon_id = $id");

        return $coupon_cat;
	}
}
