<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Common\Utility;
use App\Http\Entities\Carrier;
use App\Http\Entities\Cart;
use App\Http\Entities\UserRole;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('foodDeliveryApp')-> accessToken;
			
			// Update device Id on login
			$device_id = request('deviceid') != null ? request('deviceid') : '';
			if($device_id != ''){
				$CarrierObj = new Carrier();
				$userObj = new User();
				$CarrierObj->updateCarrierById($user->carrier_id,'device_id',$device_id);
				$userObj->updateUserById($user->id,"device_id",$device_id);
			}
			
			// Login From Cart Checkout
			if($user->id){
				$cart_id = request('cartid') != null ? request('cartid') : '';
				if($cart_id != ''){
					$CartObj = new Cart();
					$CartObj->updateCartByUserId($user->id,"is_active",0);
					$CartObj->updateCartById($cart_id,"customer_id",$user->id);				
				}
				
				// Get User Role
				$role = array();
				$userRoleObj = new UserRole();
				$user_role_details = $userRoleObj->getUserRole($user->id);
				if(sizeof($user_role_details)>0){
					$role = $user_role_details;
				}
				
				// Get Updated user details
				$user_details =  User::find($user->id);
			}
			
            return response()->json(['success' => $success,'data' => $user_details,'role'=> $role, 'status'=>$this-> successStatus], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Login failed: Invalid Email and Password', 'status'=>401], 401); 
        } 
    }
	/**
	 * Change Password api 
     * 
     * @return \Illuminate\Http\Response 
	*/
	public function changePassword(Request $request){
		$user = Auth::user();
		$validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password'
        ]);
		if ($validator->fails()) {
			$errors = $validator->errors();
			$old_password_messages = $errors->get('old_password');
			$new_password_messages = $errors->get('new_password');
			$confirm_password_messages = $errors->get('confirm_password');
						
			if (!empty($old_password_messages)) {
				$response = array(
								'error' => true,
								'message' => $old_password_messages[0],
								'status' => 401
							 );
				return response()->json($response);
			}
			if (!empty($new_password_messages)) {
				$response = array(
								'error' => true,
								'message' => $new_password_messages[0],
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
		
		if ((Hash::check(request('old_password'), $user->password)) == false) {
			$response = array(
								'error' => true,
								'message' => 'Please check your current password.',
								'status' => 401
							 );
			return response()->json($response);
		}else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
			$response = array(
								'error' => true,
								'message' => "Please enter a password which is not similar to current password.",
								'status' => 401
							 );
			return response()->json($response);
        }else{
			$user->password = bcrypt(request('new_password'));
			$user->token()->revoke();
			$token = $user->createToken('foodDeliveryApp')->accessToken;
			$user->save();
            $response = array(
								'error' => true,
								'message' => "Password updated successfully.",
								'status' => 401
							 );
			return response()->json($response);
		}
		print_r($check);
	}
	/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
		$email_verified_token = mt_rand(100000, 999999);
		$mobile_verified_token = mt_rand(100000, 999999);
		$userObj = new User();
		$user_table = $userObj->getTable();
				
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email|unique:'.$user_table, 
            'password' => 'required', 
            'confirm_password' => 'required|same:password',
			'mobile_number' => 'required|min:13|max:13',
        ]);
		if ($validator->fails()) {
            //return response()->json(['error'=>$validator->errors()], 401);
			$errors = $validator->errors();
			$name_messages = $errors->get('name');
			$email_messages = $errors->get('email');
			$password_messages = $errors->get('password');
			$confirm_password_messages = $errors->get('confirm_password');
			$mobile_number_messages = $errors->get('mobile_number');
						
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

			
        }
		$input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
		$input['email_verified_token'] = md5($email_verified_token);
		$input['mobile_verified_token'] = md5($mobile_verified_token);

        $user = User::create($input);
		// sending password in mail not in development
		$environment = env('APP_ENV','production');
		if($environment == 'production'){
			//$this->sendVerificationCode($input['name'],$email_verified_token,$input['email']);
			//$this->sendMobileVerificationCode($input['name'],$mobile_verified_token,$input['mobile_number']);
		}		
        $success['token'] =  $user->createToken('foodDeliveryApp')-> accessToken; 
        $success['name'] =  $user->name;
        $success['id'] =  $user->id;
		if($user->id){
			$cart_id = isset($input['cartid']) ? $input['cartid'] : '';
			if($cart_id != ''){
				$CartObj = new Cart();
				$CartObj->updateCartById($cart_id,"customer_id",$user->id);
				$CartObj->updateCartById($cart_id,"is_active",1);
			}
		}
		return response()->json(['success'=>$success], $this-> successStatus); 
    }
	/** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    }
	/**
     * Details api
     *
     * @return \Illuminate\Http\Response
     */
    public function detailsbyid(Request $request)
    {
		$id = $request->route('id');
		$user =  User::find($id);
		return response()->json(['success' => $user], $this-> successStatus);
	}
	/**
     * Details api
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyemail(Request $request)
    {
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$userid = $request_arr['userid'];
			$token = md5($request_arr['token']);
			$userObj = new User();
			
			$user =  User::find($userid);
			if($user){
				if($user['email_verified_flag'] == 0){
					$email_verified_token = $user['email_verified_token'];
					if($email_verified_token == $token){
						$userObj->updateUserById($userid,'email_verified_flag',1);
						$userObj->updateUserById($userid,'email_verified_at',date("Y-m-d H:i:s"));	
						$user =  User::find($userid);
						$code = $this->successStatus;
						$message = 'Email verified successfully!';
					}else{
						$code = $this->successStatus;
						$message = 'Please enter correct security code!';
						$user = null;
					}
				}else{
					$code = $this->successStatus;
					$message = 'Email Id already verified!';
				}
			}else{
				$code = $this->successStatus;
				$message = 'No records founds';
			}
			
			$response = array(
								'success' => $user,
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
     * Details api
     *
     * @return \Illuminate\Http\Response
     */
    public function verifymobile(Request $request)
    {
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$userid = $request_arr['userid'];
			$token = md5($request_arr['token']);
			$userObj = new User();
			
			$user =  User::find($userid);
			if($user){
				if($user['mobile_verified_flag'] == 0){
					$mobile_verified_token = $user['mobile_verified_token'];
					if($mobile_verified_token == $token){
						$userObj->updateUserById($userid,'mobile_verified_flag',1);
						$userObj->updateUserById($userid,'mobile_verified_at',date("Y-m-d H:i:s"));	
						$user =  User::find($userid);
						$code = $this->successStatus;
						$message = 'Mobile verified successfully!';
					}else{
						$code = $this->successStatus;
						$message = 'Please enter correct security code!';
						$user = null;
					}
				}else{
					$code = $this->successStatus;
					$message = 'Mobile Number already verified!';
				}
			}else{
				$code = $this->successStatus;
				$message = 'No records founds';
			}
			
			$response = array(
								'success' => $user,
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
	public function sendMobileVerificationCode($name,$security_code,$to){        
    
        $message = 'Your Lazy Urban verification code: '.$security_code;        
        
        $utility = new Utility;
		$utility->SendSms($to, $message);
    }
	/**
     * Send verification code by email
     * 
     */
	public function sendVerificationCode($name,$security_code,$to){
        // getting global config
        $globalconfig = \config('globalconfig');
        $headers = $globalconfig['mail_headers'];
        
        $subject = "Vefification Code for Food Delivery App";        
        $message = '<html>
                    <head>
                    <title>Account Created</title>
                    </head>
                    <body>
                    <div style="width:640px;margin:0 auto;background:#fff"><div >
						</div><div style="min-height:5px;background-color:#ffcd53" >
								<span style="min-height:5px;background-color:#0b638c;width:100px;display:block"></span>
						</div><div >
						</div><div align="center"><div >
							</div><h2 style="color:#0b638c;font-weight:normal;text-align:center;font-size:40px">'.$security_code.'</h2>
							<small align="center" style="color:grey;display:block;margin:30px 0">Use the above code to verify your email account</small>
							<small style="padding:15px 0;color:grey;font-size:15px;display:block;width:100%">If not you then please ignore.</small>
						</div>
					</div>
                    </body>
                    </html>';        
        
        mail($to,$subject,$message,$headers);
    }
}