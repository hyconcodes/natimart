<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'description',
        'price',
        'image_path',
        'is_approved',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_approved' => 'boolean',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
