<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = [
        'name',
        'monthly_price',
        'annual_price',
        'features',
        'is_active',
        'slug',
    ];

    protected $casts = [
        // 'features' => 'array',
        'is_active' => 'boolean',
    ];
}
