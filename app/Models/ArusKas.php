<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArusKas extends Model
{
    protected $table = 'arus_kas';
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];
}
