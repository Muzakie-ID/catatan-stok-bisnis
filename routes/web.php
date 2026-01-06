<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Home;
use App\Livewire\Stok;
use App\Livewire\Penjualan;
use App\Livewire\ServiceIndex;
use App\Livewire\Laporan;
use App\Livewire\Keuangan;

Route::get('/', Home::class);
Route::get('/stok', Stok::class);
Route::get('/service', ServiceIndex::class);
Route::get('/penjualan', Penjualan::class);
Route::get('/laporan', Laporan::class);
Route::get('/keuangan', Keuangan::class);
