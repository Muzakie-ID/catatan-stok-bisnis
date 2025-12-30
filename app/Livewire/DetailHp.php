<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Hp;
use App\Models\Service;
use Livewire\Attributes\On;

class DetailHp extends Component
{
    public $hp;
    public $deskripsi_service;
    public $biaya_service;
    public $showServiceForm = false;

    // Edit Mode Properties
    public $isEditing = false;
    public $edit_merk_model;
    public $edit_warna;
    public $edit_minus;
    public $edit_sumber_beli;
    public $edit_harga_beli_awal;

    public function toggleServiceForm()
    {
        $this->showServiceForm = !$this->showServiceForm;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if ($this->isEditing && $this->hp) {
            $this->edit_merk_model = $this->hp->merk_model;
            $this->edit_warna = $this->hp->warna;
            $this->edit_minus = $this->hp->keterangan_minus;
            $this->edit_sumber_beli = $this->hp->sumber_beli;
            $this->edit_harga_beli_awal = $this->hp->harga_beli_awal;
        }
    }

    public function updateHp()
    {
        $this->validate([
            'edit_merk_model' => 'required|string',
            'edit_warna' => 'nullable|string',
            'edit_minus' => 'nullable|string',
            'edit_sumber_beli' => 'nullable|string',
            'edit_harga_beli_awal' => 'required|numeric|min:0',
        ]);

        // Hitung selisih harga beli jika berubah, untuk update total modal
        $selisih = $this->edit_harga_beli_awal - $this->hp->harga_beli_awal;
        
        // Update CashFlow jika harga beli berubah
        if ($selisih != 0) {
            \App\Models\CashFlow::where('reference_type', \App\Models\Hp::class)
                ->where('reference_id', $this->hp->id)
                ->where('category', 'Pembelian Stok')
                ->update(['amount' => $this->edit_harga_beli_awal]);
        }

        $this->hp->update([
            'merk_model' => $this->edit_merk_model,
            'warna' => $this->edit_warna,
            'keterangan_minus' => $this->edit_minus,
            'sumber_beli' => $this->edit_sumber_beli,
            'harga_beli_awal' => $this->edit_harga_beli_awal,
            'total_modal' => $this->hp->total_modal + $selisih,
        ]);

        $this->isEditing = false;
        $this->dispatch('stok-saved'); // Refresh list utama
    }

    public function deleteHp()
    {
        if ($this->hp) {
            // Hapus CashFlow Pembelian Stok
            \App\Models\CashFlow::where('reference_type', \App\Models\Hp::class)
                ->where('reference_id', $this->hp->id)
                ->delete();

            // Hapus CashFlow Service & Data Service
            foreach ($this->hp->services as $service) {
                \App\Models\CashFlow::where('reference_type', \App\Models\Service::class)
                   ->where('reference_id', $service->id)
                   ->delete();
            }
            $this->hp->services()->delete();

            $this->hp->delete();
            $this->dispatch('stok-saved');
            $this->dispatch('close-modal-detail'); // Perlu handle ini di JS view
        }
    }

    #[On('open-detail-hp')]
    public function loadHp($id)
    {
        $this->hp = Hp::with('services')->find($id);
        $this->showServiceForm = false;
        $this->isEditing = false;
        $this->dispatch('show-modal-detail');
    }

    public function saveService()
    {
        $this->validate([
            'deskripsi_service' => 'required|string',
            'biaya_service' => 'required|numeric|min:0',
        ]);

        // 1. Simpan data service
        $service = Service::create([
            'hp_id' => $this->hp->id,
            'deskripsi' => $this->deskripsi_service,
            'biaya' => $this->biaya_service,
            'tanggal_service' => now(),
        ]);

        // Catat Pengeluaran (CashFlow)
        \App\Models\CashFlow::create([
            'date' => now(),
            'type' => 'expense',
            'category' => 'Service HP',
            'amount' => $this->biaya_service,
            'description' => "Service {$this->hp->merk_model}: {$this->deskripsi_service}",
            'reference_type' => \App\Models\Service::class,
            'reference_id' => $service->id,
        ]);

        // 2. Update Total Modal HP
        $this->hp->total_modal += $this->biaya_service;
        $this->hp->status = 'SERVICE'; // Ubah status jadi SERVICE sementara? Atau tetap READY?
        $this->hp->save();

        // 3. Reset Form
        $this->deskripsi_service = '';
        $this->biaya_service = '';
        $this->showServiceForm = false;

        // 4. Refresh data HP & List Stok Utama
        $this->hp->refresh();
        $this->dispatch('stok-saved'); // Refresh list stok di halaman utama
    }

    public function markAsReady()
    {
        if ($this->hp) {
            $this->hp->update(['status' => 'READY']);
            $this->hp->refresh();
            $this->dispatch('stok-saved');
        }
    }

    public function render()
    {
        return view('livewire.detail-hp');
    }
}
