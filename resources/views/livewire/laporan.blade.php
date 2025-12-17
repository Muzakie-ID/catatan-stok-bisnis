<div class="pb-24">
    <!-- Header & Filter -->
    <div class="bg-white p-4 sticky top-0 z-30 shadow-sm border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-800 mb-4">Laporan Penjualan</h1>
        
        <div class="flex gap-2">
            <div class="form-control w-1/2">
                <label class="label py-0"><span class="label-text text-xs">Dari</span></label>
                <input type="date" wire:model.live="startDate" class="input input-sm input-bordered w-full" />
            </div>
            <div class="form-control w-1/2">
                <label class="label py-0"><span class="label-text text-xs">Sampai</span></label>
                <input type="date" wire:model.live="endDate" class="input input-sm input-bordered w-full" />
            </div>
        </div>
    </div>

    <div class="p-4 space-y-4">
        <!-- Summary Cards -->
        <div class="grid grid-cols-2 gap-3">
            <!-- Profit Card (Highlight) -->
            <div class="col-span-2 card bg-gradient-to-r from-green-600 to-green-500 text-white shadow-lg">
                <div class="card-body p-5">
                    <h2 class="text-sm font-medium opacity-90">Total Keuntungan Bersih</h2>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
                    <div class="flex justify-between items-end mt-2">
                        <div class="text-xs opacity-80">
                            Omzet: Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                        </div>
                        <div class="badge bg-white/20 border-0 text-white text-xs">
                            {{ $totalUnit }} Unit Terjual
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Info -->
            <div class="card bg-white border border-gray-200 shadow-sm">
                <div class="card-body p-3">
                    <div class="text-xs text-gray-500">Total Modal Keluar</div>
                    <div class="font-bold text-gray-700">Rp {{ number_format($totalModal, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Margin Info -->
            <div class="card bg-white border border-gray-200 shadow-sm">
                <div class="card-body p-3">
                    <div class="text-xs text-gray-500">Margin Rata-rata</div>
                    @php 
                        $margin = $totalOmzet > 0 ? ($totalProfit / $totalOmzet) * 100 : 0;
                    @endphp
                    <div class="font-bold text-blue-600">{{ number_format($margin, 1) }}%</div>
                </div>
            </div>
        </div>

        <!-- Transaction List -->
        <div>
            <h3 class="font-bold text-gray-800 mb-3">Rincian Transaksi</h3>
            
            @forelse($groupedDetails as $date => $items)
                <div class="mb-4">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 sticky top-[130px] bg-gray-50 py-1 z-10">
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </div>
                    
                    <div class="space-y-2">
                        @foreach($items as $item)
                            <div class="card bg-white border border-gray-100 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-bold text-gray-800">{{ $item->hp->merk_model ?? 'Item Dihapus' }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->hp->imei ?? '-' }}</div>
                                            @if($item->penjualan->nama_pembeli)
                                                <div class="text-xs text-blue-500 mt-1">
                                                    Pembeli: {{ $item->penjualan->nama_pembeli }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-green-600">+ Rp {{ number_format($item->laba_rugi, 0, ',', '.') }}</div>
                                            <div class="text-xs text-gray-400">
                                                Jual: {{ number_format($item->harga_jual_unit, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <p class="text-gray-500">Tidak ada data penjualan pada periode ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
