<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Hp;
use Livewire\Attributes\On;

class Stok extends Component
{
    public $search = '';

    #[On('stok-saved')] 
    public function refreshStok()
    {
        // Method ini dipanggil saat event 'stok-saved' di-dispatch.
        // Livewire otomatis me-render ulang komponen, jadi cukup kosong saja.
    }

    public function render()
    {
        $hps = Hp::query()
            ->where('status', '!=', 'SOLD') // Hanya tampilkan yang belum terjual
            ->where(function($query) {
                $query->where('merk_model', 'like', '%' . $this->search . '%')
                      ->orWhere('imei', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->get();

        return view('livewire.stok', [
            'hps' => $hps
        ]);
    }
}
