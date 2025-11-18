<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'stripe_response' => 'array',
        'stripe_amount_paid' => 'decimal:2'
    ];
}
