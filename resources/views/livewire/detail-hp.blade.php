<div>
    <dialog id="modal_detail_hp" class="modal modal-bottom sm:modal-middle" wire:ignore.self>
        <div class="modal-box relative w-full max-w-md mx-auto rounded-t-3xl rounded-b-none sm:rounded-2xl p-0 bg-white shadow-2xl h-[85vh] flex flex-col">
            
            <!-- Handle Bar -->
            <div class="w-full flex justify-center pt-3 pb-1 flex-none" onclick="modal_detail_hp.close()">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            @if($hp)
            <div class="flex-1 overflow-y-auto p-6 pt-2">
                <!-- Header Info -->
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="font-bold text-2xl text-gray-800">{{ $hp->merk_model }}</h3>
                        <p class="text-sm text-gray-500 font-mono mt-1">{{ $hp->imei }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <div class="badge {{ $hp->status == 'READY' ? 'badge-success' : 'badge-warning' }} text-white">
                            {{ $hp->status }}
                        </div>
                        @if($hp->status == 'SERVICE')
                            <button wire:click="markAsReady" class="btn btn-xs btn-success text-white">
                                Selesai Service
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Financial Card -->
                <div class="card bg-blue-50 border border-blue-100 mb-6">
                    <div class="card-body p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs text-gray-500">Harga Beli Awal</span>
                            <span class="font-semibold text-gray-700">Rp {{ number_format($hp->harga_beli_awal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs text-gray-500">Total Biaya Service</span>
                            <span class="font-semibold text-red-500">+ Rp {{ number_format($hp->total_modal - $hp->harga_beli_awal, 0, ',', '.') }}</span>
                        </div>
                        <div class="divider my-1"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-blue-800">Total Modal Saat Ini</span>
                            <span class="text-lg font-bold text-blue-800">Rp {{ number_format($hp->total_modal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Service History Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-bold text-gray-700">Riwayat Service</h4>
                        <!-- Ganti $toggle dengan wire:click biasa ke method toggleServiceForm -->
                        <button wire:click="toggleServiceForm" class="btn btn-xs btn-outline btn-primary">
                            {{ $showServiceForm ? 'Batal' : '+ Tambah Service' }}
                        </button>
                    </div>

                    <!-- Form Tambah Service (Toggle) -->
                    @if($showServiceForm)
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4 animate-fade-in-down">
                        <form wire:submit="saveService">
                            <div class="form-control w-full mb-2">
                                <input wire:model="deskripsi_service" type="text" placeholder="Keterangan (Misal: Ganti LCD)" class="input input-sm input-bordered w-full" />
                                @error('deskripsi_service') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-control w-full mb-3">
                                <div class="relative" x-data="{
                                    price: @entangle('biaya_service'),
                                    format(val) { return val ? new Intl.NumberFormat('id-ID').format(val) : '' },
                                    input(e) {
                                        let raw = e.target.value.replace(/[^0-9]/g, '');
                                        this.price = raw;
                                        e.target.value = this.format(raw);
                                    }
                                }">
                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                    <input type="text" :value="format(price)" @input="input" placeholder="Biaya" class="input input-sm input-bordered w-full pl-6" />
                                </div>
                                @error('biaya_service') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-full">Simpan Biaya</button>
                        </form>
                    </div>
                    @endif

                    <!-- List Service -->
                    <div class="space-y-2">
                        @forelse($hp->services as $service)
                            <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                                <div>
                                    <p class="font-semibold text-sm text-gray-800">{{ $service->deskripsi }}</p>
                                    <p class="text-xs text-gray-400">{{ $service->created_at->format('d M Y') }}</p>
                                </div>
                                <span class="font-bold text-red-500 text-sm">- Rp {{ number_format($service->biaya, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 text-sm py-4 italic">Belum ada riwayat service.</p>
                        @endforelse
                    </div>
                </div>

            </div>
            @else
            <div class="flex-1 flex items-center justify-center">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>
            @endif
        </div>

        <form method="dialog" class="modal-backdrop bg-black/20 backdrop-blur-sm">
            <button>close</button>
        </form>
    </dialog>

    <script>
        window.addEventListener('show-modal-detail', event => {
            document.getElementById('modal_detail_hp').showModal();
        })
    </script>
</div>
