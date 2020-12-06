<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class ProductVariantOption extends Model
{
    use Notifiable;
    protected $table = 'product_variant_options';   

}
