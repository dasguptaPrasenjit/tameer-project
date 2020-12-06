<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use Notifiable;
    protected $table = 'address_book';

    /**
     * Get Address
     *
    */
    public function getAll($user_id){
        $addresses = DB::table($this->table)
                     ->select('*')
					 ->where("user_id",$user_id)
					 ->where("is_active",1)
                     ->get();

        return $addresses;
    }
	/**
     * Get Address By Id
     *
    */
    public function getAddressById($address_id){
		$address = DB::table($this->table)
                     ->select('*')
					 ->where("id",$address_id)
                     ->get();

        return $address;
    }
	/**
     * Get Address By User Id
     *
    */
    public function getAddressCountByUserId($user_id){
		$address = DB::table($this->table)
                     ->select('*')
					 ->where("user_id",$user_id)
                     ->get();

        return $address;
    }
	/**
     * Get Product Details
     *
    */
    public function getProductById($product_id){
		
		$product = DB::select("SELECT sku_id, sku, product_identification,products.product_image AS product_image, M.name AS manufacturer_name, price, POV.product_id AS product_id, products.name AS product_name, POV.name AS varient_name, PVO.name AS varient_option
							FROM ".$this->table_skus_product_variant_options." AS SKUPVO
							LEFT JOIN ".$this->table_skus." ON skus.id = sku_id 
							LEFT JOIN ".$this->table_product_variants." AS POV ON POV.id = SKUPVO.product_variant_id
							LEFT JOIN ".$this->table_product_variant_options." AS PVO ON PVO.id = SKUPVO.product_variant_options_id
							LEFT JOIN ".$this->table." ON products.id = POV.product_id
							LEFT JOIN ".$this->table_manufacturer." AS M ON M.id = products.manufacturer_id
							WHERE skus.product_id=".$product_id." AND products.is_active = 1 AND skus.deleted_at IS NULL");

        return $product;
    }
}
