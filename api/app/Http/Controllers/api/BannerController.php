<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Entities\Banner;
use Validator;
use App\User; 

class BannerController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getAllBanners()
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$banner = new Banner();
			$all_banner = $banner->all();
			if(sizeof($all_banner)>=1){
				$data = array();
				$data = $all_banner;
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
    public function getActiveBanners()
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$banner = new Banner();
			$active_banner = $banner->activeBanners();
			if(sizeof($active_banner)>=1){
				$data = array();
				$data = $active_banner;
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
    public function getBannerById(Request $request)
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		$input = $request->all();
		try{
			$banner = new Banner();			
			if(isset($input['bannerid']) && $input['bannerid'] != ''){				
				$banner_id = $input['bannerid'];
				$banner = $banner->getBannerById($banner_id);
				if(sizeof($banner)>=1){
					$data = array();
					$data = $banner;
					$code = $this->successStatus;
					$message = 'Success';
				}else{
					$code = $this->successStatus;
					$message = 'No records founds';
				}
			}else{
				return response()->json(
										  [
											  'error' => "Please select a Banner.",
											  'status' => $this-> successStatus,
										  ]
									   );
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
     * Add Banner api
     *
     * @return \Illuminate\Http\Response
    */
	public function addBanner(Request $request)
    {
		$data = new \stdClass();
		$code = '';
		$message = '';		
		try{			
			$validator = Validator::make($request->all(), [
				'name' => 'required',
				'image' => 'required'
			]);
			
			if ($validator->fails()) {
				$errors = $validator->errors();			
				$name_messages = $errors->get('name');
				$image_messages = $errors->get('image');
				
				if (!empty($name_messages)) {
					$response = array(
									'error' => true,
									'message' => $name_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
				
				if (!empty($image_messages)) {
					$response = array(
									'error' => true,
									'message' => $image_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			$input = $request->all();
			$banner = new Banner();
			$banner->name = $input['name'];
			$banner->image = $input['image'];
			if($banner->save()){
				$data = array();
				$code = $this->successStatus;
				$message = 'Banner saved successfully!';
				$data = $banner;
				$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
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
     * Delete Banner api
     *
     * @return \Illuminate\Http\Response
    */
    public function delete(Request $request)
    {
		$input = $request->all();
		$bannerid = '';
		if(isset($input['bannerid']) && $input['bannerid'] != ''){
			$bannerid = $input['bannerid'];
			$banner = Banner::find($bannerid);
			$banner->is_deleted = 1;
			$banner->updated_at = date("Y-m-d H:i:s");
			$banner->save();
			
			$banner_details =  $banner->getBannerById($bannerid);
			return response()->json(
									  [
										  'data' => $banner_details,
										  'status' => $this-> successStatus,
										  'message' => "Banner Deleted Successfully!"
									  ]
								   );
		}else{
			return response()->json(
									  [
										  'error' => "Please select a local.",
										  'status' => $this-> successStatus,
									  ]
								   );
		}		
	}
	/**
     * Update Banner api
     *
     * @return \Illuminate\Http\Response
    */
    public function updateBanner(Request $request)
    {
		$input = $request->all();
		$bannerid = '';
		if(isset($input['bannerid']) && $input['bannerid'] != ''){
			$bannerid = $input['bannerid'];
			$is_active = $input['is_active'];
			$banner = Banner::find($bannerid);
			$banner->is_active = $is_active;
			$banner->updated_at = date("Y-m-d H:i:s");
			$banner->save();
			
			$banner_details =  $banner->getBannerById($bannerid);
			return response()->json(
									  [
										  'data' => $banner_details,
										  'status' => $this-> successStatus,
										  'message' => "Banner Updated Successfully!"
									  ]
								   );
		}else{
			return response()->json(
									  [
										  'error' => "Please select a local.",
										  'status' => $this-> successStatus,
									  ]
								   );
		}		
	}
}