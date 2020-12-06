<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;
use App\Http\Entities\Sku;
use App\Http\Entities\SkuVariantOption;
use App\Http\Entities\ProductVariantOption;
use App\Http\Entities\ProductVariant;
use App\Http\Entities\ItemAddHistory;
use App\Http\Entities\ProductDetailedImages;

class Product extends Model
{
    use Notifiable;
    protected $table = 'products';
    protected $table_manufacturer = 'manufacturer';
    protected $table_cat = 'categories';
    protected $table_cat_details = 'category_details';
    protected $table_skus = 'skus';
    protected $table_product_variants = 'product_variants';
    protected $table_product_variant_options = 'product_variant_options';
	protected $table_skus_product_variant_options = 'skus_product_variant_options';
	
	public function skus()
    {
        return $this->hasMany('App\Http\Entities\Sku', 'product_id');
    }

    /**
     * Get Parent products
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
     * Get product by Categories
     *
    */
    public function getProductByCatId($cat_id){
		$product = DB::select("SELECT P.name AS product_name,P.product_image AS product_image, M.name AS manufacturer_name, CD.name AS category_name, P.id AS product_id, C.id AS category_id, M.id AS manufacturer_id, C.parent_id AS parent_category_id
					FROM ".$this->table." AS P
					LEFT JOIN ".$this->table_manufacturer." AS M ON M.id = P.manufacturer_id
					LEFT JOIN ".$this->table_cat." AS C ON C.id = P.category_id
					INNER JOIN ".$this->table_cat_details." AS CD ON CD.category_id = C.id 
					WHERE P.category_id = ".$cat_id);

        return $product;
    }
	
	/**
     * Get active product by Categories
     *
    */
    public function getActiveProductByCatId($cat_id){
		$product = DB::select("SELECT P.name AS product_name,P.product_image AS product_image, M.name AS manufacturer_name, CD.name AS category_name, P.id AS product_id, C.id AS category_id, M.id AS manufacturer_id, C.parent_id AS parent_category_id
					FROM ".$this->table." AS P
					LEFT JOIN ".$this->table_manufacturer." AS M ON M.id = P.manufacturer_id
					LEFT JOIN ".$this->table_cat." AS C ON C.id = P.category_id
					INNER JOIN ".$this->table_cat_details." AS CD ON CD.category_id = C.id 
					WHERE is_active = 1
					AND P.category_id = ".$cat_id);

        return $product;
    }
	/**
     * Get Product Details
     *
    */
    public function getProductByVendorId($product_id, $vendor_id){
		
		$product = DB::select("SELECT SKUPVO.sku_id, sku, product_identification,products.product_image AS product_image, M.name AS manufacturer_name, price, products.id AS product_id, products.name AS product_name, (SELECT GROUP_CONCAT(product_images) FROM product_detailed_images WHERE sku_id = SKUPVO.sku_id) AS detailed_product_images,
		(
		SELECT GROUP_CONCAT(CONCAT(PV.name,'|',PVO.name)) AS product_description FROM skus_product_variant_options AS SPVO LEFT JOIN skus AS SK ON SK.id=SPVO.sku_id LEFT JOIN product_variants AS PV ON PV.id=SPVO.product_variant_id LEFT JOIN product_variant_options AS PVO ON PVO.id=SPVO.product_variant_options_id WHERE SK.id=SKUPVO.sku_id
		) AS product_description
						FROM ".$this->table_skus_product_variant_options." AS SKUPVO							
						LEFT JOIN ".$this->table_skus." ON skus.id = sku_id
						LEFT JOIN ".$this->table." ON products.id = skus.product_id
						LEFT JOIN ".$this->table_manufacturer." AS M ON M.id = products.manufacturer_id
						WHERE skus.product_id=".$product_id." AND skus.vendor_id=".$vendor_id." 
						AND skus.deleted_at IS NULL AND products.is_active = 1
						GROUP BY skus.id"
						);
						
		return $product;
    }
	/**
     * Get Product Details By Search
     *
    */
    public function getProductBySearchData($search_data){
		
		$product = DB::select("SELECT SKUPVO.sku_id, sku, product_identification,products.product_image AS product_image, M.name AS manufacturer_name, price, products.id AS product_id, products.name AS product_name, (SELECT GROUP_CONCAT(product_images) FROM product_detailed_images WHERE sku_id = SKUPVO.sku_id) AS detailed_product_images,
		(
		SELECT GROUP_CONCAT(CONCAT(PV.name,'|',PVO.name)) AS product_description FROM skus_product_variant_options AS SPVO LEFT JOIN skus AS SK ON SK.id=SPVO.sku_id LEFT JOIN product_variants AS PV ON PV.id=SPVO.product_variant_id LEFT JOIN product_variant_options AS PVO ON PVO.id=SPVO.product_variant_options_id WHERE SK.id=SKUPVO.sku_id
		) AS product_description
						FROM ".$this->table_skus_product_variant_options." AS SKUPVO							
						LEFT JOIN ".$this->table_skus." ON skus.id = sku_id
						LEFT JOIN ".$this->table." ON products.id = skus.product_id
						LEFT JOIN ".$this->table_manufacturer." AS M ON M.id = products.manufacturer_id
						WHERE skus.product_id IN (SELECT DISTINCT(id) AS product_id FROM products WHERE name LIKE '%".$search_data."%' )
						AND skus.deleted_at IS NULL AND products.is_active = 1
						GROUP BY skus.id"
						);
						
		return $product;
    }
	/**
     * Get Product Details
     *
    */
    public function getProductBySkuId($sku_id, $cart_id = ''){
		
		if($cart_id != ''){
			$product = DB::select("SELECT SKUPVO.sku_id, sku, product_identification,products.product_image AS product_image, M.name AS manufacturer_name, price, products.id AS product_id, products.name AS product_name, skus.vendor_id, (SELECT GROUP_CONCAT(product_images) FROM product_detailed_images WHERE sku_id = SKUPVO.sku_id) AS detailed_product_images,
			(
			SELECT GROUP_CONCAT(CONCAT(PV.name,'|',PVO.name)) AS product_description FROM skus_product_variant_options AS SPVO LEFT JOIN skus AS SK ON SK.id=SPVO.sku_id LEFT JOIN product_variants AS PV ON PV.id=SPVO.product_variant_id LEFT JOIN product_variant_options AS PVO ON PVO.id=SPVO.product_variant_options_id WHERE SK.id=SKUPVO.sku_id
			) AS product_description, 
			(SELECT quantity FROM cart_items WHERE sku_id = SKUPVO.sku_id AND cart_id = ".$cart_id.") AS quantity, (SELECT cart_item_id FROM cart_items WHERE sku_id = SKUPVO.sku_id AND cart_id = ".$cart_id.") AS cart_item_id
								FROM ".$this->table_skus_product_variant_options." AS SKUPVO							
								LEFT JOIN ".$this->table_skus." ON skus.id = sku_id 
								LEFT JOIN ".$this->table." ON products.id = skus.product_id
								LEFT JOIN ".$this->table_manufacturer." AS M ON M.id = products.manufacturer_id
								WHERE skus.id=".$sku_id."
								AND skus.deleted_at IS NULL AND products.is_active = 1
								GROUP BY skus.id"
								);
		}else{
			$product = DB::select("SELECT SKUPVO.sku_id, sku, product_identification,products.product_image AS product_image, M.name AS manufacturer_name, price, products.id AS product_id, products.name AS product_name, skus.vendor_id, (SELECT GROUP_CONCAT(product_images) FROM product_detailed_images WHERE sku_id = SKUPVO.sku_id) AS detailed_product_images,
			(
			SELECT GROUP_CONCAT(CONCAT(PV.name,'|',PVO.name)) AS product_description FROM skus_product_variant_options AS SPVO LEFT JOIN skus AS SK ON SK.id=SPVO.sku_id LEFT JOIN product_variants AS PV ON PV.id=SPVO.product_variant_id LEFT JOIN product_variant_options AS PVO ON PVO.id=SPVO.product_variant_options_id WHERE SK.id=SKUPVO.sku_id
			) AS product_description
							FROM ".$this->table_skus_product_variant_options." AS SKUPVO							
							LEFT JOIN ".$this->table_skus." ON skus.id = sku_id
							LEFT JOIN ".$this->table." ON products.id = skus.product_id
							LEFT JOIN ".$this->table_manufacturer." AS M ON M.id = products.manufacturer_id
							WHERE skus.id=".$sku_id." 
							AND skus.deleted_at IS NULL AND products.is_active = 1
							GROUP BY skus.id"
							);
		}

        return $product;
    }
	/**
	* Product added unit
	*
	*/
	function getAvailableUnitBySkuId($sku_id){
			$total_unit = DB::select("SELECT SUM(quantity) as total_unit FROM item_added_history WHERE sku_id=".$sku_id." GROUP BY sku_id");
			
			return $total_unit;
	}
	/**
     * Get Product Details
     *
    */
    public function getProductById($product_id, $cart_id = ''){
		
		if($cart_id != ''){
			$product = DB::select("SELECT SKUPVO.sku_id, sku, product_identification,products.product_image AS product_image, M.name AS manufacturer_name, price, sku_name, is_veg, products.id AS product_id, products.name AS product_name, (SELECT GROUP_CONCAT(product_images) FROM product_detailed_images WHERE sku_id = SKUPVO.sku_id) AS detailed_product_images,
			(
			SELECT GROUP_CONCAT(CONCAT(PV.name,'|',PVO.name)) AS product_description FROM skus_product_variant_options AS SPVO LEFT JOIN skus AS SK ON SK.id=SPVO.sku_id LEFT JOIN product_variants AS PV ON PV.id=SPVO.product_variant_id LEFT JOIN product_variant_options AS PVO ON PVO.id=SPVO.product_variant_options_id WHERE SK.id=SKUPVO.sku_id
			) AS product_description, 
			(SELECT quantity FROM cart_items WHERE sku_id = SKUPVO.sku_id AND cart_id = ".$cart_id.") AS quantity, (SELECT cart_item_id FROM cart_items WHERE sku_id = SKUPVO.sku_id AND cart_id = ".$cart_id.") AS cart_item_id
								FROM ".$this->table_skus_product_variant_options." AS SKUPVO							
								LEFT JOIN ".$this->table_skus." ON skus.id = sku_id 
								LEFT JOIN ".$this->table." ON products.id = skus.product_id
								LEFT JOIN ".$this->table_manufacturer." AS M ON M.id = products.manufacturer_id
								WHERE skus.product_id=".$product_id."
								AND skus.deleted_at IS NULL AND products.is_active = 1
								GROUP BY skus.id"
								);
		}else{
			$product = DB::select("SELECT SKUPVO.sku_id, sku, product_identification,products.product_image AS product_image, M.name AS manufacturer_name, price, sku_name, is_veg, products.id AS product_id, products.name AS product_name, (SELECT GROUP_CONCAT(product_images) FROM product_detailed_images WHERE sku_id = SKUPVO.sku_id) AS detailed_product_images,
			(
			SELECT GROUP_CONCAT(CONCAT(PV.name,'|',PVO.name)) AS product_description FROM skus_product_variant_options AS SPVO LEFT JOIN skus AS SK ON SK.id=SPVO.sku_id LEFT JOIN product_variants AS PV ON PV.id=SPVO.product_variant_id LEFT JOIN product_variant_options AS PVO ON PVO.id=SPVO.product_variant_options_id WHERE SK.id=SKUPVO.sku_id
			) AS product_description
							FROM ".$this->table_skus_product_variant_options." AS SKUPVO							
							LEFT JOIN ".$this->table_skus." ON skus.id = sku_id
							LEFT JOIN ".$this->table." ON products.id = skus.product_id
							LEFT JOIN ".$this->table_manufacturer." AS M ON M.id = products.manufacturer_id
							WHERE skus.product_id=".$product_id." 
							AND skus.deleted_at IS NULL AND products.is_active = 1
							GROUP BY skus.id"
							);
		}

        return $product;
    }
	
	/**
     * add Product Details
     *
    */
    public function addProduct($product_id,$price,$vendor_id,$no_of_unit,$variant_arr,$filenames, $sku_name, $is_veg){
	
		$product_details = Product::find($product_id);
		$sku_identification = strtoupper(str_replace(" ","",$product_details->name)).$vendor_id.$no_of_unit.rand(100,10000);
		
		$skuObj = new Sku();
		$skuObj->product_id = $product_id;
		$skuObj->sku = $sku_identification;
		$skuObj->product_identification = $sku_identification;
		$skuObj->price = $price;
		$skuObj->vendor_id = $vendor_id;
		$skuObj->sku_name = $sku_name;
		$skuObj->is_veg = $is_veg;
		$skuObj->save();
		
		$sku_id = $skuObj->id;
		$sku_option_id = $this->addProductVariantDetails($variant_arr,$product_id,$sku_id);
		if($filenames){
			$this->addProductDetailedImages($filenames,$sku_id);
		}
		$this->addProductUnitQuantity($sku_id,$no_of_unit);
		$response = array(
				"productid" => $product_id,
				"skuid" => $sku_id,
				"price" => $price,
				"no_of_unit" => $no_of_unit
		);
		return $response;
	}
	/**
     * add Product unit
     *
    */
    public function addProductUnitQuantity($sku_id,$no_of_unit){
		
		$item_added_history_obj = new ItemAddHistory();
		$item_added_history_obj->sku_id = $sku_id;
		$item_added_history_obj->quantity = $no_of_unit;
		
		$item_added_history_obj->save();
		$variant_option_id = $item_added_history_obj->id;
		return true;
	}
	/**
	*
	*
	*/
	public function addProductDetailedImages($filenames,$sku_id){		
		if(sizeof($filenames)>=1){
			$i=1;
			foreach($filenames as $file){
				$product_image = new ProductDetailedImages();
				$product_image->sku_id = $sku_id;
				$product_image->product_images = $file;
				$product_image->position = $i;
				$product_image->save();
				$i++;
			}
		}
	}
	/**
	*
	*
	*/
	public function updateProductDetailImage($sku_id,$filenames){
		$image_details = DB::table("product_detailed_images")
						 ->select('*')
						 ->where('sku_id', '=', $sku_id)
						 ->get();
		foreach($image_details as $image){
			if (file_exists(public_path()."/".$image->product_images)) {
				unlink(public_path()."/".$image->product_images);
			}
			DB::table("product_detailed_images")
				->where('sku_id', '=', $sku_id)
				->delete();
		}
		$this->addProductDetailedImages($filenames,$sku_id);
	}
	/**
     * add Product Variant
     *
    */
    public function addProductVariantDetails($variant_arr,$product_id,$sku_id){
		if(sizeof($variant_arr)>=1){
			foreach($variant_arr as $key=>$val){
				// Variant
				$product_variant = DB::select("SELECT id FROM product_variants WHERE name='$key' AND product_id=$product_id");
				if(sizeof($product_variant)>=1){
					$variant_id = $product_variant[0]->id;
 				}else{
					$product_variant_obj = new ProductVariant();

					$product_variant_obj->product_id = $product_id;
					$product_variant_obj->name = ucwords($key);
					
					$product_variant_obj->save();
					$variant_id = $product_variant_obj->id;
				}
				// Variant Option
				$product_variant_option = DB::select("SELECT id FROM product_variant_options WHERE name='$val' AND product_variant_id=$variant_id");
				if(sizeof($product_variant_option)>=1){
					$variant_option_id = $product_variant_option[0]->id;
 				}else{
					$product_variant_option_obj = new ProductVariantOption();
					$product_variant_option_obj->product_variant_id = $variant_id;
					$product_variant_option_obj->name = ucwords($val);
					
					$product_variant_option_obj->save();
					$variant_option_id = $product_variant_option_obj->id;
				}
				
				$sku_variant_option_obj = new SkuVariantOption();
				$sku_variant_option_obj->sku_id = $sku_id;
				$sku_variant_option_obj->product_variant_id = $variant_id;
				$sku_variant_option_obj->product_variant_options_id = $variant_option_id;
				
				$sku_variant_option_obj->save();
			}
						
			return true;
		}
	}
}
