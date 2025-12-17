<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $fillable = [
        'penjualan_id',
        'hp_id',
        'modal_terakhir',
        'harga_jual_unit',
        'laba_rugi'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function hp()
    {
        return $this->belongsTo(Hp::class, 'hp_id');
    }
}
