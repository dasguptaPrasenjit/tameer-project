<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Validator;

class UtilityController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	
	/**
     * Upload logo.
     * @param  Request $request
     * @return Response
    */
    public function upload(Request $request)
    {
		try{			
			$validator = Validator::make($request->all(), [ 
				'type' => 'required',
				'filenames' => 'required',
				'filenames.*' => 'mimes:jpg,jpeg,png'
			]);
			if ($validator->fails()) {
				$errors = $validator->errors();
				$type_error = $errors->get('type');
				$filenames_error = $errors->get('filenames');
				if(!empty($type_error)){
					$response = array(
									'error' => true,
									'message' => $type_error[0],
									'status' => 401
								 );
					return response()->json($response);
				}else if(!empty($filenames_error)){
					$response = array(
									'error' => true,
									'message' => $filenames_error[0],
									'status' => 401
								 );
					return response()->json($response);
				}else{
					$response = array(
									'error' => true,
									'message' => "File extension should be jpg,jpeg and png.",
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			$request_arr = $request->all();
			$type = $request_arr['type'];
			$data = array();
			$message = "";
			$code = '';
			$files = $request->file('filenames');
			if($request->hasfile('filenames')){				
				foreach($files as $file){
					$rel_path = '/uploads/'.$type;
					$path = public_path() . $rel_path;
					$new_filename = time().".".str_replace(" ","_",$file->getClientOriginalName());
					$file->move($path, $new_filename);
					$data[] = $rel_path."/".$new_filename;
				}
				$message = "File uploaded successfully.";
				$code = $this->successStatus;
			 }
			 $response = array(
								'data' => $data, 
								'message' => $message,
								'code' => $code
							  );

        } catch (Exception $e) {
            $response = array('type'=>2, 'message'=>'Some internal error. Try after sometime.');
        }
		
		return response()->json($response,200);
	}
}