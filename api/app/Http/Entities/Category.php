<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Notifiable;
    protected $table='categories';
    protected $table_details='category_details';
	
	/**
     * Get All Categories
     *
    */
    public function getAllCategory(){
        $parent_categories = DB::table($this->table)
					 ->join($this->table_details, $this->table.".id", '=', $this->table_details.".category_id")
					 ->orderBy('parent_id', 'ASC')
					 ->orderBy($this->table.".id", 'ASC')					 
                     ->select('*')
					 ->where('status', '=', 1)
                     ->get();

        return $parent_categories;
    }
    /**
     * Get Parent Categories
     *
    */
    public function getParentCategory(){
        $parent_categories = DB::table($this->table)
					 ->join($this->table_details, $this->table.".id", '=', $this->table_details.".category_id")
                     ->select('*')
					 ->whereNull('parent_id')
					 ->where('status', '=', 1)
                     ->get();

        return $parent_categories;
    }
	/**
     * Get Categories By Id
     *
    */
    public function getCategory($cat_id){
		//$category = DB::select("WITH RECURSIVE cte AS (SELECT id, parent_id FROM ".$this->table." WHERE id = ".$cat_id." AND ".$this->table.".status = 1 UNION ALL SELECT po.id, po.parent_id FROM ".$this->table." po INNER JOIN cte ON cte.parent_id = po.id) SELECT cte.id AS category_id, cte.parent_id,CD.name, CD.slug, CD.description AS description FROM cte INNER JOIN ".$this->table_details." AS CD ON cte.id = CD.category_id");
		
		$category = DB::table($this->table)
					 ->join($this->table_details, $this->table.".id", '=', $this->table_details.".category_id")
                     ->select('*')
					 ->where('categories.id', '=', $cat_id)
					 ->where('status', '=', 1)
                     ->get();

        return $category;
    }
	/**
     * Get Categories By Id
     *
    */
    public function getCategoryDetailsByVendorId($vendor_id){		
		$category = DB::select("SELECT DISTINCT(category_id) as subcategory_id FROM products WHERE id IN (SELECT DISTINCT(product_id) FROM skus WHERE vendor_id = $vendor_id AND skus.deleted_at IS NULL) AND is_active = 1");

        return $category;
    }
	/**
     * Get Categories By Parent Id
     *
    */
    public function getCategoryById($parent_id){
		//$category = DB::select("WITH RECURSIVE cte AS (SELECT id, parent_id FROM ".$this->table." WHERE id = ".$cat_id." AND ".$this->table.".status = 1 UNION ALL SELECT po.id, po.parent_id FROM ".$this->table." po INNER JOIN cte ON cte.parent_id = po.id) SELECT cte.id AS category_id, cte.parent_id,CD.name, CD.slug, CD.description AS description FROM cte INNER JOIN ".$this->table_details." AS CD ON cte.id = CD.category_id");
		
		$category = DB::table($this->table)
					 ->join($this->table_details, $this->table.".id", '=', $this->table_details.".category_id")
                     ->select('*')
					 ->where('parent_id', '=', $parent_id)
					 ->where('status', '=', 1)
                     ->get();

        return $category;
    }
	/**
     * Get Active Sub Categories By Parent Id
     *
    */
    public function getActiveCategoryById($parent_id){
		
		$category = DB::table($this->table)
					 ->join($this->table_details, $this->table.".id", '=', $this->table_details.".category_id")
                     ->select('*')
					 ->where('parent_id', '=', $parent_id)
					 ->where('status', '=', 1)
                     ->get();

        return $category;
    }
}
