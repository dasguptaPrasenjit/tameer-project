<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use Notifiable;
    protected $table = 'cart_items';
	protected $primaryKey = 'cart_item_id';
    
}
