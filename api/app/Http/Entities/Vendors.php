<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class Vendors extends Model
{
    use Notifiable;
	protected $table = 'vendors';
	protected $fillable = ['mobile_number', 'mobile_verified_token', 'shop_name', 'category_id', 'address', 'city', 'state', 'zip', 'vendor_image'];

    /**
     * Get Vendors
     *
    */
    public function getAll($category_id){
        $vendors = DB::table($this->table)
					 ->join('users', $this->table.".id", '=', "users.vendor_id")
					 ->leftJoin('categories', $this->table.".category_id", '=', "categories.id")
					 ->leftJoin('category_details', "category_details.category_id", '=', "categories.id")
                     ->select(
						'users.id as user_id', 'users.email as user_email','users.mobile_number as mobile_number', 'vendors.category_id as category_id', 'vendors.id as vendor_id', 'users.name as name', 'category_details.name as category_name','shop_name','address','city','state','zip','is_active','vendor_image'
					 )
					 ->where($this->table.".is_active", 1)
					 ->whereNotNull($this->table.".category_id");
		if(!empty($category_id)){
			return $vendors->where($this->table.".category_id", $category_id)->get();
		} else {
			return $vendors->get();
		}
    }
	/**
     * Get Vendor By Id
     *
    */
    public function getVendorById($vendor_id){
			
		$vendors = DB::table($this->table)
					 ->join('users', $this->table.".id", '=', "users.vendor_id")
					 ->leftJoin('categories', $this->table.".category_id", '=', "categories.id")
					 ->leftJoin('category_details', "category_details.category_id", '=', "categories.id")
                     ->select(
						'users.id as user_id', 'users.email as user_email','users.mobile_number as mobile_number', 'categories.id as category_id', 'vendors.id as vendor_id', 'users.name as name', 'category_details.name as category_name','shop_name','address','city','state','zip', 'users.device_id as device_id', 'vendor_image'
					 )
					 ->where($this->table.".id",$vendor_id)
                     ->get();

        return $vendors;
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
							WHERE skus.product_id=".$product_id);

        return $product;
    }
	/**
     * Update Vendor Table
     *
    */
    public function updateVendorById($vendorid,$column,$value){
		
		DB::table($this->table)
                ->where('id', $vendorid)
                ->update([$column => $value]);

        return true;
    }
	
	public function getNearestPincode($lat,$lng){
		
		$nearest_pincodes = DB::select("SELECT pincodes.*, ( 3959 * acos( cos( radians('".$lat."') ) * cos( radians( pincodes.latitude ) ) * cos( radians( pincodes.longitude ) - radians('".$lng."') ) + sin( radians('".$lat."') ) * sin( radians( pincodes.latitude ) ) ) ) AS distance FROM pincodes");

        return $nearest_pincodes;
    }
	
	/**
     * Get Sales By Year
     *
    */
    public function getYearlySalesByVendorId($vendorid){
		$sales = DB::select("SELECT SUM(cart_amount) AS total_sales, YEAR(order_summary.created_at) as sales_year FROM (
								SELECT order_master.transaction_id,order_master.id as id, order_master.transaction_status as transaction_status, order_master.transaction_amount as transaction_amount, order_master.created_at as created_at, is_accepted, is_assigned, cart_amount
								FROM order_master
								INNER JOIN order_details ON order_details.order_id = order_master.id
								INNER JOIN (
											SELECT cart_id, SUM(price*quantity) AS cart_amount FROM cart_items 
											INNER JOIN skus ON cart_items.sku_id = skus.id
											WHERE skus.vendor_id = ".$vendorid."
											GROUP BY cart_id
										   ) AS cart_sale ON cart_sale.cart_id = order_details.cart_id
							WHERE transaction_status = 'Success' AND is_accepted = 1) AS order_summary
							GROUP BY sales_year
							ORDER BY sales_year DESC");

        return $sales;
    }
	/**
     * Get Sales By Month
     *
    */
    public function getMonthlySalesByVendorId($vendorid, $salesyear){

		$sales = DB::select("SELECT SUM(cart_amount) AS total_sale, YEAR(order_summary.created_at) as sales_year, MONTH(order_summary.created_at) as sales_month, MONTHNAME(order_summary.created_at) as sales_month_formated 
								FROM (
								SELECT order_master.transaction_id,order_master.id as id, order_master.transaction_status as transaction_status, order_master.transaction_amount as transaction_amount, order_master.created_at as created_at, is_accepted, is_assigned, cart_amount
								FROM order_master
								INNER JOIN order_details ON order_details.order_id = order_master.id
								INNER JOIN (
											SELECT cart_id, SUM(price*quantity) AS cart_amount FROM cart_items 
											INNER JOIN skus ON cart_items.sku_id = skus.id
											WHERE skus.vendor_id = ".$vendorid."
											GROUP BY cart_id
										   ) AS cart_sale ON cart_sale.cart_id = order_details.cart_id
							WHERE transaction_status = 'Success' AND is_accepted = 1 AND YEAR (order_master.created_at) = ".$salesyear.") AS order_summary
							GROUP BY sales_month
							ORDER BY sales_month DESC");

        return $sales;
    }
	public function getVendorDetailsByOrderId($orderId){
		$vendor_details = DB::select("SELECT * FROM vendors INNER JOIN (SELECT vendor_id FROM skus WHERE id IN (SELECT sku_id FROM cart_items WHERE cart_id IN (SELECT cart_id FROM order_details WHERE order_id = $orderId)) LIMIT 1) AS VND ON VND.vendor_id = vendors.id");

		if(sizeof($vendor_details)>0){
			return $vendor_details[0];
		}else{
			return '';
		}
	}
	
	public function getVendorBYProductId($product_id){
		$vendor_details = DB::select("SELECT * FROM vendors INNER JOIN (SELECT Distinct(vendor_id) FROM skus WHERE product_id = $product_id) AS VND ON VND.vendor_id = vendors.id WHERE vendors.is_active = 1");

		if(sizeof($vendor_details)>0){
			return $vendor_details;
		}else{
			return '';
		}
	}
}
