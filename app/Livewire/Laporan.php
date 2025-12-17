<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Carbon\Carbon;

class Laporan extends Component
{
    public $startDate;
    public $endDate;

    public function mount()
    {
        // Default: Bulan ini
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        // Ambil data penjualan detail dalam range tanggal
        $details = DetailPenjualan::with(['penjualan', 'hp'])
            ->whereHas('penjualan', function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_jual', [$start, $end]);
            })
            ->latest() // Urutkan terbaru
            ->get();

        // Hitung Ringkasan
        $totalOmzet = $details->sum('harga_jual_unit');
        $totalModal = $details->sum('modal_terakhir');
        $totalProfit = $details->sum('laba_rugi');
        $totalUnit = $details->count();

        // Grouping per tanggal untuk list (opsional, tapi bagus untuk UI)
        $groupedDetails = $details->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        });

        return view('livewire.laporan', [
            'groupedDetails' => $groupedDetails,
            'totalOmzet' => $totalOmzet,
            'totalModal' => $totalModal,
            'totalProfit' => $totalProfit,
            'totalUnit' => $totalUnit,
        ]);
    }
}
