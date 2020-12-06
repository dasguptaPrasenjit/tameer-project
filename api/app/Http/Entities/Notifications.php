<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use Notifiable;
    protected $table = 'notifications';
    
	public function getAllForAdmin(){
		$notifications = DB::table($this->table)
						 ->select('*')
						 ->where("notification_for","admin")
						 ->orderBy($this->table.'.created_at', 'DESC')
						 ->get();

        return $notifications;
	}
	
	public function getAllUnopenedCountForAdmin(){
		$notifications_counts = DB::select("SELECT 
											count(notifications.id) AS count
											FROM ".$this->table." WHERE is_opened = 0 AND notification_for = 'admin'"
										  );

        return $notifications_counts;
	}
	
	/**
     * Update Notification Table
     *
    */
    public function updateNotificationById($notification_id,$column,$value){
		
		DB::table('notifications')
                ->where('id', $notification_id)
                ->update([$column => $value]);

        return true;
    }
	
	public function addNotification($message, $title, $notification_for = 'admin', $user_id = NULL){
		DB::table('notifications')->insert(
			['message' => $message, 'title' => $title, 'notification_for' => $notification_for, 'user_id' => $user_id, "created_at" => date("Y-m-d H:i:s")]
		);
	}
}
