<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Entities\Cart;
use App\Http\Entities\CartItem;
use Validator;

class CartController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getCartItems(Request $request)
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$user_id = isset($request_arr['userid']) ? $request_arr['userid'] : '';
			$cart_id = isset($request_arr['cartid']) ? $request_arr['cartid'] : '';
			$cart = new Cart();
			$all_items = $cart->getAllItems($user_id, $cart_id);
			if(sizeof($all_items)>=1){
				$data = array();
				$data['cart_items'] = $all_items;
				$data_pdetails = $cart->getCartItemProcessed($all_items,$cart_id);
				if(!$data_pdetails){
					$code = $this->successStatus;
					$message = 'The cart is empty.';
				}else{
					$data['cart_item_count'] = $data_pdetails['cart_item_count'];
					$data['cart_total_amount'] = $data_pdetails['cart_total_amount'];
							
					// Tax Handling
					$data['tax_amount'] = $data_pdetails['tax_amount'];
					$data['total_including_tax'] = $data_pdetails['total_including_tax'];
			
					// Delivery Fee Handling
					$data['delivery_fee'] = $data_pdetails['delivery_fee'];
					$data['total_including_tax_delivery'] = $data_pdetails['total_including_tax_delivery'];
			
			
					$code = $this->successStatus;
					$message = 'Success';
				}
			}else{
				$code = $this->successStatus;
				$message = 'The cart is empty.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
							);
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
    
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
    * Insert Cart
	* @param Request
    * @return Response
    */
    public function setCartItems(Request $request)
    {		
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$user_id = isset($request_arr['userid']) ? $request_arr['userid'] : '';
			$cart_id = isset($request_arr['cartid']) ? $request_arr['cartid'] : '';
			$sku_id = isset($request_arr['skuid']) ? $request_arr['skuid'] : '';
			$quantity = isset($request_arr['quantity']) ? $request_arr['quantity'] : '';
			if($sku_id == ''){
				$response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
				return $response;
			}
			$cart = new Cart();
			$all_items = $cart->setAllItems($user_id, $cart_id, $sku_id,$quantity);
			if(sizeof($all_items)>=1){
				$data = array();
				$data['cart_item_id'] = strval($all_items['cart_item_id']);				
				$data['sku_id'] = strval($sku_id);
				
				$cart_id = $all_items['cart_id'];
				$cart_items = $cart->getAllItems($user_id, $cart_id);
				if(sizeof($cart_items)>=1){
					$data['cart_items'] = $cart_items;
					$data_pdetails = $cart->getCartItemProcessed($cart_items,$cart_id);
					if(!$data_pdetails){
						$code = $this->successStatus;
						$message = 'The cart is empty.';
					}else{
						$data['cart_item_count'] = $data_pdetails['cart_item_count'];
						$data['cart_total_amount'] = $data_pdetails['cart_total_amount'];
								
						// Tax Handling
						$data['tax_amount'] = $data_pdetails['tax_amount'];
						$data['total_including_tax'] = $data_pdetails['total_including_tax'];
				
						// Delivery Fee Handling
						$data['delivery_fee'] = $data_pdetails['delivery_fee'];
						$data['total_including_tax_delivery'] = $data_pdetails['total_including_tax_delivery'];
				
				
						$code = $this->successStatus;
						$message = 'Success';
					}
				}else{
					$code = $this->successStatus;
					$message = 'The cart is empty.';
				}
			}else{
				$code = $this->successStatus;
				$message = 'The cart is empty.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
							);
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
    }
	/**
	*
	*	Remove cart items
	*
	*/
	public function emptyCart(Request $request){
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$cartid = isset($request_arr['cartid']) ? $request_arr['cartid'] : '';
			if($cartid != ''){
				$cartObj = new Cart();
				if($cartObj->deleteCartItems($cartid)){
					$code = $this->successStatus;
					$message = 'Cart items removed successfully.';
					$data = array("cartid" => $cartid);
					$response = array(
									'data' => $data, 
									'message' => $message,
									'status' => $code
								);
				}
			}else{
				$response = array(
									'error' => true,
									'message' => "Some problem with the cart.",
									'status' => 401
								 );
				return response()->json($response); 
			}
			
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
	}		
	
	/**
    * Update Cart
	* @param Request
    * @return Response
    */
    public function updateCartItems(Request $request)
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$cart_item_id = isset($request_arr['cart_item_id']) ? $request_arr['cart_item_id'] : '';
			$quantity = isset($request_arr['quantity']) ? $request_arr['quantity'] : '';
			$user_id = isset($request_arr['userid']) ? $request_arr['userid'] : '';
			
			$cart = new Cart();
			$all_items = $cart->updateCartItem($cart_item_id,$quantity);
			if($all_items){
				
				$cart_id = $cart->getCartIdByItemId($cart_item_id);
				$cart_items = $cart->getAllItems($user_id, $cart_id);
				if(sizeof($cart_items)>=1){
					$data = array();
					$data['cart_items'] = $cart_items;
					$data_pdetails = $cart->getCartItemProcessed($cart_items,$cart_id);
					if(!$data_pdetails){
						$code = $this->successStatus;
						$message = 'The cart is empty.';
					}else{
						$data['cart_item_count'] = $data_pdetails['cart_item_count'];
						$data['cart_total_amount'] = $data_pdetails['cart_total_amount'];
								
						// Tax Handling
						$data['tax_amount'] = $data_pdetails['tax_amount'];
						$data['total_including_tax'] = $data_pdetails['total_including_tax'];
				
						// Delivery Fee Handling
						$data['delivery_fee'] = $data_pdetails['delivery_fee'];
						$data['total_including_tax_delivery'] = $data_pdetails['total_including_tax_delivery'];
				
				
						$code = $this->successStatus;
						$message = 'Success';
					}
				}else{
					$code = $this->successStatus;
					$message = 'The cart is empty.';
				}
				
			}else{
				$code = $this->successStatus;
				$message = 'No item associated.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
							);
			
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
	}
	
}