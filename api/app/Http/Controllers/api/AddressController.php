<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Entities\Address;
use Validator;

class AddressController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getAllAddress(Request $request)
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$user_id = $request_arr['userid'];
			$addresses = new Address();
			$all_addresses = $addresses->getAll($user_id);
			if(sizeof($all_addresses)>=1){
				$data = $all_addresses;
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
    public function getAddressById(Request $request)
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$address_id = $request_arr['addressid'];
			$addresses = new Address();
			$addresses_arr = $addresses->getAddressById($address_id);
			if(sizeof($addresses_arr)>=1){
				$data = $addresses_arr;
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
     * Store Address api
     *
     * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
		$code = '';
		$message = '';
		try{
			$validator = Validator::make($request->all(), [
				'address' => 'required',
				'city' => 'required',
				'state' => 'required',
				'zip' => 'required'
			]);
			if ($validator->fails()) {
				$errors = $validator->errors();
				$address_messages = $errors->get('address');
				$city_messages = $errors->get('city');
				$state_messages = $errors->get('state');
				$zip_messages = $errors->get('zip');
				
				if (!empty($address_messages)) {
					return response()->json(['error'=>$address_messages[0]], 401);
				}
				if (!empty($city_messages)) {
					return response()->json(['error'=>$city_messages[0]], 401);
				}
				if (!empty($state_messages)) {
					return response()->json(['error'=>$state_messages[0]], 401);
				}
				if (!empty($zip_messages)) {
					return response()->json(['error'=>$zip_messages[0]], 401);
				}
			}
			
			$input = $request->all();
			$addressid = '';
			$message = '';
			if(isset($input['addressid']) && $input['addressid'] != ''){
				$addressid = $input['addressid'];
			}
			if($addressid != ''){
				$address = Address::find($addressid);
				$address->updated_at = date("Y-m-d H:i:s");
				$message = "Address updated successfully!";
				$code = $this->successStatus;
			}else{
				$address = new Address;
				$address->created_at = date("Y-m-d H:i:s");
				$message = "Address added successfully!";
				$code = $this->successStatus;
			}
			$address->address = isset($input['address']) ? $input['address'] : '';
			$address->landmark = isset($input['landmark']) ? $input['landmark'] : '';
			$address->zip = isset($input['zip']) ? $input['zip'] : '';
			$address->city = isset($input['city']) ? $input['city'] : '';
			$address->state = isset($input['state']) ? $input['state'] : '';
			$address->user_id = isset($input['userid']) ? $input['userid'] : '';
			
			$address_by_user = $address->getAddressCountByUserId($input['userid']);
			if(count($address_by_user) > 0){
				$address->address_name = isset($input['address_name']) ? $input['address_name'] : 'Other';
				if(isset($input['is_default']) && $input['is_default'] != 0){
					$address_def = new Address();
					$address_def::where("user_id",$input['userid'])->update(['is_default' => 0]);
					$address->is_default = 1;
				}else{
					$address->is_default = 0;
				}
			}else{
				$address->address_name = isset($input['address_name']) ? $input['address_name'] : 'Default';
			}
			$address->save();

			$response = array(
								'data' => $address, 
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
     * Delete Address api
     *
     * @return \Illuminate\Http\Response
    */
    public function delete(Request $request)
    {
		$input = $request->all();
		$address_id = '';
		if(isset($input['addressid']) && $input['addressid'] != ''){
			$address_id = $input['addressid'];
			$address = Address::find($address_id);
			$address->is_active = 0;
			$address->updated_at = date("Y-m-d H:i:s");
			$address->save();
			
			$address_details =  $address->getAddressById($address_id);
			return response()->json(
									  [
										  'data' => $address_details,
										  'status' => $this-> successStatus,
										  'message' => "Address Deleted Successfully!"
									  ]
								   );
		}else{
			return response()->json(
									  [
										  'error' => "Please select a address.",
										  'status' => $this-> successStatus,
									  ]
								   );
		}		
	}
}