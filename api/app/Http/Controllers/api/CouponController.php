<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Entities\ProductCoupon;
use App\Http\Entities\CouponCategoryMap;
use App\Http\Entities\CouponVendorMap;
use App\Http\Entities\CouponUserMap;
use Validator;

class CouponController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/**
    * Add Coupon
    * @return Response
    */
    public function addCoupon(Request $request)
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$ProductCouponObj = new ProductCoupon();
			$coupon_table = $ProductCouponObj->getTable();
			$request_arr = $request->all();
			$validator = Validator::make($request->all(), [ 
				'coupon_code' => 'required|unique:'.$coupon_table,
				'coupon_description' => 'required',
				'category_id' => 'required',
				'coupon_valid_from' => 'required',
				'coupon_valid_to' => 'required',
				'coupon_usage_count' => 'required',
				'discount_type' => 'required',
				'discount_amount' => 'required'
			]);
			if ($validator->fails()) {
				$errors = $validator->errors();
				$coupon_code_messages = $errors->get('coupon_code');
				$coupon_description_messages = $errors->get('coupon_description');
				$category_id_messages = $errors->get('category_id');
				$coupon_valid_from_messages = $errors->get('coupon_valid_from');
				$coupon_valid_to_messages = $errors->get('coupon_valid_to');
				$coupon_usage_count_messages = $errors->get('coupon_usage_count');
				$discount_type_messages = $errors->get('discount_type');
				$discount_amount_messages = $errors->get('discount_amount');
				
				if (!empty($coupon_code_messages)) {
					$response = array(
									'error' => true,
									'message' => $coupon_code_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($category_id_messages)) {
					$response = array(
									'error' => true,
									'message' => $category_id_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($coupon_description_messages)) {
					$response = array(
									'error' => true,
									'message' => $coupon_description_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($coupon_valid_from_messages)) {
					$response = array(
									'error' => true,
									'message' => $coupon_valid_from_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($coupon_valid_to_messages)) {
					$response = array(
									'error' => true,
									'message' => $coupon_valid_to_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($coupon_usage_count_messages)) {
					$response = array(
									'error' => true,
									'message' => $coupon_usage_count_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($discount_type_messages)) {
					$response = array(
									'error' => true,
									'message' => $discount_type_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($discount_amount_messages)) {
					$response = array(
									'error' => true,
									'message' => $discount_amount_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			
			
			
			$vendor_id = isset($request_arr['vendorid']) ? $request_arr['vendorid'] : '';
			$is_visible = isset($request_arr['is_visible']) ? $request_arr['is_visible'] : 0;
			$coupon_banner_url = isset($request_arr['coupon_banner_url']) ? $request_arr['coupon_banner_url'] : '';
			$coupon_code = $request_arr['coupon_code'];
			$coupon_description = $request_arr['coupon_description'];
			$coupon_valid_from = $request_arr['coupon_valid_from'];
			$coupon_valid_to = $request_arr['coupon_valid_to'];
			$coupon_usage_count = $request_arr['coupon_usage_count'];
			$discount_type = $request_arr['discount_type'];
			$discount_amount = $request_arr['discount_amount'];
			$category_id = $request_arr['category_id'];
			
			$ProductCouponObj->coupon_banner_url = $coupon_banner_url;
			$ProductCouponObj->coupon_code = $coupon_code;
			$ProductCouponObj->coupon_description = $coupon_description;
			$ProductCouponObj->coupon_valid_from = $coupon_valid_from;
			$ProductCouponObj->coupon_valid_to = $coupon_valid_to;
			$ProductCouponObj->coupon_usage_count = $coupon_usage_count;
			$ProductCouponObj->discount_type = $discount_type;
			$ProductCouponObj->discount_amount = $discount_amount;
			$ProductCouponObj->is_visible = $is_visible;
			$ProductCouponObj->save();
			$coupon_id = $ProductCouponObj->id;
			
			$CouponCategoryMapObj = new CouponCategoryMap();
			$CouponCategoryMapObj->category_id = $category_id;
			$CouponCategoryMapObj->coupon_id = $coupon_id;
			$CouponCategoryMapObj->save();
			
			if($vendor_id != ''){
				$vendor_arr = explode(",",$vendor_id);
				foreach($vendor_arr as $vendor){
					$CouponVendorMapObj = new CouponVendorMap();
					$CouponVendorMapObj->vendor_id = $vendor;
					$CouponVendorMapObj->coupon_id = $coupon_id;
					$CouponVendorMapObj->save();
				}
			}
			$data = $ProductCouponObj;
			$code = $this->successStatus;
		    $message = 'Coupon added successfully.';
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
							);
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $this->internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
    
	}
	/**
	*
	* Delete Coupon
	*/
	public function deleteCoupons(Request $request){
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$validator = Validator::make($request->all(), [ 
				'coupon_code' => 'required'
			]);
			if ($validator->fails()) {
				$errors = $validator->errors();
				$coupon_code_messages = $errors->get('coupon_code');
				
				if (!empty($coupon_code_messages)) {
					$response = array(
									'error' => true,
									'message' => $coupon_code_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			$coupon_code = $request_arr['coupon_code'];
			$ProductCouponObj = new ProductCoupon();
			$ProductCouponObj->updateCouponByCode($coupon_code,"is_deleted",1);
			$response = array(
								'message'=>'Coupon deleted successfully', 
								'status' => $this->successStatus
							 ); 
		}catch (Exception $e) {
			$response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $this->internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
	}
	/**
	*
	* Manage Visiblity
	*/
	public function visibleCoupons(Request $request){
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$validator = Validator::make($request->all(), [ 
				'coupon_code' => 'required'
			]);
			if ($validator->fails()) {
				$errors = $validator->errors();
				$coupon_code_messages = $errors->get('coupon_code');
				
				if (!empty($coupon_code_messages)) {
					$response = array(
									'error' => true,
									'message' => $coupon_code_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			$coupon_code = $request_arr['coupon_code'];
			$is_visible = $request_arr['is_visible'];
			$ProductCouponObj = new ProductCoupon();
			$ProductCouponObj->updateCouponByCode($coupon_code,"is_visible",$is_visible);
			$response = array(
								'message'=>'Coupon visibility changed successfully', 
								'status' => $this->successStatus
							 ); 
		}catch (Exception $e) {
			$response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $this->internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
	}
	
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getActiveCoupons(Request $request)
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$coupon_details = array();
			$ProductCouponObj = new ProductCoupon();
			$CouponCategoryMapObj = new CouponCategoryMap();
			$CouponVendorMapObj = new CouponVendorMap();
			$activeCoupons = $ProductCouponObj->getActiveCoupons();
			$i = 0;
			foreach($activeCoupons as $active){
				$coupon_details[$i]["id"] = $active->id;
				$coupon_details[$i]["coupon_code"] = $active->coupon_code;
				$coupon_details[$i]["coupon_description"] = $active->coupon_description;
				$coupon_details[$i]["coupon_valid_from"] = $active->coupon_valid_from;
				$coupon_details[$i]["coupon_valid_to"] = $active->coupon_valid_to;
				$coupon_details[$i]["coupon_usage_count"] = $active->coupon_usage_count;
				$coupon_details[$i]["discount_type"] = $active->discount_type;
				$coupon_details[$i]["discount_amount"] = $active->discount_amount;
				$coupon_details[$i]["coupon_banner_url"] = $active->coupon_banner_url;
				$coupon_details[$i]["is_visible"] = $active->is_visible;

				$categoryList = $CouponCategoryMapObj->getCategoriesByCouponId($active->id);
				$k = 0;
				$coupon_details[$i]["categories"] = array();
				foreach($categoryList as $list){
					$coupon_details[$i]["categories"][$k]['category_id'] = $list->category_id;
					$coupon_details[$i]["categories"][$k]['category_image'] = $list->category_image;
					$coupon_details[$i]["categories"][$k]['category_name'] = $list->name;
					
					$k++;
				}
				
				$vendorList = $CouponVendorMapObj->getVendorsByCouponId($active->id);
				$coupon_details[$i]["vendors"] = array();
				$k = 0;
				foreach($vendorList as $list){
					$coupon_details[$i]["vendors"][$k]['vendor_id'] = $list->id;
					$coupon_details[$i]["vendors"][$k]['shop_name'] = $list->shop_name;
					$coupon_details[$i]["vendors"][$k]['address'] = $list->address;
					$coupon_details[$i]["vendors"][$k]['city'] = $list->city;
					$coupon_details[$i]["vendors"][$k]['state'] = $list->state;
					$coupon_details[$i]["vendors"][$k]['zip'] = $list->zip;
					$coupon_details[$i]["vendors"][$k]['mobile_number'] = $list->mobile_number;
					
					$k++;
				}
								
				$i++;
			}
			$code = $this->successStatus;
			if(sizeof($coupon_details)>=1){
				$message = 'List of Coupons';
			}else{
				$message = 'No Coupons available.';
			}
			$response = array(
								'data' => $coupon_details, 
								'message' => $message,
								'code' => $code
							);
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $this->internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
	}
	/**
	* Apply Coupon
	*/
	public function applyCoupons(Request $request)
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$validator = Validator::make($request->all(), [ 
				'coupon_code' => 'required',
				'category_id' => 'required',
				'vendor_id' => 'required'
			]);
			if ($validator->fails()) {
				$errors = $validator->errors();
				$coupon_code_messages = $errors->get('coupon_code');
				$category_id_messages = $errors->get('category_id');
				$vendor_id_messages = $errors->get('vendor_id');
				
				if (!empty($coupon_code_messages)) {
					$response = array(
									'error' => true,
									'message' => $coupon_code_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($vendor_id_messages)) {
					$response = array(
									'error' => true,
									'message' => $vendor_id_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($category_id_messages)) {
					$response = array(
									'error' => true,
									'message' => $category_id_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			$date_now = date("Y-m-d");
			$vendor_id = $request_arr['vendor_id'];
			$coupon_code = $request_arr['coupon_code'];
			$category_id = $request_arr['category_id'];
			$user_id = $request_arr['user_id'];
			$cart_total_amount = floatval(preg_replace('/[^\d.]/', '', $request_arr['cart_total_amount']));
			$GST = "10";
			$delivery_fee = number_format('50',2);
			
			$coupon_details = array();
			$ProductCouponObj = new ProductCoupon();
			$coupon_details = $ProductCouponObj->getCouponByCode($coupon_code);
			if(sizeof($coupon_details)>0){
				$CouponUserMapObj = new CouponUserMap();
				$coupon_used_arr = $CouponUserMapObj->getUserUsedCount($user_id, $coupon_code);
				$coupon_used_for_this_user = $coupon_used_arr[0]->count;
				$coupon_valid_to = $coupon_details[0]->coupon_valid_to;
				$discount_amount = $coupon_details[0]->discount_amount;
				$discount_type = $coupon_details[0]->discount_type;
				$coupon_count_per_user = $coupon_details[0]->coupon_usage_count;				
				if($coupon_count_per_user > $coupon_used_for_this_user){
					if ($date_now <= $coupon_valid_to) {
						// Category Checking
						$CouponCategoryMapObj = new CouponCategoryMap();
						$catarory_arr = $CouponCategoryMapObj->getCategoryIdByCouponId($coupon_details[0]->id);
						$catarory_id_arr = array();
						foreach($catarory_arr as $category){
							$catarory_id_arr[] = $category->category_id;
						}
						if (in_array($category_id, $catarory_id_arr)){
							// Vendor Checking
							$CouponVendorMapObj = new CouponVendorMap();
							$vendor_arr = $CouponVendorMapObj->getVendorIdByCouponId($coupon_details[0]->id);
							$vendor_id_arr = array();
							foreach($vendor_arr as $vendor){
								$vendor_id_arr[] = $vendor->vendor_id;
							}
							if(sizeof($vendor_id_arr)>0){
								if (in_array($vendor_id, $vendor_id_arr)){
									
									$code = $this->successStatus;
									$message = 'Coupon applied successfully';
									$data = array();
									$data = $this->applyDiscount($discount_type, $discount_amount, $cart_total_amount, $GST, $delivery_fee);
									$response = array(
										'data' => $data, 
										'message' => $message,
										'code' => $code
									);
									return response()->json($response);
								
								}else{
									$response = array(
											'error' => true,
											'message' => "Coupon is not appilcable for this seller.",
											'status' => 401
										 );
									return response()->json($response);
								}
							}else{
								
								$code = $this->successStatus;
								$message = 'Coupon applied successfully';
								$data = array();
								$data = $this->applyDiscount($discount_type, $discount_amount, $cart_total_amount, $GST, $delivery_fee);
								return response()->json($response);
							}
						}else{
							$response = array(
											'error' => true,
											'message' => "Coupon is not appilcable for this category.",
											'status' => 401
										 );
							return response()->json($response);
						}
					}else{
						$response = array(
											'error' => true,
											'message' => "Coupon is already been expired.",
											'status' => 401
										 );
						return response()->json($response);
					}
				}else{
					$response = array(
										'error' => true,
										'message' => "Coupon usage limit exceeded",
										'status' => 401
									 );
					return response()->json($response);
				}
			}else{
				$response = array(
									'error' => true,
									'message' => "The Coupon is not valid.",
									'status' => 401
								 );
				return response()->json($response);
			}
			
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $this->internalErrorStatus
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
	
	public function applyDiscount($discount_type, $discount_amount, $cart_total_amount, $GST, $delivery_fee){
		$revised_amount = array();		
		if($discount_type == "Percent"){
			
			$coupon_description = "%$discount_amount off applied";				
			$discounted_amount = $this->getPercentOfNumber($cart_total_amount, $discount_amount);
			$total_after_discount = $cart_total_amount - $discounted_amount;
			$tax_amount = $this->getPercentOfNumber($total_after_discount, $GST);
			$total_including_tax_with_discount = $tax_amount + $total_after_discount;
			$total_including_tax_delivery_fee_with_discount = $total_including_tax_with_discount + $delivery_fee;
			
		}else{
			
			$coupon_description = "Flat INR $discount_amount off applied";
			$discounted_amount = $discount_amount;
			$total_after_discount = $cart_total_amount - $discounted_amount;
			$tax_amount = $this->getPercentOfNumber($total_after_discount, $GST);
			$total_including_tax_with_discount = $tax_amount + $total_after_discount;
			$total_including_tax_delivery_fee_with_discount = $total_including_tax_with_discount+$delivery_fee;
			
		}
		$revised_amount['coupon_description'] = $coupon_description;
		$revised_amount['discount_amount'] = number_format($discounted_amount,2);
		$revised_amount['total_after_discount'] = number_format($total_after_discount,2);
		$revised_amount['tax_amount'] = number_format($tax_amount,2);
		$revised_amount['total_including_tax_with_discount'] = number_format($total_including_tax_with_discount,2);
		$revised_amount['total_including_tax_delivery_fee_with_discount'] = number_format($total_including_tax_delivery_fee_with_discount,2);
		
		return $revised_amount;
	}
}