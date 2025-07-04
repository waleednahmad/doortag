<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'label',
        'value',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
}
