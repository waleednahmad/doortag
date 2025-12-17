<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'address',
        'address2',
        'city',
        'state',
        'zipcode',
        'tax_id',
        'years_in_business',
        'business_type',
        'notes',
        'margin',
        'customer_margin',
        'tax_percentage',
        'stripe_customer_id',
        'carrier_id',
        'status',
    ];

    protected $casts = [
        'margin' => 'decimal:2',
        'customer_margin' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'years_in_business' => 'integer',
    ];

    // ============== Relationships ==============
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function stripePaymentMethods()
    {
        return $this->hasMany(LocationStripePaymentMethods::class);
    }
}
