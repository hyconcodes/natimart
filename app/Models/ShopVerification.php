<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopVerification extends Model
{
    protected $fillable = [
        'shop_id',
        'cac_certificate',
        'tin_number',
        'cac_status_report',
        'owner_id_card',
        'nafdac_number',
        'son_mancap_cert',
        'lab_test_report',
        'trademark_cert',
        'logistics_sla',
        'production_address',
        'production_capacity',
        'verification_status',
    ];

    protected $casts = [
        'verification_status' => 'json',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
