<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use Notifiable;
    protected $table = 'location';
	protected $primaryKey = 'transid';
    
	protected $fillable = [
        'transid', 'longitude', 'latitude'
    ];
}
