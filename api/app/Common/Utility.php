<?php
namespace App\Common;

use Twilio\Rest\Client;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
class Utility
{
	public function SendSms($mobile_numbers,$message){
	   
	   // getting global config
       $globalconfig = \config('globalconfig');
       $sms_config = $globalconfig['sms_config'];
	   
	   // Your Account SID and Auth Token from twilio.com/console
	   $sid = $sms_config['sid'];
	   $token = $sms_config['token'];
	   $from = $sms_config['from'];
	   
	   $client = new Client( $sid, $token );
	   $numbers_in_arrays = explode( ',' , $mobile_numbers );
	   $count = 0;

	   foreach( $numbers_in_arrays as $number )
	   {
		   $count++;
		   $client->messages->create(
			   $number,
			   [
				   'from' => $from,
				   'body' => $message,
			   ]
		   );
	   }
	}
	
	public function sendNotification($title,$message,$data,$device_id){
		try{
			$firebase = (new Factory)->withServiceAccount(public_path().'/Firebase.json');
			
			$messaging = $firebase->createMessaging();
			// Format Notification
			//$title = "Most Read Story: test title";
			//$message = "Click here to read the most read story on Novella today.";
			/*$data = [
				'story' => 1
			];
			'f_houcOvTVqhPF96sejU8b:APA91bEs8rwPNc9VN1UkDxcElUrg4IJLeENnFCBNQldATJeutP4XWQPLEPQLG4xHXRztqtYoeeUwt-Q3iz2u83dSgzPDbqm6MGLFXLHOS0ywA2WVaG8zU7NkZGKuZAPzldKEaInfqJzs'
			*/
			$imageUrl = 'http://deliveryontime.co.in/api/public/logo.jpeg';

			$notification = Notification::fromArray([
				'title' => $title,
				'body' => $message,
				'data' => $data,
				'image' => $imageUrl
			]);
			
			$message = CloudMessage::withTarget('token',$device_id)->withNotification($notification);
			$messaging->send($message);
		}catch(\Kreait\Firebase\Exception\Messaging\NotFound $e){
			
		}
	}
}
