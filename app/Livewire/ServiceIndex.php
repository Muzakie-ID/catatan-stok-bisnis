<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use Livewire\WithPagination;

class ServiceIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $services = Service::with('hp')->latest('tanggal_service')->paginate(20);
        
        $totalBiayaService = Service::sum('biaya');

        return view('livewire.service-index', [
            'services' => $services,
            'totalBiayaService' => $totalBiayaService
        ]);
    }
}
