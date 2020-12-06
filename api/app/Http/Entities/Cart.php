<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use Notifiable;
    protected $table = 'cart';
    protected $table_items = 'cart_items';
    protected $table_sku = 'skus';

    /**
     * Get Cart Items
     *
    */
    public function getAllItems($user_id, $cart_id){
		$cart_items = array();
		if($user_id != ''){
			$where_condition = " AND cart.customer_id = ".$user_id;
		}
		else if($cart_id != ''){
			$where_condition = " AND cart.cart_id = ".$cart_id;
		}else{
			return $cart_items;
		}

		$cart_items = DB::select("SELECT temp.*, SUM(temp.unit_price * temp.quantity) AS totalprice, (SELECT GROUP_CONCAT(CONCAT(PV.name,'|',PVO.name)) AS product_description FROM skus_product_variant_options AS SPVO LEFT JOIN skus AS SK ON SK.id=SPVO.sku_id LEFT JOIN product_variants AS PV ON PV.id=SPVO.product_variant_id LEFT JOIN product_variant_options AS PVO ON PVO.id=SPVO.product_variant_options_id WHERE SK.id=temp.sku_id) AS product_description 
								FROM 
									 (
										  SELECT 
										  cart_item_id, cart.cart_id, sku_id, product_id, quantity, skus.price AS unit_price, product_identification, products.name as product_name,products.product_image
										  FROM cart_items
										  INNER JOIN cart ON cart.cart_id = cart_items.cart_id								  
										  LEFT JOIN skus ON skus.id = cart_items.sku_id
										  INNER JOIN products ON products.id = skus.product_id
										  WHERE 
										  cart.is_active = 1 ".$where_condition." AND products.is_active = 1 AND skus.deleted_at IS NULL
									  ) AS temp GROUP BY temp.cart_item_id"
								);

        return $cart_items;
    }
	/**
     * Get Recent Cart Items
     *
    */
    public function getAllRecentItems($cart_id){
		$cart_items = array();

		$cart_items = DB::select("SELECT temp.*, SUM(temp.unit_price * temp.quantity) AS totalprice, (SELECT GROUP_CONCAT(CONCAT(PV.name,'|',PVO.name)) AS product_description FROM skus_product_variant_options AS SPVO LEFT JOIN skus AS SK ON SK.id=SPVO.sku_id LEFT JOIN product_variants AS PV ON PV.id=SPVO.product_variant_id LEFT JOIN product_variant_options AS PVO ON PVO.id=SPVO.product_variant_options_id WHERE SK.id=temp.sku_id) AS product_description 
								FROM 
									 (
										  SELECT 
										  cart_item_id, cart.cart_id, sku_id, product_id, quantity, skus.price AS unit_price, product_identification, products.name as product_name,products.product_image
										  FROM cart_items
										  INNER JOIN cart ON cart.cart_id = cart_items.cart_id								  
										  LEFT JOIN skus ON skus.id = cart_items.sku_id
										  INNER JOIN products ON products.id = skus.product_id
										  WHERE 
										  cart.cart_id = ".$cart_id." AND products.is_active = 1 AND skus.deleted_at IS NULL
									  ) AS temp GROUP BY temp.cart_item_id"
								);

        return $cart_items;
    }
	public function getCartById($cartId){
		$cart_details = DB::table($this->table)				 
						 ->select('*')
						 ->where('cart_id', '=', $cartId)
						 ->get();

        return $cart_details;
	}
	/**
	* Get Cart Count and Amount
	*
	*/
	public function getCounts($cart_id){
		$cart_counts = DB::select("SELECT 
								  count(Q.cart_item_id) AS itemcount, 
								  SUM(totalprice) AS grandtotal 
								  FROM (SELECT temp.*, SUM(temp.unit_price * temp.quantity) AS totalprice FROM (SELECT 
								  cart_item_id, cart.cart_id, sku_id, product_id, quantity, skus.price AS unit_price, product_identification, products.name as product_name
								  FROM cart_items
							      INNER JOIN cart ON cart.cart_id = cart_items.cart_id								  
								  LEFT JOIN skus ON skus.id = cart_items.sku_id
								  INNER JOIN products ON products.id = skus.product_id
							      WHERE 
								  cart.is_active = 1 
								  AND cart.cart_id = ".$cart_id." AND products.is_active = 1 AND skus.deleted_at IS NULL) AS temp GROUP BY temp.cart_item_id) AS Q"
								  );

        return $cart_counts;
	}
	
	/**
     * Set Cart
     *
    */
    public function setAllItems($user_id, $cart_id, $sku_id, $quantity){
		$cartId = '';
		if($cart_id != ''){
			$cartId = $cart_id;
		}else if ($user_id != ''){			
			$cart_arr = $this->getCartIdByUserId($user_id);
			if(!empty($cart_arr)){
				$cart_obj = $cart_arr[0];
				$cartId = $cart_obj->cart_id;
			}else{
				$cartId = $this->creatNewCart($user_id);
			}
		}else{
			$cartId = $this->creatNewCart();
		}
		//insert item
		$cart_item_id = $this->insertCartItem($sku_id,$cartId,$quantity);
		return array('cart_item_id' => $cart_item_id, 'cart_id' => $cartId);
    }
	/**
     * insert cart item
     *
    */
    public function insertCartItem($sku_id,$cart_id,$quantity){
		
		$cart_item_check = DB::select("SELECT * FROM cart_items WHERE cart_id = ".$cart_id." AND sku_id = ".$sku_id );
		if(!empty($cart_item_check)){
			$cart_item_id = $cart_item_check[0]->cart_item_id;
			$this->updateCartItem($cart_item_id,$quantity);
			
			return $cart_item_id;
		}
		
		$cart_item = new CartItem;
		$cart_item->cart_id = $cart_id;
		$cart_item->sku_id = $sku_id;
		$cart_item->quantity = $quantity;
		$cart_item->created_at = date("Y-m-d H:i:s");
		$cart_item->save();
		
		return $cart_item->cart_item_id;
    }
	public function getCartIdByItemId($cart_item_id){
		$cart_item = CartItem::find($cart_item_id);
		if(!$cart_item){
			return false;
		}
		return $cart_item->cart_id;
	}
	/**
     * Update Cart item
     *
    */
    public function updateCartItem($cart_item_id,$quantity){
		$cart_item = CartItem::find($cart_item_id);
		if(!$cart_item){
			return false;
		}
		if($quantity > 0){			
			$cart_item->quantity = $quantity;
			$cart_item->updated_at = date("Y-m-d H:i:s");
			$cart_item->save();		
			
			return $cart_item->cart_item_id;
		}else{
			$cart_item->delete();
			
			return true;
		}
    }
	public function deleteCartItems($cartid){
		if($cartid != ''){
			DB::delete('DELETE FROM '.$this->table_items.' WHERE cart_id = ?',[$cartid]);		
		}		
		return true;
	}
	/**
     * Get Cart ID By User ID
     *
    */
    public function getCartIdByUserId($user_id){
		
		$cart_details = DB::select("SELECT cart_id FROM cart WHERE cart.is_active = 1 AND cart.customer_id=".$user_id);

        return $cart_details;
    }
	/**
     * Create a new Cart
     *
    */
	public function creatNewCart($user_id=""){
		$cart = new Cart;
		if($user_id != ''){
			$cart->customer_id = $user_id;
		}
		$cart->created_at = date("Y-m-d H:i:s");
		$cart->save();
		
		return $cart->id;
    }
	
	/**
	*
	* get cart item processed
	*
	*/
	public function getCartItemProcessed($all_items,$cart_id){
		$cart_id = $all_items[0]->cart_id;
		if($cart_id != ''){
			$cart_count_arr = $this->getCounts($cart_id);
			$grandtotal = $cart_count_arr[0]->grandtotal;
			$data['cart_item_count'] = $cart_count_arr[0]->itemcount;
			$data['cart_total_amount'] = $grandtotal;
							
			// Tax Handling
			$GST = "10";
			$tax_amount = $this->getPercentOfNumber($grandtotal, $GST);
			$data['tax_amount'] = number_format($tax_amount,2);
			$total_including_tax = $tax_amount+$grandtotal;
			$data['total_including_tax'] = number_format(($total_including_tax),2);

			// Delivery Fee Handling
			$data['delivery_fee'] = number_format('50',2);
			$data['total_including_tax_delivery'] = number_format(($total_including_tax + 50),2);
			
			return $data;
		}else{
			return false;
		}		
	}
	/**
	*
	* Get Percentage
	*
	*/
	public function getPercentOfNumber($number, $percent){
		return ($percent / 100) * $number;
	}
	
	/**
     * Update Carrier Table
     *
    */
    public function updateCartById($cartid,$column,$value){
		
		DB::table($this->table)
                ->where('cart_id', $cartid)
                ->update([$column => $value]);

        return true;
    }
	
	/**
     * Update Carrier Table
     *
    */
    public function updateCartByUserId($userid,$column,$value){
		
		DB::table($this->table)
                ->where('customer_id', $userid)
                ->update([$column => $value]);

        return true;
    }
}
