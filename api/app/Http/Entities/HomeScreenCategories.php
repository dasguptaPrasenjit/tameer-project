<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class HomeScreenCategories extends Model
{
    use Notifiable;
    protected $table = 'home_screen_categories';
    protected $fillable = [
        'type','sku_id'
    ];
	
	/**
     * get Product
     *
    */
    public function getProductForHome($type){
		$type = str_replace(",","','",$type);
		$product = DB::select("SELECT SKUPVO.sku_id, sku, home_screen_categories.type as product_home_category, product_identification,products.product_image AS product_image, M.name AS manufacturer_name, price, products.id AS product_id, products.name AS product_name, skus.vendor_id, (SELECT GROUP_CONCAT(product_images) FROM product_detailed_images WHERE sku_id = SKUPVO.sku_id) AS detailed_product_images,
			(
			SELECT GROUP_CONCAT(CONCAT(PV.name,'|',PVO.name)) AS product_description FROM skus_product_variant_options AS SPVO LEFT JOIN skus AS SK ON SK.id=SPVO.sku_id LEFT JOIN product_variants AS PV ON PV.id=SPVO.product_variant_id LEFT JOIN product_variant_options AS PVO ON PVO.id=SPVO.product_variant_options_id WHERE SK.id=SKUPVO.sku_id
			) AS product_description
							FROM skus_product_variant_options AS SKUPVO							
							LEFT JOIN skus ON skus.id = sku_id
							LEFT JOIN products ON products.id = skus.product_id
							LEFT JOIN manufacturer AS M ON M.id = products.manufacturer_id
							INNER JOIN home_screen_categories ON home_screen_categories.sku_id = skus.id
							WHERE home_screen_categories.type IN('".$type."')
							AND products.is_active = 1 AND skus.deleted_at IS NULL
							GROUP BY skus.id
							ORDER BY home_screen_categories.type"							
							);
		return $product;
	}
}
