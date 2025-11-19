<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'address2',
        'city',
        'state',
        'zipcode',
        'margin',
        'customer_margin',
        'password',
        'address_residential_indicator',
        'can_modify_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'address_residential_indicator' => 'boolean',
            'can_modify_data' => 'boolean',
        ];
    }


    // =====================
    // Relationships
    // =====================
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'customer_id', 'id');
    }
}
