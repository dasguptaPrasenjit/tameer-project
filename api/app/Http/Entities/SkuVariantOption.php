<?php
namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Model;

class SkuVariantOption extends Model
{
    use Notifiable;
    protected $table = 'skus_product_variant_options';

    public function product_variant()
    {
        return $this->belongsTo('App\Http\Entities\ProductVariant', 'product_variant_id');
    }

    public function product_variant_option()
    {
        return $this->belongsTo('App\Http\Entities\ProductVariantOption', 'product_variant_options_id');
    }
}
