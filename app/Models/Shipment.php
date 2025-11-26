<?php

namespace App\Models;

use App\Observers\ShipmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ShipmentObserver::class])]
class Shipment extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'stripe_response' => 'array',
        'stripe_amount_paid' => 'decimal:2'
    ];
}
