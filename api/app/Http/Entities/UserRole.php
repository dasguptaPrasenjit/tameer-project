<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use Notifiable;
    protected $table = 'user_role';
	protected $primaryKey = 'user_role_id';

	protected $fillable = [
        'user_id', 'role_id'
    ];
    /**
     * Get User Role
     *
    */
    public function getUserRole($user_id){
        $user_role = DB::table($this->table)
					 ->join('users', $this->table.".user_id", '=', "users.id")
					 ->join('role', $this->table.".role_id", '=', "role.role_id")
					 ->where('users.id',$user_id)
                     ->select(
						'role.role_name as role_name', 'role.role_description as role_description','role.role_id as role_id'
					 )
                     ->get();

        return $user_role;
    }
	/**
     * Get All Role
     *
    */
	public function getAllRoles(){
        $role = DB::table('role')
                     ->get();

        return $role;
    }

}
