<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Bisnis HP' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen pb-24"> <!-- pb-24 untuk space bottom nav -->
    
    <!-- Main Content -->
    <main class="container mx-auto p-4 max-w-md">
        {{ $slot }}
    </main>

    <!-- Bottom Navigation -->
    <div class="fixed bottom-0 w-full max-w-md left-1/2 -translate-x-1/2 bg-white border-t border-gray-200 z-50 h-16 flex justify-around items-center shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <!-- ... menu items ... -->
        <!-- Home -->
        <a href="/" wire:navigate class="flex flex-col items-center justify-center w-full h-full {{ request()->is('/') ? 'text-blue-600' : 'text-gray-400 hover:text-blue-600' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <span class="text-[10px] font-medium">Home</span>
        </a>
        
        <!-- Stok -->
        <a href="/stok" wire:navigate class="flex flex-col items-center justify-center w-full h-full {{ request()->is('stok*') ? 'text-blue-600' : 'text-gray-400 hover:text-blue-600' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
            <span class="text-[10px] font-medium">Stok</span>
        </a>

        <!-- Add Button (Center) -->
        <div class="relative -top-5">
            <button onclick="modal_input_stok.showModal()" class="bg-blue-600 text-white rounded-full h-14 w-14 shadow-lg grid place-items-center hover:bg-blue-700 transition-transform active:scale-95 border-4 border-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            </button>
        </div>

        <!-- Penjualan -->
        <a href="/penjualan" wire:navigate class="flex flex-col items-center justify-center w-full h-full {{ request()->is('penjualan*') ? 'text-blue-600' : 'text-gray-400 hover:text-blue-600' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-[10px] font-medium">Penjualan</span>
        </a>

        <!-- Laporan -->
        <a href="/laporan" wire:navigate class="flex flex-col items-center justify-center w-full h-full {{ request()->is('laporan*') ? 'text-blue-600' : 'text-gray-400 hover:text-blue-600' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            <span class="text-[10px] font-medium">Laporan</span>
        </a>
    </div>

    <!-- Global Modal Input Stok -->
    <livewire:input-stok />
    
    <!-- Global Modal Detail HP -->
    <livewire:detail-hp />

</body>
</html>