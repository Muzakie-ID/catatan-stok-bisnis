<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CashFlow;
use Livewire\WithPagination;

class Keuangan extends Component
{
    use WithPagination;

    public $date;
    public $type = 'expense'; // Default pengeluaran
    public $category = 'operasional';
    public $amount;
    public $description;

    // public $showForm = false; // Tidak dipakai lagi karena pakai modal

    public function mount()
    {
        $this->date = date('Y-m-d');
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

    public function delete($id)
    {
        CashFlow::find($id)->delete();
        session()->flash('message', 'Transaksi dihapus.');
    }

    public function render()
    {
        $transactions = CashFlow::latest('date')->latest('id')->paginate(20);
        
        $totalIncome = CashFlow::where('type', 'income')->sum('amount');
        $totalExpense = CashFlow::where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('livewire.keuangan', [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
        ]);
    }
}
