@extends('layouts.app')

@section('title', 'Kelola Pengaduan - Admin UPT Gandul')

@section('content')
<div class="w-full space-y-6">

    <!-- Header & Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Kelola Seluruh Pengaduan</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar semua pengaduan pegawai UPT Gandul yang masuk ke sistem</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports.index') }}" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold border border-slate-200 transition flex items-center shadow-xs">
                <i class="fa-solid fa-file-excel mr-2 text-emerald-600 text-base"></i> Rekapitulasi Laporan
            </a>
        </div>
    </div>

    <!-- Filter Multi Kriteria -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.complaints.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            
            <!-- Pencarian Kata Kunci -->
            <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cari Tiket / Pelapor</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nomor tiket, nama pelapor, atau judul..."
                       class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pln-blue focus:outline-none">
            </div>

            <!-- Filter Bulan -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Bulan</label>
                <select name="month" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
                    <option value="">Semua Bulan</option>
                    @php
                        $monthList = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                    @endphp
                    @foreach($monthList as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ request('month') == $mNum ? 'selected' : '' }}>
                            {{ $mName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kategori -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Kategori</label>
                <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Status</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="baru" {{ request('status') == 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="lg:col-span-1 flex items-center space-x-2">
                <button type="submit" title="Terapkan Filter" class="w-full py-3 px-3 bg-pln-blue hover:bg-pln-hover text-white rounded-xl text-sm font-bold flex items-center justify-center shadow transition">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(request()->anyFilled(['search', 'month', 'category_id', 'status']))
                    <a href="{{ route('admin.complaints.index') }}" title="Reset Filter" class="py-3 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold flex items-center justify-center transition">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Tabel Data Pengaduan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden w-full">
        @if($complaints->isEmpty())
            <div class="p-12 text-center text-slate-400">
                <i class="fa-regular fa-folder-open text-4xl mb-3 text-slate-300"></i>
                <p class="text-sm font-semibold text-slate-600">Tidak ada pengaduan yang ditemukan.</p>
                <p class="text-sm text-slate-400 mt-1">Coba sesuaikan filter atau kata kunci pencarian.</p>
            </div>
        @else
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-100 uppercase font-bold text-slate-700 border-b border-slate-200 text-xs">
                        <tr>
                            <th class="py-4 px-5">Nomor Tiket</th>
                            <th class="py-4 px-5">Pelapor & Unit</th>
                            <th class="py-4 px-5">Kategori</th>
                            <th class="py-4 px-5">Judul Pengaduan</th>
                            <th class="py-4 px-5">Tanggal Masuk</th>
                            <th class="py-4 px-5">Status</th>
                            <th class="py-4 px-5 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium">
                        @foreach($complaints as $c)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-5 font-mono font-bold text-pln-navy whitespace-nowrap">
                                    {{ $c->ticket_number }}
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-800 text-base">{{ $c->user?->name ?? 'Anonim' }}</div>
                                    <div class="text-xs sm:text-sm text-slate-600 font-mono font-semibold mt-1">
                                        {{ $c->user?->nip ?? '-' }} &bull; {{ $c->user?->unit_bagian ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-slate-100 text-slate-700">
                                        {{ $c->category_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 font-semibold text-slate-800 max-w-xs truncate">
                                    {{ $c->title }}
                                </td>
                                <td class="py-4 px-5 text-sm text-slate-600 whitespace-nowrap font-medium">
                                    {{ $c->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $c->status_badge_class }}">
                                        {{ $c->status_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    <a href="{{ route('admin.complaints.show', $c->id) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-bold bg-pln-blue text-white hover:bg-pln-hover transition shadow-xs">
                                        <i class="fa-solid fa-sliders mr-1.5"></i> Proses / Kelola
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($complaints->hasPages())
                <div class="p-5 border-t border-slate-100 text-sm">
                    {{ $complaints->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection