<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Common\Utility;
use App\Http\Entities\UserRole;

class RoleController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/** 
     * Get Role api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function getRole(){ 
        try{
			$data = new \stdClass();
			$message = "";
			$code = '';
			
			$userRole = new UserRole();
			$roles = $userRole->getAllRoles();
			if(sizeof($roles)>0){
				$data = array();
				$data = $roles;
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
     * Get Role api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function AssignRole(Request $request){ 
        try{
			$data = new \stdClass();
			$message = "";
			$code = '';
			
			$request_arr = $request->all();
			$user_id = $request_arr['userid'];
			$role_id = $request_arr['roleid'];

			UserRole::updateOrCreate(
										['user_id' => $user_id, 'role_id' => $role_id],
										['user_id' => $user_id, 'role_id' => $role_id]
									);
			// Get User Role
			$role = array();
			$data = array();
			$userRoleObj = new UserRole();
			$user_role_details = $userRoleObj->getUserRole($user_id);
			if(sizeof($user_role_details)>0){
				$data = $user_role_details;
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
}