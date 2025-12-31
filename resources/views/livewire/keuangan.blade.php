<div class="pb-20">
    <!-- Header -->
    <div class="bg-white p-4 sticky top-0 z-30 shadow-sm border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">Keuangan & Kas</h1>
            <button onclick="modal_keuangan.showModal()" class="btn btn-sm btn-primary">
                + Catat Transaksi
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

        <!-- Gaji Owner Card -->
        <div class="card bg-white border border-gray-200 shadow-sm">
            <div class="card-body p-4">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-sm font-bold text-gray-700">Jatah Gaji Owner (50% Profit)</h2>
                    <div class="badge badge-sm badge-ghost">Akumulasi</div>
                </div>
                
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-xs text-gray-500">Bisa Diambil Saat Ini</p>
                        <p class="text-xl font-bold {{ $availableSalary < 0 ? 'text-red-600' : 'text-green-600' }}">
                            Rp {{ number_format($availableSalary, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Sudah Diambil</p>
                        <p class="text-xs font-semibold text-gray-600">Rp {{ number_format($totalSalaryTaken, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                @if($availableSalary > 0)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <button onclick="modal_konfirmasi_gaji.showModal()" class="btn btn-xs btn-outline btn-success w-full">
                            Ambil Semua Gaji (Rp {{ number_format($availableSalary, 0, ',', '.') }})
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Konfirmasi Gaji -->
        <dialog id="modal_konfirmasi_gaji" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg text-center">Konfirmasi Pengambilan Gaji</h3>
                <p class="py-4 text-center text-gray-600">
                    Anda akan mengambil gaji sebesar <br>
                    <span class="font-bold text-xl text-green-600">Rp {{ number_format($availableSalary, 0, ',', '.') }}</span>
                </p>
                <div class="modal-action justify-center">
                    <form method="dialog">
                        <button class="btn btn-sm btn-ghost mr-2">Batal</button>
                    </form>
                    <button onclick="modal_konfirmasi_gaji.close(); modal_keuangan.showModal(); @this.set('type', 'expense'); @this.set('category', 'gaji'); @this.set('amount', {{ $availableSalary }}); @this.set('description', 'Pengambilan Gaji Owner (Otomatis)');" class="btn btn-sm btn-primary">
                        Ya, Ambil Sekarang
                    </button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- Modal Input -->
        <dialog id="modal_keuangan" class="modal" wire:ignore.self>
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                
                <h3 class="font-bold text-lg mb-4">Catat Transaksi Baru</h3>
                
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

                    <button type="submit" class="btn btn-primary w-full mt-4">Simpan Transaksi</button>
                </form>
            </div>
        </dialog>

        <!-- Modal Konfirmasi Hapus -->
        <dialog id="modal_hapus_transaksi" class="modal" wire:ignore.self>
            <div class="modal-box">
                <h3 class="font-bold text-lg text-center text-red-600">Hapus Transaksi?</h3>
                <p class="py-4 text-center text-gray-600">
                    Apakah Anda yakin ingin menghapus transaksi ini? <br>
                    <span class="text-xs text-gray-400">Data yang dihapus tidak dapat dikembalikan.</span>
                </p>
                <div class="modal-action justify-center">
                    <form method="dialog">
                        <button class="btn btn-sm btn-ghost mr-2">Batal</button>
                    </form>
                    <button wire:click="destroy" class="btn btn-sm btn-error text-white">
                        Ya, Hapus
                    </button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

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
                                    <button wire:click="confirmDelete({{ $trx->id }})" class="text-xs text-red-400 underline mt-1">Hapus</button>
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

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', () => {
                document.getElementById('modal_keuangan').close();
            });
            
            Livewire.on('open-modal-delete', () => {
                document.getElementById('modal_hapus_transaksi').showModal();
            });

            Livewire.on('close-modal-delete', () => {
                document.getElementById('modal_hapus_transaksi').close();
            });
        });
    </script>
</div>
