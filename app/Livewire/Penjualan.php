<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Hp;
use App\Models\Penjualan as PenjualanModel;
use App\Models\DetailPenjualan;
use Illuminate\Support\Facades\DB;

class Penjualan extends Component
{
    public $search = '';
    public $selectedHps = []; // Array ID HP yang dipilih
    public $step = 1; // 1: Pilih Barang, 2: Checkout (Split Harga)
    
    // Data Checkout
    public $nama_pembeli;
    public $total_transaksi;
    public $harga_jual_items = []; // Array [hp_id => harga_jual]

    public function toggleSelection($hpId)
    {
        if (in_array($hpId, $this->selectedHps)) {
            $this->selectedHps = array_diff($this->selectedHps, [$hpId]);
        } else {
            $this->selectedHps[] = $hpId;
        }
    }

    public function nextStep()
    {
        if (empty($this->selectedHps)) {
            return; // Harus pilih minimal 1
        }
        $this->step = 2;
        
        // Inisialisasi array harga jual dengan 0
        foreach ($this->selectedHps as $id) {
            $this->harga_jual_items[$id] = 0;
        }
    }

    public function prevStep()
    {
        $this->step = 1;
    }

    // Fitur Pintar: Auto Split Harga
    // Saat user update total transaksi atau harga item, kita bisa bantu hitung sisa
    public function updatedTotalTransaksi()
    {
        // Jika hanya 1 barang, langsung isi otomatis
        if (count($this->selectedHps) == 1) {
            $this->harga_jual_items[$this->selectedHps[0]] = $this->total_transaksi;
        }
    }

    public function updatedHargaJualItems($value, $key)
    {
        // Logika Auto Split Sederhana (Opsional, bisa dikembangkan)
        // Jika ada 2 barang, dan barang 1 diisi, barang 2 otomatis sisa
        if (count($this->selectedHps) == 2 && (float)$this->total_transaksi > 0) {
            $otherId = null;
            foreach ($this->selectedHps as $id) {
                if ($id != $key) $otherId = $id;
            }
            
            if ($otherId) {
                // Pastikan nilai numerik untuk menghindari error string - string
                $total = (float) $this->total_transaksi;
                $currentVal = (float) $value;
                $this->harga_jual_items[$otherId] = $total - $currentVal;
            }
        }
    }

    public function processPenjualan()
    {
        $this->validate([
            'nama_pembeli' => 'nullable|string',
            'total_transaksi' => 'required|numeric|min:0',
            'harga_jual_items.*' => 'required|numeric|min:0',
        ]);

        // Validasi Total Item harus sama dengan Total Transaksi
        $sumItems = array_sum($this->harga_jual_items);
        if ($sumItems != $this->total_transaksi) {
            $this->addError('total_transaksi', 'Total rincian item (Rp '.number_format($sumItems).') tidak sama dengan Total Transaksi.');
            return;
        }

        DB::transaction(function () {
            // 1. Buat Header Penjualan
            $penjualan = PenjualanModel::create([
                'nama_pembeli' => $this->nama_pembeli,
                'total_transaksi' => $this->total_transaksi,
                'tanggal_jual' => now(),
            ]);

            // 2. Buat Detail & Update Stok
            foreach ($this->selectedHps as $hpId) {
                $hp = Hp::find($hpId);
                $hargaJual = $this->harga_jual_items[$hpId];
                
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'hp_id' => $hp->id,
                    'modal_terakhir' => $hp->total_modal,
                    'harga_jual_unit' => $hargaJual,
                    'laba_rugi' => $hargaJual - $hp->total_modal,
                ]);

                // Update Status HP
                $hp->update(['status' => 'SOLD']);
            }
        });

        // Reset & Redirect
        session()->flash('message', 'Penjualan berhasil disimpan!');
        return redirect()->to('/');
    }

    public function render()
    {
        $hps = [];
        if ($this->step == 1) {
            $hps = Hp::where('status', 'READY')
                ->where(function($q) {
                    $q->where('merk_model', 'like', '%'.$this->search.'%')
                      ->orWhere('imei', 'like', '%'.$this->search.'%');
                })
                ->get();
        } else {
            // Load data HP yang dipilih untuk step 2
            $hps = Hp::whereIn('id', $this->selectedHps)->get();
        }

        return view('livewire.penjualan', [
            'hps' => $hps
        ]);
    }
}
