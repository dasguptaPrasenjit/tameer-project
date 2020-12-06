<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sku extends Model
{
    use Notifiable;
    use SoftDeletes;
    protected $table = 'skus';

    public function image()
    {
        return $this->hasOne('App\Http\Entities\ProductDetailedImages', 'sku_id');
    }
    
    public function sku_variant()
    {
        return $this->hasMany('App\Http\Entities\SkuVariantOption', 'sku_id');
    }
    
    public function cart_item()
    {
        return $this->hasOne('App\Http\Entities\CartItem', 'sku_id');
    }

}
