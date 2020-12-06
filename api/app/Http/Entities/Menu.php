<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use Notifiable;
    protected $table = 'menu';
	
	public function findAllMenu(){
        
        $menu_query =  DB::table($this->table);
        $menu_query->select('*');
		$menu_query->orderBy('parent_cat_id', 'ASC');
		
		return $menu_query->get();
	}
	
	public function findAllMenuBySubCategoryList($categoryListArr){
        
        $menu_query =  DB::table($this->table);
        $menu_query->select('*');
		$menu_query->whereIn('category_id', $categoryListArr);
		$menu_query->orderBy('parent_cat_id', 'ASC');
		
		return $menu_query->get();
	}
}
