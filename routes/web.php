<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Home;
use App\Livewire\Stok;
use App\Livewire\Penjualan;
use App\Livewire\Laporan;

Route::get('/', Home::class);
Route::get('/stok', Stok::class);
Route::get('/penjualan', Penjualan::class);
Route::get('/laporan', Laporan::class);
