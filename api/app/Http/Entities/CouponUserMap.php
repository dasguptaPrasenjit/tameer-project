<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class CouponUserMap extends Model
{
    use Notifiable;
    protected $table = 'coupon_user_map';
	
	public function getUserUsedCount($user_id, $coupon_code){
		$coupon_user = DB::select("SELECT COUNT(*) as count FROM coupon_user_map WHERE user_id = $user_id AND coupon_code = '$coupon_code'");

        return $coupon_user;
	}
}
