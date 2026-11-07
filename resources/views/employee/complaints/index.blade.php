@extends('layouts.app')

@section('title', 'Pengaduan Saya - SIPANDAI')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Daftar Seluruh Pengaduan Saya</h1>
            <p class="text-sm text-slate-500">Pantau seluruh riwayat dan progres pengaduan yang telah Anda kirimkan</p>
        </div>
        <a href="{{ route('employee.complaints.create') }}" 
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-pln-blue hover:bg-pln-hover text-white font-bold text-sm shadow transition">
            <i class="fa-solid fa-plus-circle mr-1.5"></i> Buat Pengaduan Baru
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('employee.complaints.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <!-- Search Keyword -->
            <div class="sm:col-span-5">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nomor tiket, judul, atau lokasi..."
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-3">
                <select name="status" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
                    <option value="">-- Semua Status --</option>
                    <option value="baru" {{ request('status') == 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Filter Kategori -->
            <div class="sm:col-span-3">
                <select name="category_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="sm:col-span-1 flex space-x-1">
                <button type="submit" title="Terapkan Filter" class="w-full py-2 px-3 bg-pln-blue hover:bg-pln-hover text-white rounded-xl text-sm font-semibold flex items-center justify-center">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(request()->anyFilled(['search', 'status', 'category_id']))
                    <a href="{{ route('employee.complaints.index') }}" title="Reset Filter" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengaduan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($complaints->isEmpty())
            <div class="p-12 text-center text-slate-400">
                <i class="fa-regular fa-clipboard text-4xl mb-3 text-slate-300"></i>
                <p class="text-sm font-semibold text-slate-600">Tidak ada pengaduan yang cocok.</p>
                <p class="text-sm text-slate-400 mt-1">Coba ubah kata kunci pencarian atau reset filter.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-5">Nomor Tiket</th>
                            <th class="py-3.5 px-5">Kategori</th>
                            <th class="py-3.5 px-5">Judul Pengaduan</th>
                            <th class="py-3.5 px-5">Lokasi</th>
                            <th class="py-3.5 px-5">Tanggal Masuk</th>
                            <th class="py-3.5 px-5">Status</th>
                            <th class="py-3.5 px-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($complaints as $c)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-5 font-mono font-bold text-pln-navy whitespace-nowrap">
                                    {{ $c->ticket_number }}
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-slate-100 text-slate-700">
                                        {{ $c->category_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 font-semibold text-slate-800 max-w-xs truncate">
                                    {{ $c->title }}
                                </td>
                                <td class="py-3.5 px-5 text-sm text-slate-500 max-w-[150px] truncate">
                                    {{ $c->incident_location }}
                                </td>
                                <td class="py-3.5 px-5 text-sm text-slate-500 whitespace-nowrap">
                                    {{ $c->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-bold border {{ $c->status_badge_class }}">
                                        {{ $c->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                    <a href="{{ route('employee.complaints.show', $c->id) }}" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-pln-light text-pln-navy hover:bg-pln-blue hover:text-white transition">
                                        <i class="fa-solid fa-magnifying-glass-location mr-1"></i> Lacak Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($complaints->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $complaints->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

