@extends('layouts.app')

@section('title', 'Lacak Pengaduan #' . $complaint->ticket_number . ' - SIPANDAI')

@section('content')
<div class="w-full space-y-6">

    <!-- Flash Pesan Nomor Tiket Baru Terbit -->
    @if(session('ticket_created'))
        <div class="bg-emerald-600 text-white p-6 rounded-2xl shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <div>
                    <span class="text-sm text-emerald-100 font-semibold uppercase tracking-wider block">Pengaduan Berhasil Dikirim!</span>
                    <h2 class="text-2xl font-mono font-black">{{ session('ticket_created') }}</h2>
                </div>
            </div>
            <button onclick="window.print()" class="px-5 py-2.5 bg-white text-emerald-800 rounded-xl text-sm font-bold shadow hover:bg-emerald-50 transition flex items-center">
                <i class="fa-solid fa-print mr-2"></i> Cetak Bukti Tiket
            </button>
        </div>
    @endif

    <!-- Header & Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                <span>Lacak Status Tiket</span>
                <span class="font-mono text-pln-blue bg-pln-subtle px-4 py-1.5 rounded-xl border border-pln-blue/20 text-xl font-black">
                    {{ $complaint->ticket_number }}
                </span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">Pantau perkembangan linimasa dan tanggapan petugas terkait aduan Anda</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-5 py-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold transition flex items-center shadow-xs">
                <i class="fa-solid fa-print mr-2 text-slate-400 text-base"></i> Cetak Halaman
            </button>
        </div>
    </div>

    <!-- TRACKING PENGADUAN - VISUAL STEPPER TIMELINE -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center">
                <i class="fa-solid fa-timeline text-pln-blue mr-2 text-base"></i> Timeline Perkembangan Laporan
            </h2>
            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold border {{ $complaint->status_badge_class }}">
                Status Saat Ini: {{ $complaint->status_label }}
            </span>
        </div>

        <!-- Horizontal Stepper (Desktop) -->
        <div class="hidden md:block py-6">
            <div class="flex items-center justify-between relative">
                <!-- Garis Penghubung Latar Belakang -->
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-2 bg-slate-200 rounded-full z-0"></div>

                @php
                    $stepIndex = match($complaint->status) {
                        'baru' => 1,
                        'diverifikasi' => 2,
                        'diproses' => 3,
                        'selesai' => 4,
                        'ditolak' => -1,
                    };
                @endphp

                <!-- Garis Progress Aktif -->
                @if($stepIndex > 1)
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-2 bg-pln-blue rounded-full z-0 transition-all duration-500"
                         style="width: {{ $stepIndex == 2 ? '33%' : ($stepIndex == 3 ? '66%' : '100%') }}"></div>
                @endif

                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-base shadow {{ $stepIndex >= 1 ? 'bg-pln-blue text-white ring-4 ring-sky-100' : 'bg-slate-200 text-slate-600' }}">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="mt-3 text-sm font-bold text-slate-800">Pengaduan Dibuat</span>
                    <span class="text-xs text-slate-500 mt-0.5">{{ $complaint->created_at->format('d M Y') }}</span>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-base shadow {{ $stepIndex >= 2 ? 'bg-pln-blue text-white ring-4 ring-sky-100' : 'bg-slate-200 text-slate-600' }}">
                        @if($stepIndex >= 2) <i class="fa-solid fa-check"></i> @else 2 @endif
                    </div>
                    <span class="mt-3 text-sm font-bold text-slate-800">Diverifikasi Admin</span>
                    <span class="text-xs text-slate-500 mt-0.5">Verifikasi berkas</span>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-base shadow {{ $stepIndex >= 3 ? 'bg-pln-blue text-white ring-4 ring-sky-100' : 'bg-slate-200 text-slate-600' }}">
                        @if($stepIndex >= 3) <i class="fa-solid fa-check"></i> @else 3 @endif
                    </div>
                    <span class="mt-3 text-sm font-bold text-slate-800">Ditindaklanjuti</span>
                    <span class="text-xs text-slate-500 mt-0.5">Dalam pengerjaan</span>
                </div>

                <!-- Step 4 -->
                <div class="relative z-10 flex flex-col items-center">
                    @if($complaint->status == 'ditolak')
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-base shadow bg-rose-600 text-white ring-4 ring-rose-100">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                        <span class="mt-3 text-sm font-bold text-rose-600">Ditolak</span>
                        <span class="text-xs text-slate-500 mt-0.5">Perlu revisi</span>
                    @else
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-base shadow {{ $stepIndex >= 4 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-slate-200 text-slate-600' }}">
                            @if($stepIndex >= 4) <i class="fa-solid fa-check-double"></i> @else 4 @endif
                        </div>
                        <span class="mt-3 text-sm font-bold {{ $stepIndex >= 4 ? 'text-emerald-700' : 'text-slate-800' }}">Selesai</span>
                        <span class="text-xs text-slate-500 mt-0.5">
                            {{ $complaint->resolved_at ? $complaint->resolved_at->format('d M Y') : 'Penutupan tiket' }}
                        </span>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Grid Detail Pengaduan & Catatan Tanggapan Admin -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full">

        <!-- Sisi Kiri: Rincian Lengkap Pengaduan (8 Kolom) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-7 sm:p-8 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-5">
                    <span class="text-sm font-bold text-pln-navy uppercase tracking-wider block mb-2">
                        {{ $complaint->category_label }}
                    </span>
                    <h2 class="text-2xl font-extrabold text-slate-900 leading-snug">{{ $complaint->title }}</h2>
                </div>

                <!-- Metadata Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <span class="text-slate-500 block text-xs font-semibold mb-1">Lokasi Kejadian (Catatan)</span>
                        <span class="font-bold text-slate-800 text-base">{{ $complaint->incident_location }}</span>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <span class="text-slate-500 block text-xs font-semibold mb-1">Tanggal Kejadian</span>
                        <span class="font-bold text-slate-800 text-base">{{ $complaint->incident_date?->format('d F Y') }}</span>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Lengkap</h3>
                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-base leading-relaxed whitespace-pre-line font-medium">
                        {{ $complaint->description }}
                    </div>
                </div>

                <!-- Lampiran Bukti dari Pelapor -->
                @if($complaint->attachment)
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">Berkas Lampiran Pelapor</h3>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center space-x-3 text-sm text-slate-700 truncate font-semibold">
                                <i class="fa-solid fa-paperclip text-pln-blue text-lg"></i>
                                <span class="truncate">{{ basename($complaint->attachment) }}</span>
                            </div>
                            <a href="{{ Storage::url($complaint->attachment) }}" target="_blank" 
                               class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-pln-blue hover:bg-pln-subtle transition flex items-center shrink-0 shadow-xs">
                                <i class="fa-solid fa-arrow-up-right-from-square mr-1.5"></i> Buka Berkas
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Riwayat Log Aktivitas / History Logs -->
            <div class="bg-white rounded-2xl border border-slate-200 p-7 sm:p-8 shadow-sm">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-5 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left text-pln-blue mr-2 text-base"></i> Riwayat Aktivitas & Perubahan Status
                </h3>
                <div class="space-y-4">
                    @forelse($complaint->histories as $history)
                        <div class="flex items-start space-x-4 text-sm border-b border-slate-100 pb-4 last:border-0 last:pb-0">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-pln-blue flex items-center justify-center shrink-0 mt-0.5 border border-slate-200">
                                <i class="fa-solid fa-check text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-800 text-base capitalize">{{ $history->status }}</span>
                                    <span class="text-xs text-slate-500 font-medium">{{ $history->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-slate-700 mt-1 font-medium">{{ $history->notes }}</p>
                                @if($history->user)
                                    <span class="text-xs text-slate-400 font-semibold block mt-1">Oleh: {{ $history->user->name }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada riwayat aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Catatan / Tanggapan Admin & Kontak (4 Kolom) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Box Tanggapan Petugas / Admin UPT Gandul -->
            <div class="bg-white rounded-2xl border border-slate-200 p-7 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-comment-dots text-pln-blue mr-2 text-base"></i> Tanggapan / Catatan Admin
                </h3>
                
                @if($complaint->admin_notes)
                    <div class="p-5 rounded-xl bg-sky-50 border border-sky-200 text-slate-800 text-sm leading-relaxed font-medium">
                        <div class="flex items-center space-x-2 text-pln-navy font-bold mb-1.5 text-base">
                            <i class="fa-solid fa-user-tie"></i>
                            <span>Admin UPT Gandul:</span>
                        </div>
                        {{ $complaint->admin_notes }}
                    </div>
                @else
                    <div class="p-6 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center text-sm text-slate-500 font-medium">
                        <i class="fa-regular fa-comment text-3xl mb-2 text-slate-300 block"></i>
                        Belum ada catatan atau tanggapan dari petugas.
                    </div>
                @endif

                <!-- Bukti Penyelesaian dari Admin / Petugas -->
                @if($complaint->resolution_proof)
                    <div class="pt-4 border-t border-slate-100">
                        <span class="text-xs font-bold text-emerald-800 block uppercase mb-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Bukti Penyelesaian Petugas:
                        </span>
                        <a href="{{ Storage::url($complaint->resolution_proof) }}" target="_blank"
                           class="inline-flex items-center px-4 py-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-sm font-bold hover:bg-emerald-100 transition w-full justify-between shadow-xs">
                            <span class="truncate">{{ basename($complaint->resolution_proof) }}</span>
                            <i class="fa-solid fa-download ml-2 text-base shrink-0"></i>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Box Bantuan & Kontak Unit -->
            <div class="bg-gradient-to-br from-pln-subtle to-white rounded-2xl border border-pln-blue/20 p-6 text-sm text-slate-700 shadow-xs space-y-3">
                <h4 class="font-bold text-pln-navy text-base flex items-center">
                    <i class="fa-solid fa-headset mr-2 text-pln-blue text-lg"></i> Butuh Konfirmasi Cepat?
                </h4>
                <p class="text-slate-600 leading-relaxed font-medium">
                    Jika pengaduan Anda berstatus darurat atau memerlukan penanganan segera, hubungi unit SDM & Umum UPT Gandul.
                </p>
                <div class="font-mono text-slate-900 bg-white p-3 rounded-xl border border-slate-200 text-xs font-bold">
                    Sebutkan Nomor Tiket: <strong class="text-pln-navy text-sm">{{ $complaint->ticket_number }}</strong>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection