<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Entities\Carrier;
use App\Http\Entities\CarrierReport;
use Validator;
use App\Common\Utility;
use App\User; 
use Datetime;

class CarrierController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getAllCarrier()
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$carrier = new Carrier();
			$all_carriers = $carrier->getAll();
			if(sizeof($all_carriers)>=1){
				$data = $all_carriers;
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
    * Display a listing of the resource.
    * @return Response
    */
    public function getCarrierById(Request $request)
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$carrier_id = $request_arr['carrierid'];
			$carrier = new Carrier();
			$carrier_arr = $carrier->getCarrierById($carrier_id);
			if(sizeof($carrier_arr)>=1){
				$data = $carrier_arr;
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
     * Add Mobile Delivery Boy api
     *
     * @return \Illuminate\Http\Response
    */
    public function addMobile(Request $request)
    {
		$code = '';
		$message = '';
		$mobile_verified_token = mt_rand(100000, 999999);
		try{
			$validator = Validator::make($request->all(), [
				'mobile_number' => 'required|min:13|max:13',
				'device_id' => 'required'
			]);
			if ($validator->fails()) {
				$errors = $validator->errors();			
				$mobile_number_messages = $errors->get('mobile_number');
				$device_id_messages = $errors->get('device_id');
				
				if (!empty($mobile_number_messages)) {
					$response = array(
									'error' => true,
									'message' => $mobile_number_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($device_id_messages)) {
					$response = array(
									'error' => true,
									'message' => $device_id_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			
			$input = $request->all();
			$input['mobile_verified_token'] = md5($mobile_verified_token);
			$carrier = new Carrier();
			$carrier->mobile_number = $input['mobile_number'];
			$carrier->mobile_verified_token = $input['mobile_verified_token'];
			$carrier->device_id = $input['device_id'];
			if($carrier->save()){
				// sending password in mail not in development
				$environment = env('APP_ENV','production');
				if($environment == 'production'){
					//$this->sendMobileVerificationCode($mobile_verified_token,$input['mobile_number']);
				}
			}
			$message ="Success"; 
			$code = $this->successStatus;
			$response = array(
								'data' => $carrier, 
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
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
		$email_verified_token = mt_rand(100000, 999999);
		$userObj = new User();
		$user_table = $userObj->getTable();
		$data = array();
		$message = '';		
		$code = '';
		$mobile_verified_token = mt_rand(100000, 999999);
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'mobile_number' => 'required|min:13|max:13',
			'device_id' => 'required',
            'email' => 'required|email|unique:'.$user_table, 
            'password' => 'required', 
            'confirm_password' => 'required|same:password'
        ]);
		if ($validator->fails()) {
            //return response()->json(['error'=>$validator->errors()], 401);
			$errors = $validator->errors();
			$name_messages = $errors->get('name');
			$email_messages = $errors->get('email');
			$password_messages = $errors->get('password');
			$confirm_password_messages = $errors->get('confirm_password');
			$mobile_number_messages = $errors->get('mobile_number');
			$device_id_messages = $errors->get('device_id');
						
			if (!empty($name_messages)) {
				$response = array(
								'error' => true,
								'message' => $name_messages[0],
								'status' => 401
							 );
				return response()->json($response);
			}
			if (!empty($email_messages)) {
				$response = array(
								'error' => true,
								'message' => $email_messages[0],
								'status' => 401
							 );
				return response()->json($response);
			}
			if (!empty($password_messages)) {
				$response = array(
								'error' => true,
								'message' => $password_messages[0],
								'status' => 401
							 );
				return response()->json($response);
			}
			if (!empty($confirm_password_messages)) {
				$response = array(
								'error' => true,
								'message' => $confirm_password_messages[0],
								'status' => 401
							 );
				return response()->json($response);
			}
			if (!empty($mobile_number_messages)) {
				$response = array(
								'error' => true,
								'message' => $mobile_number_messages[0],
								'status' => 401
							 );
				return response()->json($response);
			}
			if (!empty($device_id_messages)) {
					$response = array(
									'error' => true,
									'message' => $device_id_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
        }
		$input = $request->all();
		$input['mobile_verified_token'] = md5($mobile_verified_token);
		$carrier = new Carrier();
		$carrier->mobile_number = $input['mobile_number'];
		$carrier->mobile_verified_token = $input['mobile_verified_token'];
		$carrier->device_id = $input['device_id'];
		if($carrier->save()){
			$carrierid = $carrier->id;
		}
		$input = array();
		$CarrierObj = new Carrier();	
		$request_arr = $request->all();
		$carrier =  $CarrierObj::find($carrierid);
        $input['password'] = bcrypt($request_arr['password']); 
		$input['email_verified_token'] = md5($email_verified_token);
		$input['mobile_number'] = $carrier->mobile_number;
		$input['email'] = $request_arr['email'];
		$input['name'] = $request_arr['name'];
		
        $user = User::create($input);
		
		$user->carrier_id = $carrierid;
		$user->mobile_verified_flag = 1;
		$user->device_id = $carrier->device_id;
		$user->save();
		
		// sending password in mail not in development
		$environment = env('APP_ENV','production');
		if($environment == 'production'){
			//$this->sendVerificationCode($input['name'],$email_verified_token,$input['email']);
		}		
		$data['token'] =  $user->createToken('foodDeliveryApp')-> accessToken; 
        $data['name'] =  $user->name;
        $data['id'] =  $user->id;
        $data['carrier_id'] = $carrierid;

		$message ="Success"; 
		$code = $this->successStatus;
		$response = array(
							'data' => $data, 
							'message' => $message,
							'status' => $code
						 );
	
		return response()->json($response,200);
    }
	/**
     *  carrier api
     *
     * @return \Illuminate\Http\Response
     */
    public function verifymobile(Request $request)
    {
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$carrierid = $request_arr['carrierid'];
			$token = md5($request_arr['token']);
			$CarrierObj = new Carrier();
			
			$carrier =  $CarrierObj::find($carrierid);
			if($carrier){
				if($carrier['mobile_verified_flag'] == 0){
					$mobile_verified_token = $carrier['mobile_verified_token'];
					if($mobile_verified_token == $token){
						$CarrierObj->updateCarrierById($carrierid,'mobile_verified_flag',1);
						$CarrierObj->updateCarrierById($carrierid,'mobile_verified_at',date("Y-m-d H:i:s"));	
						$carrier =  $CarrierObj::find($carrierid);
						$code = $this->successStatus;
						$message = 'Mobile Number verified successfully!';
					}else{
						$code = 401;
						$message = 'Please enter correct security code!';
						$carrier = null;
					}
				}else{
					$code = 401;
					$message = 'Mobile Number already verified!';
				}
			}else{
				$code = $this->successStatus;
				$message = 'No records founds';
			}
			
			$response = array(
								'data' => $carrier,
								'message' => $message,
								'status' => $code
							 );
		
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'code' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
	}
	/**
     * Send verification code by Mobile
     * 
     */
	public function sendMobileVerificationCode($security_code,$to){        
    
        //$message = 'Your Lazy Urban verification code: '.$security_code;        
        $message = '<#> Apnidukan: Your code is '.$security_code.' FgijyZcOmqG';        
        
        $utility = new Utility;
		$utility->SendSms($to, $message);
    }	
	/**
     * Delete Carrier api
     *
     * @return \Illuminate\Http\Response
    */
    public function delete(Request $request)
    {
		$input = $request->all();
		$carrierid = '';
		if(isset($input['carrierid']) && $input['carrierid'] != ''){
			$carrierid = $input['carrierid'];
			$carrierObj = new Carrier();
			$carrier = $carrierObj->find($carrierid);
			$carrier->is_active = 0;
			$carrier->updated_at = date("Y-m-d H:i:s");
			$carrier->save();
			
			$carrier_details =  $carrier->getCarrierById($carrierid);
			return response()->json(
									  [
										  'data' => $carrier_details,
										  'status' => $this-> successStatus,
										  'message' => "Delivery Person Deleted Successfully!"
									  ]
								   );
		}else{
			return response()->json(
									  [
										  'error' => "Please select a Delivery Person.",
										  'status' => $this-> successStatus,
									  ]
								   );
		}		
	}

	public function restore(Request $request)
    {
		$input = $request->all();
		$carrierid = '';
		if(isset($input['carrierid']) && $input['carrierid'] != ''){
			$carrierid = $input['carrierid'];
			$carrierObj = new Carrier();
			$carrier = $carrierObj->find($carrierid);
			$carrier->is_active = 1;
			$carrier->updated_at = date("Y-m-d H:i:s");
			$carrier->save();
			
			$carrier_details =  $carrier->getCarrierById($carrierid);
			return response()->json(
									  [
										  'data' => $carrier_details,
										  'status' => $this->successStatus,
										  'message' => "Delivery Person Restored Successfully!"
									  ]
								   );
		}else{
			return response()->json(
									  [
										  'error' => "Please select a Delivery Person.",
										  'status' => $this->successStatus,
									  ]
								   );
		}		
	}
	
	/**
     * Availablity Carrier api
     *
     * @return \Illuminate\Http\Response
    */
    public function availablity(Request $request)
    {
		$input = $request->all();
		$carrierid = '';
		if(isset($input['carrierid']) && $input['carrierid'] != ''){
			$carrierid = $input['carrierid'];
			$carrierObj = new Carrier();
			$carrier = $carrierObj->find($carrierid);
			$carrier->is_available = $input['is_available'];
			$carrier->updated_at = date("Y-m-d H:i:s");
			$carrier->save();
			
			$carrier_details =  $carrier->getCarrierById($carrierid);
			return response()->json(
									  [
										  'data' => $carrier_details,
										  'status' => $this-> successStatus,
										  'message' => "Delivery Person Deleted Successfully!"
									  ]
								   );
		}else{
			return response()->json(
									  [
										  'error' => "Please select a Delivery Person.",
										  'status' => $this-> successStatus,
									  ]
								   );
		}		
	}
	
	/**
     * Get Available Carrier
     *
     * @return \Illuminate\Http\Response
    */
    public function getAvailableCarrier()
    {
		$data = [];
		
		// getting global config
		$globalconfig = \config('globalconfig');
		$no_of_orders_per_carrier = $globalconfig['no_of_orders_per_carrier'];

		$carrierObj = new Carrier();
		$carriers = $carrierObj->getAvailableCarrier();
		if(sizeof($carriers)>0){
			$data = array();
			foreach($carriers as $carrier){
				if($carrier->no_of_active_orders < $no_of_orders_per_carrier){
					$data[] = $carrier;
				}
			}
		}
		return response()->json(
								  [
									  'data' => $data,
									  'status' => $this-> successStatus,
									  'message' => "Success"
								  ]
							   );	
	}
	/**
     * Get Carrier Order Details
     *
     * @return \Illuminate\Http\Response
    */
	public function orderDetails(Request $request){
		$carrierObj = new Carrier();
		$input = $request->all();
		$order_delivery_status = $input['status'];
		$carrier_id = $input['carrier_id'];
		
		$orderMaster = $carrierObj->getCarrierOrderDetails($order_delivery_status, $carrier_id);
		$ordersDetails = array();
		$i = 0;
		foreach($orderMaster as $master){
			$ordersDetails[$i]['order_details'] = $master;
			$order_id = $master->id;
			
			$cart_delivery_add_arr = $carrierObj->getCartDeliveryAddress($order_id);
			$cart_id = $cart_delivery_add_arr[0]->cart_id;
			$delivery_address = $cart_delivery_add_arr[0]->delivery_address;
			$ordersDetails[$i]['delivery_address'] = $delivery_address;
			
			$customer_details = $carrierObj->getCustomerDetails($cart_id);
			$ordersDetails[$i]['cutomer_name'] = $customer_details[0]->name;
			$ordersDetails[$i]['cutomer_email'] = $customer_details[0]->email;
			$ordersDetails[$i]['cutomer_mobile_number'] = $customer_details[0]->mobile_number;
			
			$i++;
		}
		if(sizeof($ordersDetails) >=1){
			return response()->json(
									  [
										  'data' => $ordersDetails,
										  'status' => $this-> successStatus,
										  'message' => "Order details!"
									  ]
								   );
		}else{
			return response()->json(
									  [
										  'data' => $ordersDetails,
										  'status' => $this-> successStatus,
										  'message' => "No records found!"
									  ]
								   );
		}
	}

	public function setLocation(Request $request){
		$validator = Validator::make($request->all(), [
            'id' => 'required|exists:carrier,id',
			'latitude' => 'required',
			'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error('ValidationError', $validator->errors());
		}
		try {
			$data = $request->all();
			$carrier = Carrier::where('id', $data['id'])->update([
				'latitude' => $data['latitude'],
				'longitude' => $data['longitude']
			]);
			if($carrier){
				return $this->success($data, "Location updated successfully", 200);
			} else {
                return $this->error('ValidationError', 'Location not updated');
            }
		} catch (Exception $e) {
			return $this->error('Some internal error', null, 500);
		}
	}

	public function getLocation($id){
		$validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:carrier,id'
        ]);

        if ($validator->fails()) {
            return $this->error('ValidationError', $validator->errors());
		}
		try {
			$carrier = Carrier::select(['latitude', 'longitude'])->where('id', $id)->first();
			if($carrier){
				return $this->success($carrier, "Location received successfully", 200);
			} else {
                return $this->error('ValidationError', 'Carrier not found');
            }
		} catch (Exception $e) {
			return $this->error('Some internal error', null, 500);
		}
	}

	/**
     * Add carrier documents
     * @return Response
     */
    public function addDocuments(Request $request)
    {
        $request_arr = $request->all();
        try {
            $validator = Validator::make($request_arr, [
                'carrier_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->error('ValidationError', $validator->errors());
            }

            $carrierObj = new Carrier();
            $carrier_id = $request_arr['carrier_id'];
            $proof_vehicle_no = isset($request_arr['proof_vehicle_no']) ? $request_arr['proof_vehicle_no'] : null;
            $proof_photo = isset($request_arr['proof_photo']) ? $request_arr['proof_photo'] : null;
            $proof_address = isset($request_arr['proof_address']) ? $request_arr['proof_address'] : null;

            $carrierObj
                ->where('id', $carrier_id)
                ->update([
                    'proof_vehicle_no' => $proof_vehicle_no,
                    'proof_photo' => $proof_photo,
                    'proof_address' => $proof_address,
                ]);

            return $this->success($request_arr, "Updated successfully", 200);

        } catch (Exception $e) {
            return $this->error('Unable to update', $request_arr);
        }

    }

    public function addReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'carrier_id' => ['required'],
            'reported_proof_vehicle_no' => [Rule::in([0, 1])],
            'reported_proof_photo' => [Rule::in([0, 1])],
            'reported_proof_address' => [Rule::in([0, 1])],
        ]);

        if ($validator->fails()) {
            return $this->error('ValidationError', $validator->errors());
        }

        try {
            $data = $request->all();
            $now = new DateTime();
            $data['raised_on'] = $now->format('Y-m-d H:i:s');
            $result = CarrierReport::create($data);
            if ($result) {
                return $this->success($result, "Created successfully", 201);
            } else {
                return $this->error('Unable to create', $data);
            }
        } catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
    }
}