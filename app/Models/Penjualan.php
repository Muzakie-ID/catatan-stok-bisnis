<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = ['nama_pembeli', 'total_transaksi', 'tanggal_jual'];

    public function details()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }
}
