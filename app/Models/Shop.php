<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'whatsapp_number',
        'state',
        'logo_path',
        'primary_color',
        'pricing_plan_id',
        'subscription_status',
        'subscription_expires_at',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    public function pricingPlan()
    {
        return $this->belongsTo(PricingPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function verification()
    {
        return $this->hasOne(ShopVerification::class);
    }
}
