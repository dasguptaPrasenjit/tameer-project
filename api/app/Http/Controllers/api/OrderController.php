<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Validator;
use App\Http\Entities\Cart;
use App\Http\Entities\Address;
use App\Http\Entities\OrderMaster;
use App\Http\Entities\OrderDetails;
use App\Http\Entities\OrderDeliveryStatus;
use App\Http\Entities\ProductCoupon;
use App\Http\Entities\CouponUserMap;
use App\Http\Entities\Carrier;
use App\Http\Entities\Vendors;
use App\Http\Entities\Notifications;
use App\User;
use Razorpay\Api\Api;
use App\Common\Utility;
use App\Exports\OrdersExport;

class OrderController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	
	/**
     * Create New Order.
     * @param  Request $request
     * @return Response
    */
    public function placeOrder(Request $request)
    {
		try{
			$request_arr = $request->all();
			$user_id = $request_arr['userid'];
			$cart_id = $request_arr['cartid'];
			$address_id = $request_arr['addressid'];
			$payment_type = $request_arr['payment_type'];
			$coupon_code = isset($request_arr['coupon_code']) ? $request_arr['coupon_code'] : '';
			$txnid = "TXN".time().rand(10000,999999);			
			$data = new \stdClass();
			$message = "";
			$code = '';
			
			$coupon_details = array();
			if($coupon_code != ''){
				$ProductCouponObj = new ProductCoupon();
				$coupon_details = $ProductCouponObj->getCouponByCode($coupon_code);
			}
			
			$cart = new Cart();
			$cart_items = $cart->getAllItems($user_id, $cart_id);
			if(sizeof($cart_items)>=1){
				//$data['cart_items'] = $cart_items;
				$data_pdetails = $cart->getCartItemProcessed($cart_items,$cart_id);
				if(!$data_pdetails){
					$code = $this->successStatus;
					$message = 'The cart is empty.';
				}else{
					$data = array();
					// Get User Details
					$user = new User();
					$user_details = $user->find($user_id);

					$addresses = new Address();
					$addresses_arr = $addresses->getAddressById($address_id);
					$addres_str = $addresses_arr[0]->address . ", " . $addresses_arr[0]->city . ", " . $addresses_arr[0]->state . ", " . $addresses_arr[0]->zip;
					
					if(isset($addresses_arr[0]->landmark) && $addresses_arr[0]->landmark != ''){
						$addres_str .= ", " . $addresses_arr[0]->landmark;
					}
					
					// If Coupon Exist
					if(sizeof($coupon_details) > 0){
						$discount_amount = $coupon_details[0]->discount_amount;
						$discount_type = $coupon_details[0]->discount_type;
						$cart_total_amount = $data_pdetails['cart_total_amount'];
						$GST = "10";
						$delivery_fee = number_format('50',2);
						
						$revised_amount = $this->applyDiscount($discount_type, $discount_amount, $cart_total_amount, $GST, $delivery_fee);
						
						$order_detail_razor_pay = $this->getDynamicOrderId($txnid, sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $revised_amount['total_including_tax_delivery_fee_with_discount']))));

						if($order_detail_razor_pay){
							// Insert Order
							$order_id = $order_detail_razor_pay->id;
							$order_master = new OrderMaster();
							$order_master->order_id = $order_id;
							$order_master->transaction_id = $txnid;
							$order_master->amount_before_discount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $data_pdetails['total_including_tax_delivery'])));
							$order_master->coupon_code = $coupon_code;
							$order_master->coupon_descripition = $revised_amount['coupon_description'];
							$order_master->transaction_amount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $revised_amount['total_including_tax_delivery_fee_with_discount'])));
							$order_master->payment_type = $payment_type;
							if($payment_type == 'cod'){
								$order_master->transaction_status = "Cod";
							}
							$order_master->save();
							$order_master_id = $order_master->id;
							
							// insert Order Details
							$order_details = new OrderDetails();
							$order_details->order_id = $order_master->id;
							$order_details->cart_id = $cart_id;
							$order_details->order_amount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $revised_amount['total_after_discount'])));
							$order_details->tax_amount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $revised_amount['tax_amount'])));
							$order_details->delivery_amount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $delivery_fee)));
							$order_details->delivery_address = $addres_str;
							$order_details->save();
							
							$data['amount'] = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $revised_amount['total_including_tax_delivery_fee_with_discount'])));
							$data['name'] = $user_details->name;
							$data['email'] = $user_details->email;
							$data['phone'] = $user_details->mobile_number;
							$data['orderid'] = $order_id;
							$data['userid'] = $user_id;
							$data['cartid'] = $cart_id;
							$data['delivery_address'] = $addres_str;
							$data['transaction_id'] = $txnid;
							$data['id'] = $order_master->id;
							$data['payment_type'] = $payment_type;
							$data['coupon_code'] = $coupon_code;
							$data['coupon_descripition'] = $revised_amount['coupon_description'];
							
						}else{
							$code = $this->successStatus;
							$message = 'Problem with the payment.Please try again later.';
							$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
							return response()->json($response,200);
						}
						
					}else{
						$order_detail_razor_pay = $this->getDynamicOrderId($txnid, sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $data_pdetails['total_including_tax_delivery']))));
					
						if($order_detail_razor_pay){
							// Insert Order
							$order_id = $order_detail_razor_pay->id;
							$order_master = new OrderMaster();
							$order_master->order_id = $order_id;
							$order_master->transaction_id = $txnid;
							$order_master->transaction_amount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $data_pdetails['total_including_tax_delivery'])));							
							$order_master->amount_before_discount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $data_pdetails['total_including_tax_delivery'])));
							$order_master->payment_type = $payment_type;
							if($payment_type == 'cod'){
								$order_master->transaction_status = "Cod";
							}
							$order_master->save();
							$order_master_id = $order_master->id;
							
							// insert Order Details
							$order_details = new OrderDetails();
							$order_details->order_id = $order_master->id;
							$order_details->cart_id = $cart_id;
							$order_details->order_amount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $data_pdetails['cart_total_amount'])));
							$order_details->tax_amount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $data_pdetails['tax_amount'])));
							$order_details->delivery_amount = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $data_pdetails['delivery_fee'])));
							$order_details->delivery_address = $addres_str;							
							$order_details->save();
							
							$data['amount'] = sprintf("%.2f",floatval(preg_replace('/[^\d.]/', '', $data_pdetails['total_including_tax_delivery'])));
							$data['name'] = $user_details->name;
							$data['email'] = $user_details->email;
							$data['phone'] = $user_details->mobile_number;
							$data['orderid'] = $order_id;
							$data['userid'] = $user_id;
							$data['cartid'] = $cart_id;
							$data['delivery_address'] = $addres_str;
							$data['transaction_id'] = $txnid;
							$data['id'] = $order_master->id;
							$data['payment_type'] = $payment_type;
						}else{
							$code = $this->successStatus;
							$message = 'Problem with the payment.Please try again later.';
							$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
							return response()->json($response,200);
						}
						
					}
					
					if($payment_type == "cod"){
						// Update Cart Table
						$CartObj = new Cart();
						$CartObj->updateCartById($cart_id,"is_active",0);
						if($coupon_code != ''){
							
							$cart_details = $CartObj->getCartById($cart_id);
							$user_id = $cart_details[0]->customer_id;
							
							$CouponUserMapObj = new CouponUserMap();
							$CouponUserMapObj->user_id = $user_id;
							$CouponUserMapObj->coupon_code = $coupon_code;
							$CouponUserMapObj->created_at = date("Y-m-d H:i:s");
							$CouponUserMapObj->save();
						}
						
						// Notification on Cod
						$vendorObj = new Vendors();
						$vendor_details = $vendorObj->getVendorDetailsByOrderId($order_master_id);
						$vendor_id = $vendor_details->id;
						$device_id = '';
						$vendor_info = $vendorObj->getVendorById($vendor_id);
						$globalconfig = \config('globalconfig');
						$notification = $globalconfig['notification'];
						$title = $notification['place_order_vendor']['title'];
						$message = $notification['place_order_vendor']['message'];
						if(sizeof($vendor_info)>0){
							$device_id = $vendor_info[0]->device_id;
						}
						if($device_id != ''){							
							$notification_data = array(
														"click_action" => "FLUTTER_NOTIFICATION_CLICK",
														"orderid" => $order_master_id
													  );				
							$utility = new Utility;
							$utility->sendNotification($title,$message,$notification_data,$device_id);

							
						}
						// Admin Notification
						$NotificationsObj = new Notifications();
						$NotificationsObj->addNotification($notification['place_order_admin']['message'], $title);
					}
					
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
								'code' => $code
							  );

        } catch (Exception $e) {
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
	*
	* Get Order Details By Order Id
	*
	*/
	public function getOrderDetails(Request $request){		
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$order_id = $request_arr['orderid'];
			$odrMstr = new OrderMaster();
			$cart = new cart();
			$order_details = $odrMstr->getOrderById($order_id);
			if(sizeof($order_details)>=1){
				$data = array();
				$data['order_details'] = $order_details;
				$cart_id = $order_details[0]->cart_id;
				$cart_items = $cart->getAllItems('', $cart_id);
				$data['order_items'] = $cart_items;
				
				$code = $this->successStatus;
				$message = 'Success';
			}else{
				$code = $this->successStatus;
				$message = 'No records founds';
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
	* Get All orders
	*
	*/
	public function getAllOrders(Request $request){
		try{
			$input = $request->all();
			$isExport = isset($input['export']) && $input['export'] == 1 ? 1:0;
			$data = new \stdClass();
			$message = "";
			$code = '';
			$orderObj = new OrderMaster();
			$orders = $orderObj->getAllOrders();
			if(sizeof($orders)>=1){
				$data = array();
				$data = $orders;
				$message = "Success!";
				$code = $this->successStatus;	
				if($isExport){
					$export = new OrdersExport($orders->toArray());
					return Excel::download($export, 'orders.xlsx');
				}			
			}else{
				$code = $this->successStatus;
				$message = 'No records found.';
			}
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		return response()->json($response,200);
		
	}
	
	/**
	* Get All orderscfor User
	*
	*/
	public function getAllOrdersForUser(Request $request){
		try{
			$data = new \stdClass();
			$request_arr = $request->all();
			$user_id = $request_arr['userid'];
			$message = "";
			$code = '';
			$orderObj = new OrderMaster();
			$orders = $orderObj->getAllOrdersForUser($user_id);
			if(sizeof($orders)>=1){
				$data = array();
				$data = $orders;
				$message = "Success!";
				$code = $this->successStatus;				
			}else{
				$code = $this->successStatus;
				$message = 'No records found.';
			}
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	
	/**
	* Get All orders Not Accepted
	*
	*/
	public function getOrdersNotAccepted(Request $request){
		try{
			$input = $request->all();
			$isExport = isset($input['export']) && $input['export'] == 1 ? 1:0;
			$data = new \stdClass();
			$message = "";
			$code = '';
			$orderObj = new OrderMaster();
			$orders = $orderObj->getOrdersNotAccepted();
			if(sizeof($orders)>=1){
				$data = array();
				$data = $orders;
				$message = "Success!";
				$code = $this->successStatus;
				if($isExport){
					$export = new OrdersExport($orders->toArray());
					return Excel::download($export, 'orders.xlsx');
				}
			}else{
				$code = $this->successStatus;
				$message = 'No records found.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	/**
	* Get All orders Accepted
	*
	*/
	public function getOrdersAccepted(Request $request){
		try{
			$input = $request->all();
			$isExport = isset($input['export']) && $input['export'] == 1 ? 1:0;
			$data = new \stdClass();
			$message = "";
			$code = '';
			$orderObj = new OrderMaster();
			$orders = $orderObj->getOrdersAccepted();
			if(sizeof($orders)>=1){
				$data = array();
				$data = $orders;
				$message = "Success!";
				$code = $this->successStatus;
				if($isExport){
					$export = new OrdersExport($orders->toArray());
					return Excel::download($export, 'orders.xlsx');
				}
			}else{
				$code = $this->successStatus;
				$message = 'No records found.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	/**
	* Get All orders Rejected
	*
	*/
	public function getOrdersRejected(){
		try{
			$data = new \stdClass();
			$message = "";
			$code = '';
			$orderObj = new OrderMaster();
			$orders = $orderObj->getOrdersRejected();
			if(sizeof($orders)>=1){
				$data = array();
				$data = $orders;
				$message = "Success";
				$code = $this->successStatus;
				
			}else{
				$code = $this->successStatus;
				$message = 'No records found.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	
	/**
	* Get All orders By Vendor
	*
	*/
	public function getOrdersByVendor(Request $request){
		try{
			$data = new \stdClass();
			$request_arr = $request->all();
			$vendorid = $request_arr['vendorid'];
			$message = "";
			$code = '';
			$orderObj = new OrderMaster();
			$orders = $orderObj->getOrdersByVendor($vendorid);
			if(sizeof($orders)>=1){
				$data = array();
				$data = $orders;
				$message = "Success";
				$code = $this->successStatus;
				
			}else{
				$code = $this->successStatus;
				$message = 'No records found.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	
	public function getOrdersAcceptedNotAssigned(){
		try{
			$data = new \stdClass();
			$message = "";
			$code = '';
			$orderObj = new OrderMaster();
			$orders = $orderObj->getOrdersAcceptedNotAssigned();
			if(sizeof($orders)>=1){
				$data = array();
				$data = $orders;
				$message = "Success!";
				$code = $this->successStatus;
				
			}else{
				$code = $this->successStatus;
				$message = 'No records found.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	
	/**
	* Accept Order
	*
	*/
	public function orderAccept(Request $request){
		try{
			$data = new \stdClass();
			$message = "";
			$code = '';
			
			$input = $request->all();
			$orderid = $input['orderid'];
			$ordersObj = new OrderMaster();
			$userid = $ordersObj->getUserIdByOrderId($orderid);
			$orders = new OrderMaster();
			$orders = $orders::find($orderid);
			if($orders){
				$data = array();
				$orders->is_accepted = 1;
				$orders->updated_at = date("Y-m-d H:i:s");
				$orders->save();
				
				// sending Notification
				$globalconfig = \config('globalconfig');
				$notification = $globalconfig['notification'];
				$title = $notification['vendor_accept_order']['title'];
				$message = $notification['vendor_accept_order']['message'];
				$device_id = '';
				if($userid != ''){
					$userObj = new User();
					$device_id = $userObj->getDeviceId($userid);
				}
				if($device_id != ''){					
					$notification_data = array(
												"click_action" => "FLUTTER_NOTIFICATION_CLICK",
												"orderid" => $orderid
											  );				
					$utility = new Utility;
					$utility->sendNotification($title,$message,$notification_data,$device_id);
					
				}
				// Admin Notification
				$NotificationsObj = new Notifications();
				$NotificationsObj->addNotification($message, $title);
				
				$orders = $orders->getOrdersById($orderid);
				$data = $orders;
				$message = "Order accepted successfully.";
				$code = $this->successStatus;
				
			}else{
				$code = $this->successStatus;
				$message = 'No records found.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	
	/**
	* Reject Order
	*
	*/
	public function orderReject(Request $request){
		try{
			$data = new \stdClass();
			$message = "";
			$code = '';
			
			$input = $request->all();
			$orderid = $input['orderid'];
			$ordersObj = new OrderMaster();
			$userid = $ordersObj->getUserIdByOrderId($orderid);
			$orders = new OrderMaster();
			$orders = $orders::find($orderid);
			if($orders){
				$data = array();
				$orders->is_accepted = 2;
				$orders->updated_at = date("Y-m-d H:i:s");
				$orders->save();
				
				// sending Notification
				$device_id = '';
				$globalconfig = \config('globalconfig');
				$notification = $globalconfig['notification'];
				$title = $notification['vendor_reject_order']['title'];
				$message = $notification['vendor_reject_order']['message'];
				if($userid != ''){
					$userObj = new User();
					$device_id = $userObj->getDeviceId($userid);
				}
				if($device_id != ''){					
					$notification_data = array(
												"click_action" => "FLUTTER_NOTIFICATION_CLICK",
												"orderid" => $orderid
											  );				
					$utility = new Utility;
					$utility->sendNotification($title,$message,$notification_data,$device_id);
				}
				
				// Admin Notification
				$NotificationsObj = new Notifications();
				$NotificationsObj->addNotification($message, $title);
				
				$orders = $orders->getOrdersById($orderid);
				$data = $orders;
				$message = "Order rejected successfully.";
				$code = $this->successStatus;
				
			}else{
				$code = $this->successStatus;
				$message = 'No records found.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	
	/**
	*
	* Assign Delivery
	*
	*/
	public function orderAssignment(Request $request){
		try{
			$data = new \stdClass();
			$message = "";
			$code = '';
			
			$input = $request->all();
			$order_id = $input['orderid'];
			$carrier_id = $input['carrierid'];
			$orderStatus = new OrderDeliveryStatus();
			$orders = $orderStatus->findOrderStatusByOrderId($order_id);
			if(sizeof($orders) == 0){
				$data = array();
				$orderObj = new OrderDeliveryStatus();
				$orderObj->order_id = $order_id;
				$orderObj->carrier_id = $carrier_id;
				$orderObj->delivery_status = "Assigned";				
				$orderObj->save();
				
				$orderMstrObj = new OrderMaster();
				$orderMaster = $orderMstrObj::find($order_id);
				$orderMaster->is_assigned = 1;
				$orderMaster->updated_at = date("Y-m-d H:i:s");
				$orderMaster->save();
				
				// sending Notification
				$carrierObj = new Carrier();
				$carrier_details = $carrierObj->getCarrierById($carrier_id);
				$carrier_userid = '';
				if(sizeof($carrier_details)>0){
					$carrier_userid = $carrier_details[0]->user_id;
				}
				$device_id = '';
				$globalconfig = \config('globalconfig');
				$notification = $globalconfig['notification'];
				$title = $notification['order_assign_delivery_boy']['title'];
				$message = $notification['order_assign_delivery_boy']['message'];
				if($carrier_userid != ''){
					$userObj = new User();
					$device_id = $userObj->getDeviceId($carrier_userid);
				}
				if($device_id != ''){					
					$notification_data = array(
												"click_action" => "FLUTTER_NOTIFICATION_CLICK",
												"orderid" => $order_id
											  );				
					$utility = new Utility;
					$utility->sendNotification($title,$message,$notification_data,$device_id);
					
				}
				
				// Admin Notification
				$NotificationsObj = new Notifications();
				$NotificationsObj->addNotification($message, $title);
				
				// NOtification to Customer
				$ordersObj = new OrderMaster();
				$customer_userid = $ordersObj->getUserIdByOrderId($order_id);
				$device_id = '';
				$globalconfig = \config('globalconfig');
				$notification = $globalconfig['notification'];
				$title = $notification['order_assign_customer']['title'];
				$message = str_replace("delivery_boy_name",$carrier_details[0]->name,$notification['order_assign_customer']['message']);
				if($customer_userid != ''){
					$userObj = new User();
					$device_id = $userObj->getDeviceId($customer_userid);
				}
				if($device_id != ''){					
					$notification_data = array(
												"click_action" => "FLUTTER_NOTIFICATION_CLICK",
												"orderid" => $order_id
											  );				
					$utility = new Utility;
					$utility->sendNotification($title,$message,$notification_data,$device_id);					
				}
				
				// Admin Notification
				$NotificationsObj = new Notifications();
				$NotificationsObj->addNotification($message, $title);
				
				$ordersStatusDetails = $orderObj->getOrdersStatusByOrderId($order_id);
				$data = $ordersStatusDetails;
				$message = "Success!";
				$code = $this->successStatus;
				
			}else{
				$code = $this->successStatus;
				$message = 'The Order already assigned or has been delivered.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	public function getAllRecentOrders(Request $request)
    {
        try {
            $data = new \stdClass();
            $request_arr = $request->all();
            $user_id = $request_arr['userid'];
            $message = "";
            $code = '';
            $orderObj = new OrderMaster();
            $odrMstr = new OrderMaster();
            $cart = new Cart();
            $orders = $orderObj->getRecentOrdersForUser($user_id);

            if (sizeof($orders) >= 1) {
                foreach ($orders as $key => $order) {
                    $order->order_items = $cart->getAllRecentItems($order->cart_id);
                }
                $data = $orders;
                $message = "Success!";
                $code = $this->successStatus;
            } else {
                $code = $this->successStatus;
                $message = 'No records found.';
            }
            $response = array(
                'data' => $data,
                'message' => $message,
                'code' => $code,
            );
        } catch (Exception $e) {
            $response = array(
                'message' => 'Some internal error. Try after sometime.',
                'status' => $internalErrorStatus,
            );
        }

        return response()->json($response, 200);
    }
	/**
	*
	* Update Delivery Status
	*
	*/
	public function orderStatusUpdate(Request $request){
		try{
			$data = new \stdClass();
			$message = "";
			$code = '';
			
			$input = $request->all();
			$order_id = $input['orderid'];
			$delivery_status = $input['status'];
			$vendorObj = new Vendors();
			$orderStatus = new OrderDeliveryStatus();
			$orders = $orderStatus->findOrderStatusByOrderId($order_id);
			$vendor_details = $vendorObj->getVendorDetailsByOrderId($order_id);
			$shop_name = "Shop";
			if($vendor_details != ''){
				$shop_name = $vendor_details->shop_name;
				$vendor_id = $vendor_details->id;
			}
			if(sizeof($orders)>0){
				$data = array();
				
				$orderObj = new OrderDeliveryStatus();
				$orderObj->updateDeliveryStatusByOrderId($order_id,$delivery_status);
				
				$ordersStatusDetails = $orderObj->getOrdersStatusByOrderId($order_id);
				
				// Sending Notification
				$notfication_type_customer = "";
				$notfication_type_vendor = "";
				if($delivery_status == "Accepted"){
					$notfication_type_customer = "delivery_boy_accept_customer";
				}
				if($delivery_status == "Delivered"){
					$notfication_type_customer = "product_delivered_customer";
					$notfication_type_vendor = "product_delivered_vendor";
				}
				
				if($notfication_type_customer != ""){
					$ordersObj = new OrderMaster();
					$customer_userid = $ordersObj->getUserIdByOrderId($order_id);
					$device_id = '';
					$globalconfig = \config('globalconfig');
					$notification = $globalconfig['notification'];
					$title = $notification[$notfication_type_customer]['title'];
					$message = str_replace("shop_name",$shop_name,str_replace("delivery_boy_name",$ordersStatusDetails[0]->name,$notification[$notfication_type_customer]['message']));
					if($customer_userid != ''){
						$userObj = new User();
						$device_id = $userObj->getDeviceId($customer_userid);
					}
					if($device_id != ''){						
						$notification_data = array(
													"click_action" => "FLUTTER_NOTIFICATION_CLICK",
													"orderid" => $order_id
												  );				
						$utility = new Utility;
						$utility->sendNotification($title,$message,$notification_data,$device_id);
					}
					
					// Admin Notification
					$NotificationsObj = new Notifications();
					$NotificationsObj->addNotification($message, $title);
				}
				if($notfication_type_vendor != ''){
					$device_id = '';
					$vendor_info = $vendorObj->getVendorById($vendor_id);
					if(sizeof($vendor_info)>0){
						$device_id = $vendor_info[0]->device_id;
					}
					if($device_id != ''){
						$globalconfig = \config('globalconfig');
						$notification = $globalconfig['notification'];
						$title = $notification[$notfication_type_vendor]['title'];
						$message = str_replace("shop_name",$shop_name,str_replace("delivery_boy_name",$ordersStatusDetails[0]->name,$notification[$notfication_type_vendor]['message']));
						$notification_data = array(
													"click_action" => "FLUTTER_NOTIFICATION_CLICK",
													"orderid" => $order_id
												  );				
						$utility = new Utility;
						$utility->sendNotification($title,$message,$notification_data,$device_id);
					}
					
				}
				
				$data = $ordersStatusDetails;
				$message = "Success!";
				$code = $this->successStatus;
				
			}else{
				$code = $this->successStatus;
				$message = 'The Order already assigned or has been delivered.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
	}
	/**
	*
	* Update Delivery Status
	*
	*/
	public function orderPaymentStatusUpdate(Request $request){
		try{
			$data = new \stdClass();
			$message = "";
			$code = '';
			
			$input = $request->all();
			$transid = $input['transid'];
			$order_id = $input['orderid'];
			$status = $input['status'];
			$order = new OrderMaster();
			$orders = $order::find($order_id);
			if($orders){				
				$orderObj = new OrderMaster();
				// Update order Table and get Deatails
				$orderObj->updateOrderStatusByTransId($transid,$status);
				$orderDetails = $orderObj->getOrderById($order_id);
				$cart_id = $orderDetails[0]->cart_id;
				$payment_type = $orderDetails[0]->payment_type;
				$coupon_code = '';
				if($orderDetails[0]->coupon_code != ''){
					$coupon_code = $orderDetails[0]->coupon_code;
				}
				
				// Update Cart Table
				$CartObj = new Cart();
				$CartObj->updateCartById($cart_id,"is_active",0);
				if($coupon_code != ''){
					
					$cart_details = $CartObj->getCartById($cart_id);
					$user_id = $cart_details[0]->customer_id;
					
					$CouponUserMapObj = new CouponUserMap();
					$CouponUserMapObj->user_id = $user_id;
					$CouponUserMapObj->coupon_code = $coupon_code;
					$CouponUserMapObj->created_at = date("Y-m-d H:i:s");
					$CouponUserMapObj->save();
				}
				
				if($status == "Success"){
					if($payment_type == 'online'){
						$vendorObj = new Vendors();
						$vendor_details = $vendorObj->getVendorDetailsByOrderId($order_id);
						$vendor_id = $vendor_details->id;
						$device_id = '';
						$vendor_info = $vendorObj->getVendorById($vendor_id);
						$globalconfig = \config('globalconfig');
						$notification = $globalconfig['notification'];
						$title = $notification['place_order_vendor']['title'];
						$message = $notification['place_order_vendor']['message'];
						if(sizeof($vendor_info)>0){
							$device_id = $vendor_info[0]->device_id;
						}
						if($device_id != ''){							
							$notification_data = array(
														"click_action" => "FLUTTER_NOTIFICATION_CLICK",
														"orderid" => $order_id
													  );				
							$utility = new Utility;
							$utility->sendNotification($title,$message,$notification_data,$device_id);
						}
						
						// Admin Notification
						$NotificationsObj = new Notifications();
						$NotificationsObj->addNotification($message, $title);
					}
					
				}
				
				$message = "Order Updated";
				$code = $this->successStatus;
				
			}else{
				$code = $this->successStatus;
				$message = 'No orders Found.';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );
		} catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }		
		
		return response()->json($response,200);
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
		$revised_amount['discount_amount'] = $discounted_amount;
		$revised_amount['total_after_discount'] = $total_after_discount;
		$revised_amount['tax_amount'] = $tax_amount;
		$revised_amount['total_including_tax_with_discount'] = $total_including_tax_with_discount;
		$revised_amount['total_including_tax_delivery_fee_with_discount'] = $total_including_tax_delivery_fee_with_discount;
		
		return $revised_amount;
	}
	
	public function getDynamicOrderId($transaction_id, $amount){
		// getting global config
        $globalconfig = \config('globalconfig');
        $payment_credential = $globalconfig['payment_credential'];
		
		$api_key = $payment_credential['key_id'];
		$api_secret = $payment_credential['key_secret'];
		$api = new Api($api_key, $api_secret);
		
		$order = $api->order->create(array(
		  'receipt' => $transaction_id,
		  'amount' => $amount*100,
		  'currency' => 'INR'
		  )
		);
		
		return $order;
	}

	/* public function export() 
    {
		return Excel::download(new OrdersExport, 'orders.xlsx');
		//return (new OrdersExport)->download('orders.csv', \Maatwebsite\Excel\Excel::CSV);
    } */
}