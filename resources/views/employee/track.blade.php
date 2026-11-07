@extends('layouts.app')

@section('title', 'Lacak Pengaduan - SIPANDAI')

@section('content')
<div class="w-full space-y-6">

    <!-- Pencarian Tiket Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-sm text-center relative overflow-hidden">
        <div class="inline-flex items-center space-x-2 px-3.5 py-1 rounded-full bg-pln-light text-pln-navy text-sm font-bold mb-3">
            <i class="fa-solid fa-magnifying-glass-location text-pln-blue"></i>
            <span>Pelacakan Tiket Cepat</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">
            Lacak Status Pengaduan Anda
        </h1>
        <p class="text-sm sm:text-sm text-slate-500 max-w-lg mx-auto mt-2">
            Masukkan nomor tiket resmi (contoh: <strong>SIP-2026-000125</strong>) untuk melihat perkembangan proses tindak lanjut.
        </p>

        <form action="{{ route('complaints.track') }}" method="GET" class="mt-6 max-w-md mx-auto">
            <div class="flex rounded-2xl shadow-sm border-2 border-slate-200 focus-within:border-pln-blue overflow-hidden transition">
                <input type="text" name="ticket" value="{{ $ticketQuery }}" required
                       placeholder="Contoh: SIP-2026-000125"
                       class="w-full px-5 py-3.5 text-sm font-mono focus:outline-none text-slate-800 uppercase">
                <button type="submit" 
                        class="px-6 bg-pln-blue hover:bg-pln-hover text-white font-bold text-sm transition flex items-center shrink-0">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Hasil Pelacakan -->
    @if($ticketQuery !== '')
        @if($complaint)
            <!-- Ditemukan -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-sm font-bold text-slate-400 uppercase tracking-wider block">Hasil Pelacakan Nomor Tiket</span>
                        <h2 class="text-xl font-mono font-extrabold text-pln-navy">{{ $complaint->ticket_number }}</h2>
                        <span class="text-sm text-slate-500">{{ $complaint->category_label }} &bull; Masuk: {{ $complaint->created_at->format('d F Y') }}</span>
                    </div>
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-sm font-bold border {{ $complaint->status_badge_class }}">
                        Status: {{ $complaint->status_label }}
                    </span>
                </div>

                <!-- Visual Stepper Progress -->
                @php
                    $stepIndex = match($complaint->status) {
                        'baru' => 1,
                        'diverifikasi' => 2,
                        'diproses' => 3,
                        'selesai' => 4,
                        'ditolak' => -1,
                    };
                @endphp

                <div class="py-3">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 rounded-full z-0"></div>
                        @if($stepIndex > 1)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-pln-blue rounded-full z-0"
                                 style="width: {{ $stepIndex == 2 ? '33%' : ($stepIndex == 3 ? '66%' : '100%') }}"></div>
                        @endif

                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $stepIndex >= 1 ? 'bg-pln-blue text-white' : 'bg-slate-200 text-slate-500' }}">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-bold text-slate-700">Dibuat</span>
                        </div>

                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $stepIndex >= 2 ? 'bg-pln-blue text-white' : 'bg-slate-200 text-slate-500' }}">
                                @if($stepIndex >= 2) <i class="fa-solid fa-check"></i> @else 2 @endif
                            </div>
                            <span class="mt-1.5 text-[11px] font-bold text-slate-700">Diverifikasi</span>
                        </div>

                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $stepIndex >= 3 ? 'bg-pln-blue text-white' : 'bg-slate-200 text-slate-500' }}">
                                @if($stepIndex >= 3) <i class="fa-solid fa-check"></i> @else 3 @endif
                            </div>
                            <span class="mt-1.5 text-[11px] font-bold text-slate-700">Ditindaklanjuti</span>
                        </div>

                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $stepIndex >= 4 ? 'bg-emerald-600 text-white' : ($complaint->status == 'ditolak' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-500') }}">
                                @if($stepIndex >= 4) <i class="fa-solid fa-check-double"></i> @elseif($complaint->status == 'ditolak') <i class="fa-solid fa-xmark"></i> @else 4 @endif
                            </div>
                            <span class="mt-1.5 text-[11px] font-bold {{ $complaint->status == 'ditolak' ? 'text-rose-600' : ($stepIndex >= 4 ? 'text-emerald-700' : 'text-slate-700') }}">
                                {{ $complaint->status == 'ditolak' ? 'Ditolak' : 'Selesai' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pokok -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2 text-sm">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Judul Pengaduan:</span>
                        <strong class="text-slate-800 text-sm">{{ $complaint->title }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Lokasi & Tanggal Kejadian:</span>
                        <span class="text-slate-700">{{ $complaint->incident_location }} ({{ $complaint->incident_date?->format('d/m/Y') }})</span>
                    </div>
                    @if($complaint->admin_notes)
                        <div class="pt-2 border-t border-slate-200">
                            <span class="text-pln-navy font-bold block text-[11px]"><i class="fa-solid fa-comment-dots mr-1"></i> Catatan Admin:</span>
                            <span class="text-slate-700">{{ $complaint->admin_notes }}</span>
                        </div>
                    @endif
                </div>

                @auth
                    @if($complaint->user_id == Auth::id())
                        <div class="text-right">
                            <a href="{{ route('employee.complaints.show', $complaint->id) }}" class="text-sm font-bold text-pln-blue hover:underline">
                                Buka Rincian Lengkap Pengaduan Anda <i class="fa-solid fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        @else
            <!-- Tidak Ditemukan -->
            <div class="bg-white rounded-2xl border border-rose-200 p-8 shadow-sm text-center">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-4xl mb-3"></i>
                <h3 class="text-base font-bold text-slate-800">Tiket Tidak Ditemukan</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    Nomor tiket <strong>{{ $ticketQuery }}</strong> tidak ditemukan dalam basis data sistem SIPANDAI. Pastikan nomor tiket ditulis dengan benar.
                </p>
            </div>
        @endif
    @endif

</div>
@endsection

