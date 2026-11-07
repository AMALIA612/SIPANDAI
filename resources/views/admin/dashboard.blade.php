@extends('layouts.app')

@section('title', 'Dashboard Admin UPT Gandul - SIPANDAI')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let pieChart, barChart, categoryChart;

const totalAllComplaints = {{ $stats['total'] ?? 0 }};
const totalSelesai = {{ $stats['selesai'] ?? 0 }};
const initialResolutionRate = totalAllComplaints > 0 
    ? ((totalSelesai / totalAllComplaints) * 100).toFixed(1).replace('.0', '') + '%' 
    : '0%';

// Default tengah Donut menampilkan KPI Persentase Penyelesaian (bukan mengulang angka 80)
let donutCenterState = {
    label: 'TINGKAT SELESAI',
    mainText: initialResolutionRate,
    subtext: 'Rasio Penyelesaian',
    color: '#059669'
};

document.addEventListener('DOMContentLoaded', function() {
    if (window.location.search.includes('filter=')) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    const baseColors = ['#f59e0b', '#06b6d4', '#0284c7', '#10b981', '#ef4444'];
    const dimColors  = [
        'rgba(245, 158, 11, 0.16)',
        'rgba(6, 182, 212, 0.16)',
        'rgba(2, 132, 199, 0.16)',
        'rgba(16, 185, 129, 0.16)',
        'rgba(239, 68, 68, 0.16)'
    ];

    const statusMeta = {
        'all':          { index: -1, label: 'TINGKAT SELESAI',  color: '#059669', barColor: '#0284c7' },
        'baru':         { index: 0,  label: 'PENGADUAN BARU',   color: '#d97706', barColor: '#f59e0b' },
        'diverifikasi': { index: 1,  label: 'DIVERIFIKASI',     color: '#0891b2', barColor: '#06b6d4' },
        'diproses':     { index: 2,  label: 'DALAM PENANGANAN', color: '#0284c7', barColor: '#0284c7' },
        'selesai':      { index: 3,  label: 'SELESAI',          color: '#059669', barColor: '#10b981' },
        'ditolak':      { index: 4,  label: 'DITOLAK',          color: '#e11d48', barColor: '#ef4444' }
    };

    // Plugin Tengah Donut: Fokus menampilkan Persentase (%) agar tidak redundan dengan angka di Kartu
    const centerTextPlugin = {
        id: 'centerTextPlugin',
        afterDraw(chart) {
            const { ctx } = chart;
            const meta = chart.getDatasetMeta(0);
            if (!meta || !meta.data || !meta.data[0]) return;

            const centerX = meta.data[0].x;
            const centerY = meta.data[0].y;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // 1. Label Status di Atas
            ctx.font = 'bold 10px Inter, system-ui, sans-serif';
            ctx.fillStyle = '#64748b';
            ctx.fillText(donutCenterState.label, centerX, centerY - 20);

            // 2. Persentase Utama di Tengah
            ctx.font = '900 26px Inter, system-ui, sans-serif';
            ctx.fillStyle = donutCenterState.color;
            ctx.fillText(donutCenterState.mainText, centerX, centerY + 2);

            // 3. Keterangan Singkat di Bawah
            ctx.font = '600 11px Inter, system-ui, sans-serif';
            ctx.fillStyle = '#94a3b8';
            ctx.fillText(donutCenterState.subtext, centerX, centerY + 22);

            ctx.restore();
        }
    };

    // 1. Pie / Doughnut Chart
    const ctxPie = document.getElementById('statusPieChart').getContext('2d');
    pieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Baru', 'Diverifikasi', 'Sedang Diproses', 'Selesai', 'Ditolak'], 
            datasets: [{
                data: [
                    {{ $statusCounts['baru'] }},
                    {{ $statusCounts['diverifikasi'] }},
                    {{ $statusCounts['diproses'] }},
                    {{ $statusCounts['selesai'] }},
                    {{ $statusCounts['ditolak'] }}
                ],
                backgroundColor: [...baseColors],
                hoverBackgroundColor: [...baseColors],
                borderWidth: 2,
                borderColor: '#ffffff',
                offset: [0, 0, 0, 0, 0],
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            layout: { padding: 10 },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 750,
                easing: 'easeOutQuart'
            },
            onClick: (evt, activeElements) => {
                if (activeElements.length > 0) {
                    const idx = activeElements[0].index;
                    const filterKeys = ['baru', 'diverifikasi', 'diproses', 'selesai', 'ditolak'];
                    const targetCard = document.querySelector(`.filter-card[data-filter="${filterKeys[idx]}"]`);
                    if (targetCard) targetCard.click();
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 14, font: { size: 11, family: 'Inter' } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const val = context.parsed;
                            const pct = totalAllComplaints > 0 ? ((val / totalAllComplaints) * 100).toFixed(1) : 0;
                            return ` ${context.label}: ${val} laporan (${pct}%)`;
                        }
                    }
                }
            }
        },
        plugins: [centerTextPlugin]
    });

    // 2. Bar Chart Bulanan
    const ctxBar = document.getElementById('monthlyBarChart').getContext('2d');
    barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Jumlah Pengaduan Masuk',
                data: {!! json_encode(array_values($monthlyCounts)) !!},
                backgroundColor: '#0284c7',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 700, easing: 'easeOutQuart' },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            },
            plugins: { legend: { display: false } }
        }
    });

    // 3. Horizontal Bar Chart Kategori (Terbanyak di Atas)
    const categoryCanvas = document.getElementById('categoryBarChart');
    if (categoryCanvas) {
        const ctxCategory = categoryCanvas.getContext('2d');
        const categoryLabels = {!! json_encode($categoryChartLabels ?? []) !!};
        const categoryCounts = {!! json_encode($categoryChartCounts ?? []) !!};

        categoryChart = new Chart(ctxCategory, {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Jumlah Pengaduan',
                    data: categoryCounts,
                    backgroundColor: ['#0284c7', '#0ea5e9', '#06b6d4', '#14b8a6', '#10b981', '#6366f1', '#8b5cf6', '#f59e0b', '#ec4899', '#64748b'],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 700, easing: 'easeOutQuart' },
                layout: { padding: { right: 65 } },
                scales: {
                    x: { beginAtZero: true, grace: '10%', ticks: { precision: 0, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
                    y: { 
                        grid: { display: false }, 
                        ticks: { font: { size: 12, weight: '600' }, color: '#334155' } 
                    }
                },
                plugins: { legend: { display: false } }
            },
            plugins: [{
                id: 'barPercentageLabels',
                afterDatasetsDraw(chart) {
                    const { ctx, data } = chart;
                    const meta = chart.getDatasetMeta(0);
                    const totalSum = data.datasets[0].data.reduce((a, b) => a + b, 0);
                    meta.data.forEach((bar, index) => {
                        const val = data.datasets[0].data[index];
                        const pct = totalSum > 0 ? Math.round((val / totalSum) * 100) : 0;
                        const text = val + ' (' + pct + '%)';
                        ctx.save();
                        ctx.font = 'bold 11px Inter, system-ui, sans-serif';
                        ctx.fillStyle = '#475569';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(text, bar.x + 8, bar.y);
                        ctx.restore();
                    });
                }
            }]
        });
    }

    const ringColors = {
        'all': ['ring-2', 'ring-pln-blue', 'shadow-md'],
        'baru': ['ring-2', 'ring-amber-500', 'shadow-md'],
        'diverifikasi': ['ring-2', 'ring-cyan-500', 'shadow-md'],
        'diproses': ['ring-2', 'ring-blue-600', 'shadow-md'],
        'selesai': ['ring-2', 'ring-emerald-500', 'shadow-md'],
        'ditolak': ['ring-2', 'ring-rose-500', 'shadow-md']
    };

    document.querySelectorAll('.filter-card').forEach(card => {
        card.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            document.querySelectorAll('.filter-card').forEach(c => {
                c.classList.remove('ring-2', 'ring-pln-blue', 'ring-amber-500', 'ring-cyan-500', 'ring-blue-600', 'ring-emerald-500', 'ring-rose-500', 'shadow-md');
            });

            if (ringColors[filter]) {
                this.classList.add(...ringColors[filter]);
            }

            fetch(`{{ route('admin.dashboard') }}?filter=${filter}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const meta = statusMeta[filter] || statusMeta['all'];
                const totalAll = data.totalAll > 0 ? data.totalAll : 1;

                // 1. Update Donut Chart (Menampilkan % Porsi, tidak mengulang angka di kartu)
                if (filter === 'all') {
                    pieChart.data.datasets[0].backgroundColor = [...baseColors];
                    pieChart.data.datasets[0].offset = [0, 0, 0, 0, 0];
                    donutCenterState = {
                        label: 'TINGKAT SELESAI',
                        mainText: initialResolutionRate,
                        subtext: 'Rasio Penyelesaian',
                        color: '#059669'
                    };
                } else {
                    pieChart.data.datasets[0].backgroundColor = baseColors.map((col, idx) => idx === meta.index ? col : dimColors[idx]);
                    pieChart.data.datasets[0].offset = [0, 0, 0, 0, 0].map((_, idx) => idx === meta.index ? 16 : 0);

                    const pct = ((data.totalFiltered / totalAll) * 100).toFixed(1).replace('.0', '') + '%';
                    donutCenterState = {
                        label: meta.label,
                        mainText: pct,
                        subtext: 'Porsi dari Total',
                        color: meta.color
                    };
                }
                pieChart.update();

                // 2. Update Bar Chart Bulanan
                barChart.data.datasets[0].data = data.monthlyCounts;
                barChart.data.datasets[0].backgroundColor = meta.barColor;
                barChart.update();

                // 3. Update Bar Chart Kategori
                categoryChart.data.labels = data.categoryLabels;
                categoryChart.data.datasets[0].data = data.categoryCounts;
                categoryChart.update();

                // 4. Update rata-rata bulanan & kategori terbanyak
                document.getElementById('bar-avg-text').innerText = data.avgMonthly;

                const topCatEl = document.getElementById('top-category-info');
                if (data.topCategory) {
                    topCatEl.innerHTML = `Kategori Terbanyak: <strong class="text-slate-800 font-bold">${data.topCategory.name}</strong> (${data.topCategory.count} laporan &bull; ${data.topCategory.pct}%)`;
                } else {
                    topCatEl.innerHTML = `Belum ada laporan pada status ini`;
                }
            })
            .catch(err => console.error('Gagal memuat filter:', err));
        });
    });
});
</script>
@endpush

@section('content')
<div class="w-full space-y-6">

    <!-- Header Dashboard Admin -->
    <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-pln-navy text-white text-sm font-semibold mb-2">
                <span>PANEL ADMINISTRATOR UPT GANDUL</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                SIPANDAI &mdash; ADMIN UPT GANDUL
            </h1>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.complaints.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-pln-blue hover:bg-pln-hover text-white font-bold text-sm shadow transition flex items-center">
                Kelola Pengaduan
            </a>
            <a href="{{ route('admin.reports.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm border border-slate-200 transition flex items-center">
                Rekap Laporan
            </a>
        </div>
    </div>

    <!-- 6 KARTU STATISTIK (MENAMPILKAN JUMLAH ANGKA ABSOLUT) -->
    <div id="stats-cards-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        
        <div data-filter="all" class="filter-card bg-white p-4 rounded-2xl border-t-4 border-pln-blue border border-slate-200 ring-2 ring-pln-blue shadow-md hover:shadow-md transition cursor-pointer select-none">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Pengaduan</span>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total'] }}</div>
            <span class="text-xs font-semibold text-slate-600 mt-1 block">Seluruh laporan</span>
        </div>

        <div data-filter="baru" class="filter-card bg-white p-4 rounded-2xl border-t-4 border-amber-500 border border-slate-200 shadow-2xs hover:shadow-md transition cursor-pointer select-none">
            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block">Pengaduan Baru</span>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['baru'] }}</div>
            <span class="text-xs font-semibold text-slate-600 mt-1 block">Perlu verifikasi</span>
        </div>

        <div data-filter="diverifikasi" class="filter-card bg-white p-4 rounded-2xl border-t-4 border-cyan-500 border border-slate-200 shadow-2xs hover:shadow-md transition cursor-pointer select-none">
            <span class="text-xs font-bold text-cyan-700 uppercase tracking-wider block">Verifikasi</span>
            <div class="text-2xl font-black text-cyan-600 mt-1">{{ $stats['diverifikasi'] }}</div>
            <span class="text-xs font-semibold text-slate-600 mt-1 block">Pemeriksaan awal</span>
        </div>

        <div data-filter="diproses" class="filter-card bg-white p-4 rounded-2xl border-t-4 border-blue-600 border border-slate-200 shadow-2xs hover:shadow-md transition cursor-pointer select-none">
            <span class="text-xs font-bold text-blue-700 uppercase tracking-wider block">Dalam Penanganan</span>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $stats['diproses'] }}</div>
            <span class="text-xs font-semibold text-slate-600 mt-1 block">Tindak lanjut</span>
        </div>

        <div data-filter="selesai" class="filter-card bg-white p-4 rounded-2xl border-t-4 border-emerald-500 border border-slate-200 shadow-2xs hover:shadow-md transition cursor-pointer select-none">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Selesai</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['selesai'] }}</div>
            <span class="text-xs font-semibold text-slate-600 mt-1 block">Telah rampung</span>
        </div>

        <div data-filter="ditolak" class="filter-card bg-white p-4 rounded-2xl border-t-4 border-rose-500 border border-slate-200 shadow-2xs hover:shadow-md transition cursor-pointer select-none">
            <span class="text-xs font-bold text-rose-600 uppercase tracking-wider block">Ditolak</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['ditolak'] }}</div>
            <span class="text-xs font-semibold text-slate-600 mt-1 block">Tidak sesuai</span>
        </div>
    </div>

    <!-- Grafik Distribusi (Menampilkan % Proporsi) & Bulanan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-1 flex items-center">
                    <i class="fa-solid fa-chart-pie text-pln-blue mr-2"></i> Proporsi Status Laporan
                </h3>
            </div>
            <div class="h-64 my-auto relative flex items-center justify-center">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center mb-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-1 flex items-center">
                        <i class="fa-solid fa-chart-simple text-pln-blue mr-2"></i> Pengaduan Bulanan Tahun {{ date('Y') }}
                    </h3>
                </div>
                <span class="text-[11px] px-2.5 py-1 bg-slate-100 text-slate-600 font-semibold rounded-lg">
                    Jan &ndash; Des {{ date('Y') }}
                </span>
            </div>
            <div class="h-64 my-4 relative">
                <canvas id="monthlyBarChart"></canvas>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                <span>Rata-rata: <strong id="bar-avg-text">{{ round($stats['total'] / 12, 1) }}</strong> laporan / bulan</span>
                <a href="{{ route('admin.reports.index') }}" class="text-pln-blue font-bold hover:underline">
                    Lihat Rekap Tabel & Export &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Grafik Kategori -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-5 border-b border-slate-100 gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-1 flex items-center">
                    <i class="fa-solid fa-layer-group text-pln-blue mr-2"></i> Statistik Pengaduan Berdasarkan Kategori
                </h3>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs px-3 py-1.5 bg-slate-100 text-slate-600 font-semibold rounded-xl border border-slate-200/60">
                    <i class="fa-solid fa-list-check mr-1 text-slate-400"></i>
                    {{ count($categoryChartLabels ?? []) }} Kategori Terdata
                </span>
            </div>
        </div>

        <div class="pt-6">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                    Grafik Volume per Kategori
                </span>
                <span class="text-[11px] text-slate-400 font-medium">
                    Jumlah laporan dan persentase
                </span>
            </div>
            
            <div class="h-80 relative w-full">
                <canvas id="categoryBarChart"></canvas>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span id="top-category-info">
                    @php
                        $topCategory = ($categoryStats ?? collect())->first();
                        $totalAll = ($stats['total'] ?? 0) > 0 ?$stats['total'] : 1;
                    @endphp
                    @if($topCategory &&$topCategory->complaints_count > 0)
                        Kategori Terbanyak: <strong class="text-slate-800 font-bold">{{ $topCategory->name }}</strong> ({{$topCategory->complaints_count }} laporan &bull; {{ round(($topCategory->complaints_count / $totalAll) * 100) }}%)
                    @else
                        Belum ada laporan kategori
                    @endif
                </span>
            </div>
        </div>
    </div>

</div>
@endsection