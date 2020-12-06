<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Entities\Location;

class TrackerController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getPosition(Request $request)
    {
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$transactionid = isset($request_arr['transactionid']) ? $request_arr['transactionid'] : '';
			$location = new Location();
			$location_details = $location::find($transactionid);
			if($location_details){
				$data = array();
				$data = $location_details;
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
    * Set content
	* @param Request
    * @return Response
    */
    public function setPosition(Request $request)
    {		
		$data = new \stdClass();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$transactionid = isset($request_arr['transactionid']) ? $request_arr['transactionid'] : '';
			$longitude = isset($request_arr['longitude']) ? $request_arr['longitude'] : '';
			$latitude = isset($request_arr['latitude']) ? $request_arr['latitude'] : '';
			
			$location = new Location();
			$location_details = $location::updateOrCreate(
															['transid' => $transactionid],
															['latitude' => $latitude, 'longitude' => $longitude]
														 );
			
			$response = array(
								'data' => $data, 
								'message' => "Succes",
								'status' => 200
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