<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Entities\Notifications;
use Validator;
use App\User; 

class NotificationController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getAdminRecentNotification()
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$Notifications = new Notifications();			
			$notifications = $Notifications->getAllForAdmin();
			if(sizeof($notifications)>0){
				$data['notifications'] = $Notifications->getAllForAdmin();
				$code = $this->successStatus;
				$message = 'Success';
				$all_notifications = $Notifications->getAllUnopenedCountForAdmin();
				if(sizeof($all_notifications)>0){
					if($all_notifications[0]->count>=1){					
						$data['notification_count'] = $all_notifications[0]->count;					
					}
				}
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
	
	public function openNotification(Request $request){
		try{
			
			$input = $request->all();
			$notification_id = $input['id'];
			
			$NotificationsObj = new Notifications();
			$NotificationsObj->updateNotificationById($notification_id,"is_opened","1");
			
			$response = array(
								'message' => "Notification status updated",
								'status' => 200
							 );
			
		}catch (Exception $e) {
			$response = array(
								'message' => "error in getting notifications",
								'status' => 200
							 );
        }
		
		return response()->json($response,200);
	}
}