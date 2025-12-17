<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'hp_id',
        'deskripsi',
        'biaya',
        'tanggal_service',
    ];

    public function hp()
    {
        return $this->belongsTo(Hp::class);
    }
}
