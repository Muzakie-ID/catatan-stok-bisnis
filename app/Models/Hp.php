<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hp extends Model
{
    protected $fillable = [
        'imei',
        'merk_model',
        'warna',
        'keterangan_minus',
        'sumber_beli',
        'harga_beli_awal',
        'total_modal',
        'status',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
