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

    public function toggleServiceForm()
    {
        $this->showServiceForm = !$this->showServiceForm;
    }

    #[On('open-detail-hp')]
    public function loadHp($id)
    {
        $this->hp = Hp::with('services')->find($id);
        $this->showServiceForm = false;
        $this->dispatch('show-modal-detail');
    }

    public function saveService()
    {
        $this->validate([
            'deskripsi_service' => 'required|string',
            'biaya_service' => 'required|numeric|min:0',
        ]);

        // 1. Simpan data service
        Service::create([
            'hp_id' => $this->hp->id,
            'deskripsi' => $this->deskripsi_service,
            'biaya' => $this->biaya_service,
            'tanggal_service' => now(),
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
