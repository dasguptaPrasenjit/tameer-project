<?php

namespace App\Http\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class CarrierReport extends Model
{
    use Notifiable;
    protected $table = 'carrier_report';
    protected $fillable = ['carrier_id', 'reported_proof_vehicle_no', 'reported_proof_photo', 'reported_proof_address', 'remarks', 'resolved', 'raised_on', 'resolved_on'];
}
