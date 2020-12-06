<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','mobile_number','email_verified_token','mobile_verified_token',
        'vendor_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	/**
     * Update User Table
     *
    */
    public function updateUserById($userid,$column,$value){
		
		DB::table('users')
                ->where('id', $userid)
                ->update([$column => $value]);

        return true;
    }
	
	public function getDeviceId($userId){
		$user_device = DB::select("SELECT device_id from users WHERE id = ".$userId);
		if(sizeof($user_device)>0){
			return $user_device[0]->device_id;
		}else{
			return '';
		}
	}
}
