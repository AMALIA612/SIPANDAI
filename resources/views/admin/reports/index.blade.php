@extends('layouts.app')

@section('title', 'Rekapitulasi & Laporan Pengaduan - Admin UPT Gandul')

@push('styles')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 8mm 8mm 10mm 8mm;
    }
    html, body {
        font-family: Arial, Helvetica, sans-serif !important;
        background-color: #ffffff !important;
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 8.5px !important;
        height: auto !important;
        min-height: 0 !important;
        overflow: visible !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    /* Pastikan sidebar, backdrop gelap, dan elemen web tidak pernah muncul di mode cetak */
    #sidebar-backdrop, #main-sidebar, aside, header, .no-print {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        width: 0 !important;
    }
    /* Matikan semua flex container & pembatas overflow agar browser mencetak seluruh baris (1 s.d. 75) tanpa terpotong */
    .min-h-screen, 
    .flex, 
    .flex-1, 
    .flex-col, 
    .overflow-x-hidden, 
    .space-y-6,
    .report-table-box, 
    .report-scroll-wrapper {
        display: block !important;
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow: visible !important;
        box-shadow: none !important;
        border: none !important;
        background: #ffffff !important;
        background-color: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .print-table {
        width: 100% !important;
        table-layout: auto !important;
        border-collapse: collapse !important;
        margin-top: 4px !important;
        background: #ffffff !important;
    }
    .print-table thead {
        display: table-header-group !important; /* Mengulang header di setiap halaman cetak */
    }
    .print-table tbody {
        display: table-row-group !important;
    }
    .print-table tr {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
        background: #ffffff !important;
    }
    .print-table th, 
    .print-table td {
        border: 1px solid #334155 !important;
        padding: 3.5px 3px !important;
        font-size: 7.5px !important;
        line-height: 1.2 !important;
        color: #0f172a !important;
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        vertical-align: top !important;
        background: #ffffff !important;
    }
    .print-table th {
        background-color: #f1f5f9 !important;
        font-weight: 800 !important;
        text-align: center !important;
        text-transform: uppercase !important;
        font-size: 7.5px !important;
        letter-spacing: 0.01em !important;
    }
    .print-only {
        display: block !important;
    }
    .ttd-block {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
        margin-top: 14px !important;
    }
}
</style>
@endpush

@section('content')
@php
    $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
@endphp

<div class="w-full space-y-6">

    <!-- ==================== KOP SURAT RESMI DINAS KHUSUS CETAK ==================== -->
    <div class="hidden print-only mb-3">
        <table style="width: 100%; border: none !important; border-collapse: collapse; margin-bottom: 6px; background: transparent;">
            <tr>
                <td style="width: 60px; vertical-align: middle; border: none !important; padding: 0 16px 0 0; background: transparent;">
                    <img src="{{ asset('images/logo-pln.png') }}" alt="Logo PLN" 
                         style="height: 52px; width: auto; max-height: 52px; object-fit: contain;">
                </td>
                <td style="vertical-align: middle; border: none !important; padding: 0; line-height: 1.25; color: #0f172a; background: transparent;">
                    <h1 style="font-size: 14px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase; margin: 0; color: #0f172a;">PT PLN (PERSERO)</h1>
                    <h2 style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin: 2px 0 0 0; color: #1e293b;">UNIT INDUK TRANSMISI JAWA BAGIAN BARAT</h2>
                    <h3 style="font-size: 11px; font-weight: 700; text-transform: uppercase; margin: 1px 0 0 0; color: #334155;">UNIT PELAKSANA TRANSMISI GANDUL</h3>
                    <p style="font-size: 9px; color: #475569; margin: 3px 0 0 0; line-height: 1.3;">
                        Jl. Raya Gandul No. 1, Cinere, Kota Depok, Jawa Barat 16512 &bull; Telp. (021) 7540019 &bull; Website: www.pln.co.id
                    </p>
                </td>
            </tr>
        </table>
        
        <!-- Garis Ganda Kop Surat Resmi Dinas -->
        <div style="border-bottom: 2.5px solid #0f172a; width: 100%;"></div>
        <div style="border-bottom: 1px solid #0f172a; width: 100%; margin-top: 1.5px; margin-bottom: 8px;"></div>

        <!-- Judul Dokumen Cetak -->
        <div style="text-align: center; margin: 8px 0;">
            <h2 style="font-size: 12.5px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; text-decoration: underline; margin: 0; color: #0f172a;">
                LAPORAN REKAPITULASI PENGADUAN INTERNAL PEGAWAI
            </h2>
            <p style="font-size: 10px; font-weight: 700; color: #334155; margin: 2px 0 0 0; text-transform: uppercase;">
                SISTEM PENGADUAN ANTAR DAN INTERNAL (SIPANDAI)
            </p>
        </div>

        <!-- Ringkasan Informasi Dokumen Formal -->
        <table style="width: 100%; border: 1px solid #64748b; border-collapse: collapse; font-size: 9.5px; margin-bottom: 8px; background: #ffffff;">
            <tr style="background-color: #f8fafc;">
                <td style="padding: 4px 8px; font-weight: bold; width: 18%; border: 1px solid #64748b;">Periode Laporan</td>
                <td style="padding: 4px 8px; width: 32%; border: 1px solid #64748b;">: {{ $selectedMonth ? $monthNames[$selectedMonth] : 'Seluruh Bulan' }} {{ $selectedYear }}</td>
                <td style="padding: 4px 8px; font-weight: bold; width: 18%; border: 1px solid #64748b;">Total Pengaduan</td>
                <td style="padding: 4px 8px; width: 32%; font-weight: bold; border: 1px solid #64748b;">: {{ $statusSummary['total'] }} Laporan</td>
            </tr>
            <tr>
                <td style="padding: 4px 8px; font-weight: bold; border: 1px solid #64748b;">Filter Kategori</td>
                <td style="padding: 4px 8px; border: 1px solid #64748b;">: {{ $selectedCategory ? ($categories->firstWhere('id', $selectedCategory)?->name ?? 'Semua Kategori') : 'Semua Kategori' }}</td>
                <td style="padding: 4px 8px; font-weight: bold; border: 1px solid #64748b;">Rincian Status</td>
                <td style="padding: 4px 8px; border: 1px solid #64748b; font-size: 9px;">
                    : Selesai: {{ $statusSummary['selesai'] }} | Proses: {{ $statusSummary['diproses'] }} | Baru: {{ $statusSummary['baru'] }}
                </td>
            </tr>
        </table>
    </div>

    <!-- ==================== HEADER WEB & TOMBOL AKSI (NO PRINT) ==================== -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Rekapitulasi Laporan & Statistik</h1>
            <p class="text-sm text-slate-500 mt-1">Filter, analisis data pengaduan pegawai, dan ekspor laporan berkala UPT Gandul</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Cetak A4 Tegak (Portrait) -->
            <button type="button" onclick="printReport('portrait')" 
                    title="Cetak format HVS A4 Tegak biasa"
                    class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm hover:shadow transition flex items-center cursor-pointer">
                <i class="fa-solid fa-file-pdf mr-1.5 text-rose-400"></i> Ekspor PDF (.pdf)
            </button>
            <!-- Export Excel (.xls) -->
            <a href="{{ route('admin.reports.export_csv', [
                'month' => $selectedMonth ?: 'all',
                'year' => $selectedYear,
                'category_id' => $selectedCategory ?: '',
                'status' => $selectedStatus ?: '',
            ]) }}" 
               class="px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm hover:shadow transition flex items-center cursor-pointer">
                <i class="fa-solid fa-file-excel mr-1.5"></i> Ekspor Excel (.xls)
            </a>
        </div>
    </div>

<!-- ==================== FORM FILTER PENCARIAN (NO PRINT) ==================== -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs no-print">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
            
            <!-- Pilih Bulan -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pilih Bulan</label>
                <select name="month" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 bg-white focus:outline-none focus:border-pln-blue">
                    <option value="all" {{ ($selectedMonth === null) ? 'selected' : '' }}>Semua Bulan (Setahun Penuh)</option>
                    <option value="1" {{ $selectedMonth == 1 ? 'selected' : '' }}>Januari</option>
                    <option value="2" {{ $selectedMonth == 2 ? 'selected' : '' }}>Februari</option>
                    <option value="3" {{ $selectedMonth == 3 ? 'selected' : '' }}>Maret</option>
                    <option value="4" {{ $selectedMonth == 4 ? 'selected' : '' }}>April</option>
                    <option value="5" {{ $selectedMonth == 5 ? 'selected' : '' }}>Mei</option>
                    <option value="6" {{ $selectedMonth == 6 ? 'selected' : '' }}>Juni</option>
                    <option value="7" {{ $selectedMonth == 7 ? 'selected' : '' }}>Juli</option>
                    <option value="8" {{ $selectedMonth == 8 ? 'selected' : '' }}>Agustus</option>
                    <option value="9" {{ $selectedMonth == 9 ? 'selected' : '' }}>September</option>
                    <option value="10" {{ $selectedMonth == 10 ? 'selected' : '' }}>Oktober</option>
                    <option value="11" {{ $selectedMonth == 11 ? 'selected' : '' }}>November</option>
                    <option value="12" {{ $selectedMonth == 12 ? 'selected' : '' }}>Desember</option>
                </select>
            </div>

            <!-- Pilih Tahun -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tahun</label>
                <select name="year" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 bg-white focus:outline-none focus:border-pln-blue">
                    <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                    @foreach(range(date('Y'), 2024) as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Kategori -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kategori Pengaduan</label>
                <select name="category_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
                    <option value="all" {{ $selectedCategory === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Status -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status Laporan</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
                    <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="baru" {{ $selectedStatus == 'baru' ? 'selected' : '' }}>Pengaduan Baru</option>
                    <option value="diverifikasi" {{ $selectedStatus == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="diproses" {{ $selectedStatus == 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="selesai" {{ $selectedStatus == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ $selectedStatus == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="lg:col-span-2">
                <button type="submit" class="w-full py-2.5 bg-pln-blue hover:bg-pln-hover text-white rounded-xl text-sm font-bold shadow-xs transition cursor-pointer flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
            </div>

        </form>
    </div>
    <!-- ==================== RINGKASAN STATISTIK LAYAR (NO PRINT) ==================== -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3.5 no-print">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 text-center shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase block mb-1">Total Laporan</span>
            <span class="text-xl font-extrabold text-slate-800">{{ $statusSummary['total'] }}</span>
        </div>
        <div class="bg-amber-50 p-4 rounded-2xl border border-amber-200 text-center shadow-2xs">
            <span class="text-xs font-bold text-amber-700 uppercase block mb-1">Baru</span>
            <span class="text-xl font-extrabold text-amber-800">{{ $statusSummary['baru'] }}</span>
        </div>
        <div class="bg-cyan-50 p-4 rounded-2xl border border-cyan-200 text-center shadow-2xs">
            <span class="text-xs font-bold text-cyan-700 uppercase block mb-1">Diverifikasi</span>
            <span class="text-xl font-extrabold text-cyan-800">{{ $statusSummary['diverifikasi'] }}</span>
        </div>
        <div class="bg-blue-50 p-4 rounded-2xl border border-blue-200 text-center shadow-2xs">
            <span class="text-xs font-bold text-blue-700 uppercase block mb-1">Sedang Diproses</span>
            <span class="text-xl font-extrabold text-blue-800">{{ $statusSummary['diproses'] }}</span>
        </div>
        <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-200 text-center shadow-2xs">
            <span class="text-xs font-bold text-emerald-700 uppercase block mb-1">Selesai</span>
            <span class="text-xl font-extrabold text-emerald-800">{{ $statusSummary['selesai'] }}</span>
        </div>
        <div class="bg-rose-50 p-4 rounded-2xl border border-rose-200 text-center shadow-2xs">
            <span class="text-xs font-bold text-rose-700 uppercase block mb-1">Ditolak</span>
            <span class="text-xl font-extrabold text-rose-800">{{ $statusSummary['ditolak'] }}</span>
        </div>
    </div>

    <!-- ==================== TABEL REKAPITULASI DATA ==================== -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden w-full report-table-box">
        
        <!-- Header Atas Tabel (Hanya Layar Web) -->
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center text-sm no-print">
            <div class="font-bold text-slate-700">
                Data Laporan: 
                <span class="text-pln-navy font-extrabold">
                    {{ $selectedMonth ? $monthNames[$selectedMonth] : 'Seluruh Bulan' }} {{ $selectedYear }}
                </span>
                &bull; Total: {{ $complaints->count() }} data
            </div>
            <span class="text-slate-500 text-xs font-medium">Diperbarui: {{ date('d/m/Y H:i') }} WIB</span>
        </div>

        <div class="overflow-x-auto w-full report-scroll-wrapper">
            <table class="w-full text-left text-sm text-slate-700 print-table">
                <thead class="bg-slate-100 uppercase font-bold text-slate-700 border-b border-slate-200 text-xs">
                    <tr>
                        <th class="py-3 px-1.5 text-center" style="width: 4%;">NO.</th>
                        <th class="py-3 px-1.5 text-center" style="width: 12%;">NO. TIKET</th>
                        <th class="py-3 px-1.5 text-center leading-tight" style="width: 10%;">TGL MASUK</th>
                        <th class="py-3 px-2" style="width: 16%;">NIP & NAMA PELAPOR</th>
                        <th class="py-3 px-2" style="width: 14%;">UNIT / BAGIAN</th>
                        <th class="py-3 px-1.5 text-center" style="width: 10%;">KATEGORI</th>
                        <th class="py-3 px-2" style="width: 16%;">JUDUL PENGADUAN</th>
                        <th class="py-3 px-1.5 text-center" style="width: 8%;">STATUS</th>
                        <th class="py-3 px-2" style="width: 10%;">CATATAN TINDAK LANJUT</th>
                        <th class="py-3 px-3 no-print text-right" style="width: 5%;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-normal">
                    @forelse($complaints as $index => $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2 px-1.5 text-center font-bold text-slate-600 print:text-[8px]">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-2 px-1.5 font-mono font-bold text-slate-900 text-center print:text-[7.5px]">
                                {{ $c->ticket_number }}
                            </td>
                            <td class="py-2 px-1.5 text-center text-slate-600 leading-tight">
                                <span class="font-medium block text-slate-700 print:text-[8px]">{{ $c->created_at->format('d/m/Y') }}</span>
                                @if($c->resolved_at)
                                    <span class="text-[9.5px] print:text-[7px] text-emerald-700 font-bold block mt-0.5">
                                        <i class="fa-solid fa-check text-[8px] no-print"></i> Sls: {{ $c->resolved_at->format('d/m/Y') }}
                                    </span>
                                @elseif($c->status === 'selesai' && $c->updated_at)
                                    <span class="text-[9.5px] print:text-[7px] text-emerald-700 font-bold block mt-0.5">
                                        <i class="fa-solid fa-check text-[8px] no-print"></i> Sls: {{ $c->updated_at->format('d/m/Y') }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 px-2">
                                <span class="font-bold text-slate-900 block leading-tight print:text-[8px]">{{ $c->user?->name ?? '-' }}</span>
                                <span class="text-[11px] print:text-[7.5px] text-slate-500 block font-mono">{{ $c->user?->nip ?? '-' }}</span>
                            </td>
                            <td class="py-2 px-2 text-slate-700 leading-tight print:text-[7.5px]">
                                {{ $c->user?->unit_bagian ?? '-' }}
                            </td>
                            <td class="py-2 px-1.5 text-center leading-tight">
                                <!-- Mode Web: Badge Box -->
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 no-print">
                                    {{ $c->category_label }}
                                </span>
                                <!-- Mode Cetak: Teks Bersih -->
                                <span class="hidden print-only font-medium text-[7.5px] text-slate-800">
                                    {{ $c->category_label }}
                                </span>
                            </td>
                            <td class="py-2 px-2 font-medium text-slate-800 break-words leading-tight print:text-[7.5px]">
                                {{ $c->title }}
                            </td>
                            <td class="py-2 px-1.5 text-center leading-tight">
                                <!-- Mode Layar Web: Badge Warna -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $c->status_badge_class }} no-print">
                                    {{ $c->status_label }}
                                </span>
                                <!-- Mode Print: Teks Formal Bersih -->
                                <span class="hidden print-only font-bold text-[7.5px] uppercase">
                                    {{ $c->status_label }}
                                </span>
                            </td>
                            <td class="py-2 px-2 text-slate-600 break-words leading-tight text-[11px] print:text-[7.5px]">
                                {{ $c->admin_notes ?: '-' }}
                            </td>
                            <td class="py-2.5 px-3 no-print text-right whitespace-nowrap">
                                <a href="{{ route('admin.complaints.show', $c->id) }}" class="inline-flex items-center justify-center px-3 py-1 rounded-lg text-xs font-bold bg-pln-blue text-white hover:bg-pln-hover transition shadow-2xs">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-10 text-center text-slate-500 text-xs font-semibold">
                                Tidak ada data pengaduan pada filter periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== LEMBAR PENGESAHAN / TANDA TANGAN DINAS ==================== -->
    <div class="hidden print-only mt-6 ttd-block">
        <table style="width: 100%; border: none !important; border-collapse: collapse; font-size: 9px; color: #000; background: transparent;">
            <tr>
                <td style="width: 50%; text-align: center; border: none !important; vertical-align: top; padding: 0 16px; background: transparent;">
                    <p style="margin: 0;">Mengetahui,</p>
                    <p style="font-weight: bold; text-transform: uppercase; margin: 2px 0 0 0;">Manager UPT Gandul</p>
                    <div style="height: 55px;"></div>
                    <p style="font-weight: bold; text-decoration: underline; margin: 0;">( .................................................... )</p>
                    <p style="font-size: 8px; color: #475569; margin: 2px 0 0 0;">NIP. ....................................................</p>
                </td>
                <td style="width: 50%; text-align: center; border: none !important; vertical-align: top; padding: 0 16px; background: transparent;">
                    <p style="margin: 0;">Depok, {{ date('d') }} {{ $monthNames[(int)date('n')] }} {{ date('Y') }}</p>
                    <p style="font-weight: bold; text-transform: uppercase; margin: 2px 0 0 0;">Pengelola Sistem SIPANDAI</p>
                    <div style="height: 55px;"></div>
                    <p style="font-weight: bold; text-decoration: underline; margin: 0;">{{ Auth::user()->name }}</p>
                    <p style="font-size: 8px; color: #475569; margin: 2px 0 0 0;">NIP. {{ Auth::user()->nip ?: '....................................................' }}</p>
                </td>
            </tr>
        </table>
    </div>

</div>

@push('scripts')
<script>
function printReport(orientation) {
    let style = document.getElementById('print-page-style');
    if (!style) {
        style = document.createElement('style');
        style.id = 'print-page-style';
        document.head.appendChild(style);
    }
    style.innerHTML = `@media print { @page { size: A4 ${orientation}; margin: 8mm 8mm 10mm 8mm; } }`;
    setTimeout(() => {
        window.print();
    }, 50);
}
</script>
@endpush
@endsection