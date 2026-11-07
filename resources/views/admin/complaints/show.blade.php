@extends('layouts.app')

@section('title', 'Kelola Pengaduan #' . $complaint->ticket_number . ' - Admin UPT Gandul')

@section('content')
<div class="space-y-6">

    <!-- Header & Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('admin.complaints.index') }}" class="text-sm font-semibold text-slate-500 hover:text-pln-blue flex items-center gap-1 mb-1">
            </a>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                <span>Kelola Pengaduan</span>
                <span class="font-mono text-pln-navy bg-pln-subtle px-3 py-1 rounded-xl border border-pln-blue/20 text-lg">
                    {{ $complaint->ticket_number }}
                </span>
            </h1>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-sm font-bold border {{ $complaint->status_badge_class }}">
                Status: {{ $complaint->status_label }}
            </span>
        </div>
    </div>

    <!-- Grid 2 Kolom: Detail Laporan & Form Aksi Gabung Admin -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kolom Kiri: Detail Pengaduan & Identitas Pelapor (7 Kolom) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Kartu Pelapor -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center">
                    <i class="fa-solid fa-id-badge text-pln-blue mr-2"></i> Identitas Pegawai Pelapor
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Lengkap:</span>
                        <strong class="text-slate-800 text-sm">{{ $complaint->user?->name ?? 'Anonim' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">NIP Pegawai:</span>
                        <strong class="text-slate-800 font-mono text-sm">{{ $complaint->user?->nip ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Unit / Bagian:</span>
                        <span class="text-slate-700 font-semibold">{{ $complaint->user?->unit_bagian ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nomor WhatsApp / HP:</span>
                        <span class="text-slate-700 font-semibold">{{ $complaint->user?->phone ?? '-' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 block text-[11px]">Email Resmi:</span>
                        <span class="text-slate-700">{{ $complaint->user?->email ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Kartu Isi Laporan -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
                <div>
                    <span class="text-sm font-bold text-pln-navy uppercase tracking-wider block mb-1">
                        Kategori: {{ $complaint->category_label }}
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 leading-snug">{{ $complaint->title }}</h2>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <span class="text-slate-400 block text-[11px]">Lokasi Kejadian (Catatan)</span>
                        <strong class="text-slate-800">{{ $complaint->incident_location }}</strong>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <span class="text-slate-400 block text-[11px]">Tanggal Kejadian</span>
                        <strong class="text-slate-800">{{ $complaint->incident_date?->format('d F Y') }}</strong>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Pengaduan</h3>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                        {{ $complaint->description }}
                    </div>
                </div>

                @if($complaint->attachment)
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">Berkas Lampiran Pelapor</h3>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-slate-700 truncate">
                                <i class="fa-solid fa-paperclip text-pln-blue text-base"></i>
                                <span class="truncate">{{ basename($complaint->attachment) }}</span>
                            </div>
                            <a href="{{ Storage::url($complaint->attachment) }}" target="_blank" 
                               class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm font-bold text-pln-blue hover:bg-pln-subtle transition flex items-center shrink-0">
                                <i class="fa-solid fa-download mr-1"></i> Unduh Berkas
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Riwayat Log Perubahan -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3 flex items-center">
                    <i class="fa-solid fa-history text-pln-blue mr-2"></i> Log Aktivitas Tiket
                </h3>
                <div class="space-y-3">
                    @foreach($complaint->histories as $history)
                        <div class="flex items-start space-x-3 text-sm border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                            <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-800 capitalize">{{ $history->status }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-slate-600 mt-0.5">{{ $history->notes }}</p>
                                @if($history->user)
                                    <span class="text-[10px] text-slate-400">Oleh: {{ $history->user->name }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: FORM AKSI GABUNG ADMIN (Sesuai Sketsa 5 & Catatan 'GABUNG') -->
        <div class="lg:col-span-5 space-y-6">
            
            <div class="bg-white rounded-2xl border-2 border-pln-blue/30 p-6 sm:p-7 shadow-md">
                <div class="flex items-center space-x-2 text-pln-navy font-extrabold text-sm mb-1">
                    <i class="fa-solid fa-sliders text-pln-blue text-lg"></i>
                    <span>TINDAK LANJUT PENGADUAN</span>
                </div>
                <p class="text-sm text-slate-500 mb-5">
                    Verifikasi laporan, perbarui status proses, berikan catatan tindak lanjut, dan unggah bukti penyelesaian.
                </p>

                <form action="{{ route('admin.complaints.update_status', $complaint->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- 1. Dropdown Update Status (Sesuai Sketsa) -->
                    <div>
                        <label for="status" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Pengaduan <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-300 text-sm font-semibold text-slate-800 focus:border-pln-blue focus:outline-none bg-white">
                            <option value="baru" {{ old('status', $complaint->status) == 'baru' ? 'selected' : '' }}>
                                &bull; Pengaduan Baru (Menunggu diverifikasi)
                            </option>
                            <option value="diverifikasi" {{ old('status', $complaint->status) == 'diverifikasi' ? 'selected' : '' }}>
                                &bull; Diverifikasi (Laporan valid)
                            </option>
                            <option value="diproses" {{ old('status', $complaint->status) == 'diproses' ? 'selected' : '' }}>
                                &bull; Sedang Ditindaklanjuti / Diproses
                            </option>
                            <option value="selesai" {{ old('status', $complaint->status) == 'selesai' ? 'selected' : '' }}>
                                &bull; Selesai (Penanganan rampung)
                            </option>
                            <option value="ditolak" {{ old('status', $complaint->status) == 'ditolak' ? 'selected' : '' }}>
                                &bull; Ditolak (Tidak memenuhi syarat / selesai di luar sistem)
                            </option>
                        </select>
                    </div>

                    <!-- 2. Catatan / Tanggapan Petugas (Sesuai Sketsa) -->
                    <div>
                        <label for="admin_notes" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan / Tanggapan Petugas
                        </label>
                        <textarea name="admin_notes" id="admin_notes" rows="4"
                                  placeholder="Tuliskan tindak lanjut, hasil koordinasi, atau instruksi untuk pegawai..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none text-slate-800">{{ old('admin_notes', $complaint->admin_notes) }}</textarea>
                        <span class="text-[11px] text-slate-400 block mt-1">Tanggapan ini akan dapat dibaca langsung oleh pegawai di halaman tracking.</span>
                    </div>

                    <!-- 3. Upload Bukti Penyelesaian (Sesuai Sketsa) -->
                    <div>
                        <label for="resolution_proof" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Upload Bukti Penyelesaian (Foto / Dokumen Berita Acara)
                        </label>
                        <input type="file" name="resolution_proof" id="resolution_proof"
                               class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-pln-light file:text-pln-navy hover:file:bg-pln-blue hover:file:text-white transition cursor-pointer border border-slate-300 rounded-xl p-1 bg-slate-50">
                        @if($complaint->resolution_proof)
                            <div class="mt-2 text-sm text-emerald-700 flex items-center space-x-1">
                                <i class="fa-solid fa-file-check"></i>
                                <span>Bukti saat ini tersimpan: </span>
                                <a href="{{ Storage::url($complaint->resolution_proof) }}" target="_blank" class="underline font-bold">
                                    Lihat Berkas
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Simpan Pembaruan -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3 px-4 rounded-xl bg-pln-blue hover:bg-pln-hover text-white font-bold text-sm shadow-md transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>SIMPAN PERUBAHAN STATUS</span>
                        </button>
                    </div>

                </form>
            </div>

        </div>

    </div>

</div>
@endsection

