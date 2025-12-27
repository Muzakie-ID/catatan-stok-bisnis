<div class="pb-20">
    {{-- Header --}}
    <div class="navbar bg-base-100 shadow-sm sticky top-0 z-30">
        <div class="flex-1">
            <a class="btn btn-ghost text-xl">
                @if($step == 1) Pilih Barang @else Checkout @endif
            </a>
        </div>
        <div class="flex-none">
            @if($step == 2)
                <button wire:click="prevStep" class="btn btn-square btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    <div class="p-4">
        @if($step == 1)
            {{-- STEP 1: PILIH BARANG --}}
            
            {{-- Search --}}
            <div class="form-control mb-4">
                <input type="text" wire:model.live="search" placeholder="Cari Merk/Model atau IMEI..." class="input input-bordered w-full" />
            </div>

            {{-- List Barang --}}
            <div class="grid grid-cols-1 gap-3">
                @forelse($hps as $hp)
                    <div 
                        wire:click="toggleSelection({{ $hp->id }})"
                        class="card bg-base-100 shadow-md border border-base-200 cursor-pointer transition-all {{ in_array($hp->id, $selectedHps) ? 'ring-2 ring-primary bg-primary/5' : '' }}"
                    >
                        <div class="card-body p-4 flex flex-row items-center justify-between">
                            <div>
                                <h3 class="font-bold text-lg">{{ $hp->merk_model }}</h3>
                                <div class="text-xs text-gray-500">IMEI: {{ $hp->imei }}</div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @if($hp->warna)
                                        <span class="badge badge-xs badge-ghost">{{ $hp->warna }}</span>
                                    @endif
                                    @if($hp->keterangan_minus)
                                        <span class="badge badge-xs badge-error badge-outline">Minus: {{ $hp->keterangan_minus }}</span>
                                    @endif
                                </div>
                                <div class="text-sm font-semibold text-secondary mt-1">
                                    Modal: Rp {{ number_format($hp->total_modal, 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <input type="checkbox" class="checkbox checkbox-primary pointer-events-none" {{ in_array($hp->id, $selectedHps) ? 'checked' : '' }} />
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500">
                        Tidak ada barang READY yang ditemukan.
                    </div>
                @endforelse
            </div>

            {{-- Bottom Bar --}}
            @if(count($selectedHps) > 0)
                <div class="fixed bottom-16 left-0 right-0 p-4 bg-base-100 border-t border-base-200 z-40 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold">{{ count($selectedHps) }} Barang dipilih</span>
                    </div>
                    <button wire:click="nextStep" class="btn btn-primary w-full text-lg">
                        Lanjut ke Harga
                    </button>
                </div>
            @endif

        @elseif($step == 2)
            {{-- STEP 2: CHECKOUT & SPLIT HARGA --}}

            <div class="space-y-4">
                {{-- Info Pembeli --}}
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body p-4">
                        <h3 class="font-bold mb-2">Info Transaksi</h3>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Nama Pembeli (Opsional)</span></label>
                            <input type="text" wire:model="nama_pembeli" class="input input-bordered w-full" placeholder="Contoh: Pak Budi" />
                        </div>
                        <div class="form-control w-full mt-2">
                            <label class="label"><span class="label-text font-bold">Total Transaksi (Semua Barang)</span></label>
                            <input type="text" readonly x-data="{
                                val: @entangle('total_transaksi'),
                                format(v) { return v ? new Intl.NumberFormat('id-ID').format(v) : '' }
                            }" :value="format(val)" class="input input-bordered input-lg w-full font-bold text-primary bg-gray-100" placeholder="0" />
                            <label class="label"><span class="label-text-alt text-gray-500">*Otomatis terhitung dari total harga per unit</span></label>
                        </div>
                        @error('total_transaksi') <span class="text-error text-sm">{{ $message }}</span> @enderror

                        {{-- Realtime Profit Info --}}
                        @php
                            $totalModal = $hps->sum('total_modal');
                            $estimasiProfit = (float)$total_transaksi - $totalModal;
                        @endphp
                        <div class="grid grid-cols-2 gap-4 mt-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div>
                                <div class="text-xs text-gray-500">Total Modal</div>
                                <div class="font-bold text-gray-700">Rp {{ number_format($totalModal, 0, ',', '.') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">Estimasi Profit</div>
                                <div class="font-bold {{ $estimasiProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $estimasiProfit >= 0 ? '+' : '' }} Rp {{ number_format($estimasiProfit, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rincian Per Item --}}
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body p-4">
                        <h3 class="font-bold mb-2">Rincian Harga Per Unit</h3>
                        <p class="text-xs text-gray-500 mb-4">Masukkan harga jual untuk masing-masing unit agar keuntungan tercatat akurat per barang.</p>

                        <div class="space-y-4">
                            @foreach($hps as $hp)
                                <div class="p-3 bg-base-200 rounded-lg" wire:key="hp-item-{{ $hp->id }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <div class="font-bold">{{ $hp->merk_model }}</div>
                                            <div class="text-xs text-gray-500">{{ $hp->imei }}</div>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @if($hp->warna)
                                                    <span class="badge badge-xs badge-ghost">{{ $hp->warna }}</span>
                                                @endif
                                                @if($hp->keterangan_minus)
                                                    <span class="badge badge-xs badge-error badge-outline">Minus: {{ $hp->keterangan_minus }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="badge badge-ghost text-xs">Modal: {{ number_format($hp->total_modal, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-0"><span class="label-text text-xs">Harga Jual Unit Ini</span></label>
                                        <input type="text" x-data="{
                                            val: @entangle('harga_jual_items.' . $hp->id).live,
                                            format(v) { return v ? new Intl.NumberFormat('id-ID').format(v) : '' },
                                            update(e) {
                                                let raw = e.target.value.replace(/[^0-9]/g, '');
                                                this.val = raw;
                                                e.target.value = this.format(raw);
                                            }
                                        }" :value="format(val)" @input="update" class="input input-bordered input-sm w-full" placeholder="0" />
                                    </div>
                                    {{-- Kalkulasi Laba Realtime --}}
                                    @if(isset($harga_jual_items[$hp->id]) && is_numeric($harga_jual_items[$hp->id]))
                                        <div class="text-right mt-1 text-xs">
                                            @php $laba = $harga_jual_items[$hp->id] - $hp->total_modal; @endphp
                                            Laba: <span class="{{ $laba >= 0 ? 'text-success' : 'text-error' }} font-bold">
                                                Rp {{ number_format($laba, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Summary Check --}}
                        @php 
                            $sumItems = 0;
                            foreach($harga_jual_items as $val) {
                                $sumItems += (float) $val;
                            }
                        @endphp
                        <div class="mt-4 p-3 rounded-lg {{ $sumItems == $total_transaksi ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">
                            <div class="flex justify-between text-sm font-bold">
                                <span>Total Rincian:</span>
                                <span>Rp {{ number_format($sumItems, 0, ',', '.') }}</span>
                            </div>
                            @if($sumItems != $total_transaksi)
                                <div class="text-xs mt-1">
                                    Selisih: Rp {{ number_format($total_transaksi - $sumItems, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Action --}}
            <div class="fixed bottom-16 left-0 right-0 p-4 bg-base-100 border-t border-base-200 z-40 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
                <button 
                    wire:click="processPenjualan" 
                    class="btn btn-primary w-full text-lg"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Simpan Penjualan</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        @endif
    </div>
</div>
