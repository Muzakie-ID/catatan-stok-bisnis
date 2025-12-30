<div class="pb-20">
    <!-- Header -->
    <div class="bg-white p-4 sticky top-0 z-30 shadow-sm border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">Keuangan & Kas</h1>
            <button wire:click="toggleForm" class="btn btn-sm btn-primary">
                {{ $showForm ? 'Batal' : '+ Catat Transaksi' }}
            </button>
        </div>
    </div>

    <div class="p-4 space-y-4">
        <!-- Saldo Card -->
        <div class="card bg-gradient-to-br from-blue-600 to-blue-800 text-white shadow-xl">
            <div class="card-body p-5">
                <h2 class="text-sm font-medium opacity-80">Saldo Kas Saat Ini</h2>
                <p class="text-3xl font-bold">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                <div class="flex gap-4 mt-2">
                    <div class="text-xs">
                        <span class="opacity-70 block">Total Masuk</span>
                        <span class="font-semibold text-green-300">+ Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-xs">
                        <span class="opacity-70 block">Total Keluar</span>
                        <span class="font-semibold text-red-300">- Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Input -->
        @if($showForm)
        <div class="card bg-base-100 shadow-md border border-base-200 animate-fade-in-down">
            <div class="card-body p-4">
                <h3 class="font-bold text-lg mb-2">Catat Transaksi Baru</h3>
                <form wire:submit="save" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text text-xs">Tanggal</span></label>
                            <input type="date" wire:model="date" class="input input-sm input-bordered" />
                        </div>
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text text-xs">Tipe</span></label>
                            <select wire:model.live="type" class="select select-sm select-bordered">
                                <option value="income">Pemasukan (+)</option>
                                <option value="expense">Pengeluaran (-)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs">Kategori</span></label>
                        <select wire:model="category" class="select select-sm select-bordered">
                            @if($type == 'income')
                                <option value="modal_awal">Modal Awal (Suntik Modal)</option>
                                <option value="penjualan">Penjualan (Manual)</option>
                                <option value="lainnya">Pemasukan Lainnya</option>
                            @else
                                <option value="operasional">Biaya Operasional (Listrik/Sewa)</option>
                                <option value="gaji">Gaji Karyawan/Owner</option>
                                <option value="prive">Prive (Tarik Modal Pribadi)</option>
                                <option value="stok">Pembelian Stok (Manual)</option>
                                <option value="lainnya">Pengeluaran Lainnya</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs">Nominal (Rp)</span></label>
                        <input type="text" x-data="{
                            val: @entangle('amount'),
                            format(v) { return v ? new Intl.NumberFormat('id-ID').format(v) : '' },
                            update(e) {
                                let raw = e.target.value.replace(/[^0-9]/g, '');
                                this.val = raw;
                                e.target.value = this.format(raw);
                            }
                        }" :value="format(val)" @input="update" class="input input-sm input-bordered font-bold text-lg" placeholder="0" />
                        @error('amount') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs">Keterangan</span></label>
                        <textarea wire:model="description" class="textarea textarea-sm textarea-bordered" placeholder="Contoh: Bayar Listrik Bulan Ini"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full mt-2">Simpan Transaksi</button>
                </form>
            </div>
        </div>
        @endif

        <!-- List Transaksi -->
        <div>
            <h3 class="font-bold text-gray-700 mb-3">Riwayat Transaksi</h3>
            <div class="space-y-2">
                @forelse($transactions as $trx)
                    <div class="card bg-white border border-gray-100 shadow-sm">
                        <div class="card-body p-3 flex flex-row justify-between items-center">
                            <div>
                                <div class="text-xs text-gray-400 mb-1">{{ $trx->date->format('d M Y') }}</div>
                                <div class="font-bold text-gray-800 capitalize">{{ str_replace('_', ' ', $trx->category) }}</div>
                                <div class="text-xs text-gray-500">{{ $trx->description ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold {{ $trx->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $trx->type == 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </div>
                                @if(!$trx->reference_id)
                                    <button wire:click="delete({{ $trx->id }})" wire:confirm="Hapus transaksi ini?" class="text-xs text-red-400 underline mt-1">Hapus</button>
                                @else
                                    <span class="badge badge-xs badge-ghost mt-1">Otomatis</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        Belum ada data transaksi.
                    </div>
                @endforelse
                
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
