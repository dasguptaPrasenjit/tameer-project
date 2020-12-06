<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use Notifiable;
    protected $table = 'banner';

    /**
     * Get Active Banner
     *
    */
    public function activeBanners(){
        $banner = DB::table($this->table)
				  ->select('*')
				  ->where($this->table.".is_active",1)
				  ->where($this->table.".is_deleted",0)
				  ->get();

        return $banner;
    }
	/**
     * Get Banner By Id
     *
    */
    public function getBannerById($banner_id){
			
		$banner = DB::table($this->table)
				  ->select('*')
				  ->where($this->table.".id",$banner_id)
				  ->get();

        return $banner;
    }
	/**
     * Update Banner Table
     *
    */
    public function updateBannerById($vendorid,$column,$value){
		
		DB::table($this->table)
                ->where('id', $vendorid)
                ->update([$column => $value]);

        return true;
    }
}
