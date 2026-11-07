@extends('layouts.app')

@section('title', 'Dashboard Pegawai - SIPANDAI')

@section('content')
<div class="space-y-6">

    <!-- Banner Sambutan & Identitas Pegawai -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
        <div class="relative z-10">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-pln-light/70 text-pln-navy text-sm font-semibold mb-2">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">
                Selamat Datang, {{ $user->name }} 👋
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Portal Pelayanan & Pengaduan Antar Bagian Internal UPT Gandul
            </p>

            <!-- Box Identitas Pegawai -->
            <div class="mt-4 flex flex-wrap gap-2 text-sm font-medium text-slate-600">
                <span class="bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    <i class="fa-solid fa-id-card text-pln-blue mr-1"></i> NIP: <strong>{{ $user->nip ?: '-' }}</strong>
                </span>
                <span class="bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    <i class="fa-solid fa-building text-pln-blue mr-1"></i> Unit: <strong>{{ $user->unit_bagian ?: 'UPT Gandul' }}</strong>
                </span>
                <span class="bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    <i class="fa-solid fa-phone text-pln-blue mr-1"></i> No HP: <strong>{{ $user->phone ?: '-' }}</strong>
                </span>
            </div>
        </div>
    </div>

    <!-- Ringkasan Pengaduan (4 Kartu Statistik) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Pengaduan Baru (Oranye) -->
        <div class="bg-white p-5 rounded-2xl border-l-4 border-amber-500 border border-slate-200 shadow-sm hover:shadow transition">
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Pengaduan Baru</span>
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-inbox"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $stats['baru'] }}</div>
            <p class="text-sm text-slate-400 mt-1">Menunggu verifikasi admin</p>
        </div>

        <!-- Sedang Diproses (Biru) -->
        <div class="bg-white p-5 rounded-2xl border-l-4 border-blue-600 border border-slate-200 shadow-sm hover:shadow transition">
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Sedang Diproses</span>
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $stats['diproses'] }}</div>
            <p class="text-sm text-slate-400 mt-1">Dalam penanganan petugas</p>
        </div>

        <!-- Selesai (Hijau) -->
        <div class="bg-white p-5 rounded-2xl border-l-4 border-emerald-500 border border-slate-200 shadow-sm hover:shadow transition">
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Selesai</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $stats['selesai'] }}</div>
            <p class="text-sm text-slate-400 mt-1">Telah terselesaikan</p>
        </div>

        <!-- Ditolak (Merah) -->
        <div class="bg-white p-5 rounded-2xl border-l-4 border-rose-500 border border-slate-200 shadow-sm hover:shadow transition">
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Ditolak</span>
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $stats['ditolak'] }}</div>
            <p class="text-sm text-slate-400 mt-1">Perlu perbaikan berkas</p>
        </div>

    </div>

    <!-- Riwayat Pengaduan Terkini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-base font-bold text-slate-800">Daftar Pengaduan Terakhir Anda</h2>
                <p class="text-sm text-slate-400">Pantau progres pengaduan yang Anda ajukan</p>
            </div>
            <a href="{{ route('employee.complaints.index') }}" class="text-sm font-bold text-pln-blue hover:underline">
                Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        @if($recentComplaints->isEmpty())
            <div class="p-10 text-center text-slate-400">
                <i class="fa-regular fa-folder-open text-4xl mb-3 text-slate-300"></i>
                <p class="text-sm font-medium">Belum ada riwayat pengaduan yang Anda buat.</p>
                <a href="{{ route('employee.complaints.create') }}" class="inline-block mt-3 text-sm font-bold text-pln-blue underline">
                    Buat pengaduan pertama Anda
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-5">Nomor Tiket</th>
                            <th class="py-3.5 px-5">Kategori</th>
                            <th class="py-3.5 px-5">Judul Pengaduan</th>
                            <th class="py-3.5 px-5">Tanggal Masuk</th>
                            <th class="py-3.5 px-5">Status</th>
                            <th class="py-3.5 px-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentComplaints as $complaint)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-5 font-mono font-bold text-pln-navy whitespace-nowrap">
                                    {{ $complaint->ticket_number }}
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-slate-100 text-slate-700">
                                        {{ $complaint->category_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 font-semibold text-slate-800 max-w-xs truncate">
                                    {{ $complaint->title }}
                                </td>
                                <td class="py-3.5 px-5 text-sm text-slate-500 whitespace-nowrap">
                                    {{ $complaint->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-bold border {{ $complaint->status_badge_class }}">
                                        {{ $complaint->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                    <a href="{{ route('employee.complaints.show', $complaint->id) }}" 
                                       class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-pln-light text-pln-navy hover:bg-pln-blue hover:text-white transition shadow-xs">
                                        <i class="fa-solid fa-magnifying-glass mr-1.5"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection