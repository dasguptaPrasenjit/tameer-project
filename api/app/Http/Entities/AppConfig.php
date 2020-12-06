<?php

namespace App\Http\Entities;

use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    protected $table = 'app_configs';
    protected $fillable = ['food_category_id', 'max_orders_per_carrier'];

    public static function getConfig()
    {
        $appConfigId = \config('globalconfig.APP_CONFIG_ID');
        $appConfig = AppConfig::where('id', $appConfigId)->first();
        return $appConfig;
    }
}
