<?php

namespace App\Http\Entities;

use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    protected $fillable = ['receiver_name', 'receiver_mobile', 'receiver_address', 'receiver_pin', 'receiver_landmark', 'receiver_latitude', 'receiver_longitude', 'sender_name', 'sender_mobile', 'sender_address', 'sender_pin', 'sender_landmark', 'sender_latitude', 'sender_longitude', 'payment_method', 'payer', 'carrier_id', 'item_type', 'product_name', 'weight', 'distance', 'cost_per_km', 'payable_amount', 'pickup_on', 'assigned_on', 'delivered_on', 'status', 'created_by'];

    public function carrier()
    {
        return $this->belongsTo('App\Http\Entities\Carrier', 'carrier_id');
    }
}
