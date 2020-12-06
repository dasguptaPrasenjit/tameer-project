<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Entities\Vendors;
use App\Http\Entities\Pincode;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Common\Utility;
use App\User; 

class VendorController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getAllVendors(Request $request)
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$category_id = !empty($request_arr['category_id']) ? $request_arr['category_id'] : "";
			$vendors = new Vendors();
			$all_vendors = $vendors->getAll($category_id);
			if(sizeof($all_vendors)>=1){
				$data = $all_vendors;
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
    public function getVendorById(Request $request)
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$vendor_id = $request_arr['vendorid'];
			$vendors = new Vendors();
			$vendors_arr = $vendors->getVendorById($vendor_id);
			if(sizeof($vendors_arr)>=1){
				$data = $vendors_arr;
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
    public function getSales(Request $request)
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$vendor_id = $request_arr['vendorid'];
			$vendors = new Vendors();
			$is_available = 0;
			$yearly_sales = $vendors->getYearlySalesByVendorId($vendor_id);
			$salesyear = isset($request_arr['salesyear']) ? $request_arr['salesyear'] : '';
			if(sizeof($yearly_sales)>0){
				$data = array();
				$data['yearly'] = $yearly_sales;
				$code = $this->successStatus;
				$message = 'Success';
			}
			if($salesyear != ''){
				$monthly_sales = $vendors->getMonthlySalesByVendorId($vendor_id,$salesyear);
				if(sizeof($monthly_sales)>0){
					$data['monthly'] = $monthly_sales;
				}
			}
			
			if($is_available == 0){
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
     * Add Mobile Vendor api
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
				'mobile_number' => 'required|min:13|max:13'
			]);
			if ($validator->fails()) {
				$errors = $validator->errors();			
				$mobile_number_messages = $errors->get('mobile_number');
				
				if (!empty($mobile_number_messages)) {
					$response = array(
									'error' => true,
									'message' => $mobile_number_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			
			$input = $request->all();
			$input['mobile_verified_token'] = md5($mobile_verified_token);
			$vendor = new Vendors();
			$vendor->mobile_number = $input['mobile_number'];
			$vendor->mobile_verified_token = $input['mobile_verified_token'];
			if($vendor->save()){
				// sending password in mail not in development
				$environment = env('APP_ENV','production');
				if($environment == 'production'){
					//$this->sendMobileVerificationCode($mobile_verified_token,$input['mobile_number']);
				}
			}
			$message ="Success"; 
			$code = $this->successStatus;
			$response = array(
								'data' => $vendor, 
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
		$vendorObj = new Vendors();
		$userObj = new User();
		$user_table = $userObj->getTable();
		$data = array();
		$message = '';		
		$code = '';
		
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
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
        }
		$vendorObj = new Vendors();		
		$request_arr = $request->all(); 
		$vendorid = $request_arr['vendorid'];
		$vendor =  $vendorObj::find($vendorid);
        $input['password'] = bcrypt($request_arr['password']); 
		$input['email_verified_token'] = md5($email_verified_token);
		$input['mobile_number'] = $vendor->mobile_number;
		$input['email'] = $request_arr['email'];
		$input['name'] = $request_arr['name'];
		
        $user = User::create($input);
		
		$user->vendor_id = $vendorid;
		$user->mobile_verified_flag = 1;
		$user->save();
		
		// sending password in mail not in development
		$environment = env('APP_ENV','production');
		if($environment == 'production'){
			//$this->sendVerificationCode($input['name'],$email_verified_token,$input['email']);
		}		
		$data['token'] =  $user->createToken('foodDeliveryApp')-> accessToken; 
        $data['name'] =  $user->name;
        $data['id'] =  $user->id;
        $data['vendor_id'] = $vendorid;

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
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function registerByAdmin(Request $request) 
    {
		$userObj = new User();
		$user_table = $userObj->getTable();
		try {
			$validator = Validator::make($request->all(), [
				'mobile_number' => 'required|min:10|max:10',
				'shop_name' => 'required',
				'category_id' => 'required',
				'address' => 'required',
				'city' => 'required',
				'state' => 'required',
				'zip' => 'required',
				'name' => 'required', 
				'email' => 'required|email|unique:'.$user_table, 
				'password' => 'required',
				'confirm_password' => 'required|same:password',
				'vendor_image' => 'required|max:255'
			]);
			if ($validator->fails()) {
				return $this->error('ValidationError', $validator->errors());
			}

			$request_arr = $request->all();
			$mobile_verified_token = mt_rand(100000, 999999);
			$email_verified_token = mt_rand(100000, 999999);

			$vendor['mobile_number'] = "+91" . $request_arr['mobile_number'];
			$vendor['mobile_verified_token'] = md5($mobile_verified_token);
			$vendor['shop_name'] = $request_arr['shop_name'];
			$vendor['category_id'] = $request_arr['category_id'];
			$vendor['address'] = $request_arr['address'];
			$vendor['city'] = $request_arr['city'];
			$vendor['state'] = $request_arr['state'];
			$vendor['zip'] = $request_arr['zip'];
			$vendor['vendor_image'] = $request_arr['vendor_image'];
			$id = Vendors::create($vendor)->id;

			$input['password'] = bcrypt($request_arr['password']); 
			$input['email_verified_token'] = md5($email_verified_token);
			$input['mobile_number'] = "+91" . $request_arr['mobile_number'];
			$input['email'] = $request_arr['email'];
			$input['name'] = $request_arr['name'];
			$input['vendor_id'] = $id;
			$input['mobile_verified_flag'] = 1;
			//echo print_r($input);die;		
			$user = User::create($input);

			return $this->success($user, "Created successfully", 201);
		
		} catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
	}
	
	public function updateByAdmin(Request $request, $user_id) 
    {
		$userObj = new User();
		$user_table = $userObj->getTable();
		try {
			$validator = Validator::make($request->all(), [
				'mobile_number' => 'required|min:10|max:10',
				'shop_name' => 'required',
				'category_id' => 'required',
				'address' => 'required',
				'city' => 'required',
				'state' => 'required',
				'zip' => 'required',
				'name' => 'required',
				'vendor_image' => 'required|max:255'
			]);			
	
			$idValidator = Validator::make(['id' => $user_id], [
				'id' => 'required|integer|exists:users,id',
			]);
	
			if ($idValidator->fails() || $validator->fails()) {
				$errors = $validator->errors()->merge($idValidator->errors());
				return $this->error('ValidationError', $errors);
			}

			$request_arr = $request->all();

			$user = User::where('id', $user_id)->whereNotNull('vendor_id')->first();
			if($user){
				$user['name'] = $request_arr['name'];
				$user['mobile_number'] = "+91" . $request_arr['mobile_number'];
				$user->save();
	
				$vendor = Vendors::where('id', $user['vendor_id'])->first();
				$vendor['shop_name'] = $request_arr['shop_name'];
				$vendor['category_id'] = $request_arr['category_id'];
				$vendor['address'] = $request_arr['address'];
				$vendor['city'] = $request_arr['city'];
				$vendor['state'] = $request_arr['state'];
				$vendor['zip'] = $request_arr['zip'];
				$vendor['vendor_image'] = $request_arr['vendor_image'];
				$vendor['mobile_number'] = "+91" . $request_arr['mobile_number'];
				$vendor->save();
				return $this->success($user, "Updated successfully", 200);
			}
			return $this->error('ValidationError', 'User does not exists');
		
		} catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
    }
	
	/**
     * Add Address Vendor api
     *
     * @return \Illuminate\Http\Response
    */
    public function addAddress(Request $request)
    {
		$code = '';
		$message = '';
		try{
			$validator = Validator::make($request->all(), [
				'shop_name' => 'required',
				'category' => 'required',
				'address' => 'required',
				'city' => 'required',
				'state' => 'required',
				'zip' => 'required',
			]);
			if ($validator->fails()) {
				$errors = $validator->errors();			
				$shop_name_messages = $errors->get('shop_name');
				$category_messages = $errors->get('category');
				$address_messages = $errors->get('address');
				$city_messages = $errors->get('city');
				$state_messages = $errors->get('state');
				$zip_messages = $errors->get('zip');
				
				if (!empty($shop_name_messages)) {
					$response = array(
									'error' => true,
									'message' => $shop_name_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				
				if (!empty($category_messages)) {
					$response = array(
									'error' => true,
									'message' => $category_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($address_messages)) {
					$response = array(
									'error' => true,
									'message' => $address_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($city_messages)) {
					$response = array(
									'error' => true,
									'message' => $city_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($state_messages)) {
					$response = array(
									'error' => true,
									'message' => $state_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				if (!empty($zip_messages)) {
					$response = array(
									'error' => true,
									'message' => $zip_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			//$vendorObj = new Vendors();
			$request_arr = $request->all();
			//$vendorid = $request_arr['vendorid'];
			//$vendor =  $vendorObj::find($vendorid);
			$vendor = new Vendors();
			$vendor->shop_name = $request_arr['shop_name'];
			$vendor->category_id = $request_arr['category'];
			$vendor->address = $request_arr['address'];
			$vendor->city = $request_arr['city'];
			$vendor->state = $request_arr['state'];
			$vendor->zip = $request_arr['zip'];
			if($vendor->save()){
				$code = $this->successStatus;
				$message = 'Success';
				$response = array(
								'data' => $vendor, 
								'message' => $message,
								'status' => $this->successStatus
							 );
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
     * Vendor api
     *
     * @return \Illuminate\Http\Response
     */
    public function verifymobile(Request $request)
    {
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$vendorid = $request_arr['vendorid'];
			$token = md5($request_arr['token']);
			$vendorObj = new Vendors();
			
			$vendor =  $vendorObj::find($vendorid);
			if($vendor){
				if($vendor['mobile_verified_flag'] == 0){
					$mobile_verified_token = $vendor['mobile_verified_token'];
					if($mobile_verified_token == $token){
						$vendorObj->updateVendorById($vendorid,'mobile_verified_flag',1);
						$vendorObj->updateVendorById($vendorid,'mobile_verified_at',date("Y-m-d H:i:s"));	
						$vendor =  $vendorObj::find($vendorid);
						$code = $this->successStatus;
						$message = 'Mobile Number verified successfully!';
					}else{
						$code = 401;
						$message = 'Please enter correct security code!';
						$vendor = null;
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
								'data' => $vendor,
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
     * Delete Vendor api
     *
     * @return \Illuminate\Http\Response
    */
    public function delete(Request $request)
    {
		$input = $request->all();
		$vendorid = '';
		if(isset($input['vendorid']) && $input['vendorid'] != ''){
			$vendorid = $input['vendorid'];
			$vendor = Vendors::find($vendorid);
			$vendor->is_active = 0;
			$vendor->updated_at = date("Y-m-d H:i:s");
			$vendor->save();
			
			$vendor_details =  $vendor->getVendorById($vendorid);
			return response()->json(
									  [
										  'data' => $vendor_details,
										  'status' => $this-> successStatus,
										  'message' => "Vendor Deleted Successfully!"
									  ]
								   );
		}else{
			return response()->json(
									  [
										  'error' => "Please select a Vendor.",
										  'status' => $this-> successStatus,
									  ]
								   );
		}		
	}

	/**
     * Get Nearest Vendor api
     *
     * @return \Illuminate\Http\Response
    */
    public function getNearestSeller(Request $request)
    {
		try {
 
            $validator = Validator::make($request->all(), [
                'radius' => 'required|int',
                'pincode' => 'required|string',
                'distance_unit' => 'required|string'
            ]);
 
            if ($validator->fails()) {
 
                $errors = $validator->errors();
                $radius_messages = $errors->get('radius'); 
                $pincode_mesaage = $errors->get('pincode');
                $distance_unit_mesaage = $errors->get('distance_unit');
 
                if (!empty($radius_messages)) {
                    $response = array(
                        'error' => true,
                        'message' => $radius_messages[0],
                        'status' => 401
                    );
                    return response()->json($response);
                }
                if (!empty($pincode_mesaage)) {
                    $response = array(
                        'error' => true,
                        'message' => $pincode_mesaage[0],
                        'status' => 401
                    );
                    return response()->json($response);
                }
                if (!empty($distance_unit_mesaage)) {
                    $response = array(
                        'error' => true,
                        'message' => $distance_unit_mesaage[0],
                        'status' => 401
                    );
                    return response()->json($response);
                }
            }
            $seller_list = $this->findCloseZipcodes($request->pincode, $request->radius, $request->distance_unit);
            if (!empty($seller_list)) {
                $response = array(
                    'status' => true,
                    'data' => $seller_list,
                    'message' => 'List found and sent!',
 
                );
                return response()->json($response, $this->successStatus);
            } else {
                $seller_list = array();
                $response = array(
                    'status' => false,
                    'data' => $seller_list,
                    'message' => 'List not found',
 
                );
                return response()->json($response, $this->successStatus);
            }
        } catch (Exception $e) {
            $response = array(
                'message' => 'Some internal error. Try after sometime.',
                'code' => $this->internalErrorStatus
            );
        }	
	}
	
	function  findCloseZipcodes($origin_zipcode, $distance, $unit)
    {
        $vendors = array();
        // SWITCH BETWEEN KILOMETERS AND MILES
        if ($unit == "K" || $unit == "k") {
            $unit1 = 6378.39;
            $unit2 = 1;
        } else {
            $unit1 = 6378.39;
            $unit2 = 1.609344;
        }
 
        $origin_pincode_db_value = Pincode::where('pin_code', $origin_zipcode)->get(); 
        $lat = $origin_pincode_db_value[0]->latitude;
        $lng = $origin_pincode_db_value[0]->longitude;
			
		/*$nearest_pincodes = Pincode::select('vendors.*','pincodes.pin_code', 'pincodes.latitude', 'pincodes.longitude', DB::raw('(ACOS((SIN(RADIANS("' . $lat . '"))*SIN(RADIANS(pincodes.latitude))) + (COS(RADIANS( "' . $lat . '"))*COS(RADIANS(pincodes.latitude))*COS(RADIANS(pincodes.longitude)-RADIANS( "' . $lng . '")))) * ' . $unit1 . ') AS distance'))
            //->having('distance', '<', $distance)
			->where('vendors.zip', '!=', '')
            ->where('vendors.is_active', '=', '1')
            ->leftJoin('vendors', 'vendors.zip', '=', 'pincodes.pin_code')
            ->get();*/
			$vendorObj = new Vendors();
		$nearest_pincodes = $vendorObj->getNearestPincode($lat,$lng);
		foreach($nearest_pincodes as $key=>$val){
			print_r($val);
		}
		die();
		
        	
        $iteration = 0;
        foreach ($nearest_pincodes as $pincode) {
 
            $vendors[$iteration]["id"] = $pincode["id"];
            $vendors[$iteration]["mobile_number"] = $pincode["mobile_number"];
            $vendors[$iteration]["address"] = $pincode["address"];
            $vendors[$iteration]["city"] = $pincode["city"];
            $vendors[$iteration]["state"] = $pincode["state"];
            $vendors[$iteration]["zip"] = $pincode["zip"];
            $vendors[$iteration]["shop_name"] = $pincode["shop_name"];
            $vendors[$iteration]["distance"] = $pincode["distance"];
 
            $iteration++;
        }
        return $vendors;
	}
	
	public function getVendorByCategoryID(Request $request)
    {
        $data = array();
		$code = '';
		$message = '';
		try{
            $request_arr = $request->all();
			$category_id = $request_arr['categoryid'];
			$vendors = new Vendors();
			$all_vendors = $vendors->where('category_id', $category_id)->get();
			if(sizeof($all_vendors)>=1){
				$data = $all_vendors;
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
}