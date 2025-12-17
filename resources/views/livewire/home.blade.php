<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Halo, Bos! 👋</h1>
            <p class="text-gray-500 text-sm">Pantau bisnismu hari ini.</p>
        </div>
        <div class="avatar placeholder">
            <div class="bg-neutral text-neutral-content rounded-full w-10">
                <span class="text-xs">ADM</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <!-- Card 1: Profit Hari Ini -->
        <div class="card bg-white shadow-sm border border-gray-100">
            <div class="card-body p-4">
                <h2 class="card-title text-sm text-gray-500">Profit Hari Ini</h2>
                <p class="text-2xl font-bold {{ $profitToday >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($profitToday, 0, ',', '.') }}
                </p>
                @if($profitTrend != 0)
                    <div class="badge {{ $profitTrend > 0 ? 'badge-success' : 'badge-error' }} badge-outline text-xs">
                        {{ $profitTrend > 0 ? '+' : '' }}{{ number_format($profitTrend, 1) }}%
                    </div>
                @else
                    <div class="badge badge-ghost badge-outline text-xs">Stabil</div>
                @endif
            </div>
        </div>

        <!-- Card 2: Stok Ready -->
        <div class="card bg-white shadow-sm border border-gray-100">
            <div class="card-body p-4">
                <h2 class="card-title text-sm text-gray-500">Stok Ready</h2>
                <p class="text-2xl font-bold text-blue-600">{{ $stokReadyCount }} Unit</p>
                <div class="text-xs text-gray-400">Modal: Rp {{ number_format($totalModalStok, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="card bg-white shadow-sm border border-gray-100 mb-6">
        <div class="card-body p-4">
            <h3 class="font-bold text-gray-800 mb-2">Analisa 7 Hari Terakhir</h3>
            <div 
                x-data="{
                    init() {
                        var options = {
                            series: [{
                                name: 'Modal',
                                data: @js($chartModal)
                            }, {
                                name: 'Keuntungan',
                                data: @js($chartProfit)
                            }],
                            chart: {
                                type: 'bar',
                                height: 250,
                                stacked: true,
                                toolbar: { show: false },
                                fontFamily: 'inherit'
                            },
                            colors: ['#94a3b8', '#16a34a'], // Slate-400 (Modal), Green-600 (Profit)
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    borderRadius: 4,
                                    columnWidth: '60%',
                                },
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                width: 1,
                                colors: ['#fff']
                            },
                            xaxis: {
                                categories: @js($chartDates),
                                labels: {
                                    style: { fontSize: '10px' }
                                },
                                axisBorder: { show: false },
                                axisTicks: { show: false }
                            },
                            yaxis: {
                                labels: {
                                    formatter: function (value) {
                                        if(value >= 1000000) return (value/1000000).toFixed(1) + 'jt';
                                        if(value >= 1000) return (value/1000).toFixed(0) + 'rb';
                                        return value;
                                    },
                                    style: { fontSize: '10px' }
                                }
                            },
                            legend: {
                                position: 'top',
                                fontSize: '12px',
                                markers: { radius: 12 }
                            },
                            fill: {
                                opacity: 1
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val)
                                    }
                                }
                            }
                        };

                        var chart = new ApexCharts(this.$refs.chart, options);
                        chart.render();
                    }
                }"
            >
                <div x-ref="chart"></div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mb-4">
        <h3 class="font-bold text-gray-800 mb-3">Aktivitas Terbaru</h3>
        <div class="space-y-3">
            @forelse($recentActivities as $activity)
                <div class="flex items-center bg-white p-3 rounded-xl shadow-sm border border-gray-50">
                    <div class="w-10 h-10 rounded-full {{ $activity['icon_bg'] }} flex items-center justify-center {{ $activity['icon_text'] }} mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity['icon_path'] }}" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-sm">{{ $activity['title'] }}</h4>
                        <p class="text-xs text-gray-500">{{ $activity['desc'] }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $activity['date']->diffForHumans() }}</span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 text-sm">
                    Belum ada aktivitas terbaru.
                </div>
            @endforelse
        </div>
    </div>
</div>
