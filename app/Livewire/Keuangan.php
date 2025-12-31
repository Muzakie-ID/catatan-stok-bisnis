<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CashFlow;
use App\Models\DetailPenjualan;
use Livewire\WithPagination;

class Keuangan extends Component
{
    use WithPagination;

    public $date;
    public $type = 'expense'; // Default pengeluaran
    public $category = 'operasional';
    public $amount;
    public $description;
    
    // Konfigurasi Persentase Gaji (50%)
    public $salaryPercentage = 50; 
    
    public $deleteId;

    // public $showForm = false; // Tidak dipakai lagi karena pakai modal

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function updatedType($value)
    {
        // Reset kategori ke default yang sesuai saat tipe berubah
        if ($value == 'income') {
            $this->category = 'modal_awal';
        } else {
            $this->category = 'operasional';
        }
    }

    // public function toggleForm() ... dihapus

    public function save()
    {
        $this->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
        ]);

        CashFlow::create([
            'date' => $this->date,
            'type' => $this->type,
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
        ]);

        $this->reset(['amount', 'description']);
        // $this->showForm = false; 
        $this->dispatch('close-modal'); // Dispatch event untuk tutup modal
        session()->flash('message', 'Transaksi berhasil disimpan.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-modal-delete');
    }

    public function destroy()
    {
        if ($this->deleteId) {
            CashFlow::find($this->deleteId)->delete();
            session()->flash('message', 'Transaksi dihapus.');
            $this->dispatch('close-modal-delete');
        }
    }

    public function render()
    {
        $transactions = CashFlow::latest('date')->latest('id')->paginate(20);
        
        $totalIncome = CashFlow::where('type', 'income')->sum('amount');
        $totalExpense = CashFlow::where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Hitung Gaji Owner (Profit Sharing)
        $totalProfitAllTime = DetailPenjualan::sum('laba_rugi');
        $totalSalaryEntitlement = $totalProfitAllTime * ($this->salaryPercentage / 100);
        $totalSalaryTaken = CashFlow::where('category', 'gaji')->where('type', 'expense')->sum('amount');
        $availableSalary = $totalSalaryEntitlement - $totalSalaryTaken;

        return view('livewire.keuangan', [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'availableSalary' => $availableSalary,
            'totalSalaryTaken' => $totalSalaryTaken,
        ]);
    }
}
