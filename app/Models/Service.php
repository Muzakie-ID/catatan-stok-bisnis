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

    public function cashFlow()
    {
        return $this->morphOne(CashFlow::class, 'reference');
    }

    protected static function booted()
    {
        static::deleting(function ($service) {
            $service->cashFlow()->delete();
        });
    }
}
