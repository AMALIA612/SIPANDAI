@extends('layouts.app')



@section('title', 'Buat Pengaduan Baru - SIPANDAI')



@section('content')

<div class="w-full space-y-6">



    <!-- Header Breadcrumb & Judul -->

    <div class="flex items-center justify-between">

        <div>

            <a href="{{ route('employee.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-pln-blue flex items-center gap-1 mb-1">

            </a>

            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Form Buat Pengaduan Baru</h1>

            <p class="text-sm text-slate-500">Isi formulir pengaduan internal dengan lengkap dan jelas</p>

        </div>

       

    </div>



    <!-- Box Form Utama -->

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

       

        <!-- Section 1: Identitas Pelapor (Otomatis dari Akun) -->

        <div class="bg-slate-50 p-6 border-b border-slate-200">

            <div class="flex items-center justify-between mb-3">

                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center">

                    <i class="fa-solid fa-user-shield text-pln-blue mr-2"></i> 1. Identitas Pelapor

                </h2>

                <span class="text-[11px] text-slate-400 italic"></span>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">

                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">

                    <span class="text-slate-700 block text-[13px]">Nama Pelapor</span>

                    <strong class="text-slate-900 text-sm block truncate">{{ $user->name }}</strong>

                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">

                    <span class="text-slate-700 block text-[13px]">NIP Pegawai</span>

                    <strong class="text-slate-800 text-sm block font-mono">{{ $user->nip ?: '-' }}</strong>

                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">

                    <span class="text-slate-700 block text-[13px]">Unit / Bagian</span>

                    <strong class="text-slate-800 text-sm block truncate">{{ $user->unit_bagian ?: '-' }}</strong>

                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">

                    <span class="text-slate-700 block text-[13px]">Nomor WhatsApp / HP</span>

                    <strong class="text-slate-800 text-sm block">{{ $user->phone ?: '-' }}</strong>

                </div>

            </div>

        </div>



        <!-- Section 2: Data Pengaduan -->

        <form action="{{ route('employee.complaints.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">

            @csrf



            <div>

                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4 flex items-center">

                    <i class="fa-solid fa-file-lines text-pln-blue mr-2"></i> 2. Rincian Pengaduan

                </h2>



                <div class="space-y-5">

                   

                    <!-- Kategori Pengaduan (Dropdown + Opsi Lainnya) -->

                    <div>

                        <label for="category_select" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">

                            Kategori Pengaduan <span class="text-rose-500">*</span>

                        </label>

                        <select name="category_type" id="category_select" onchange="toggleCustomCategory(this.value)" required

                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">

                            <option value="">-- Pilih Kategori Pengaduan --</option>

                            @foreach($categories as $cat)

                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>

                                    {{ $cat->name }} {{ $cat->description ? '- ' . $cat->description : '' }}

                                </option>

                            @endforeach

                        </select>

                        <input type="hidden" name="category_id" id="hidden_category_id" value="{{ old('category_id') }}">

                    </div>



                    <!-- Input Kategori Kustom jika memilih 'Lainnya' -->

                    <div id="custom_category_wrapper" class="{{ old('category_type') == 'lainnya' ? '' : 'hidden' }} bg-amber-50/70 p-4 rounded-xl border border-amber-200">

                        <label for="custom_category" class="block text-sm font-bold text-amber-900 uppercase tracking-wider mb-1.5">

                            Sebutkan Nama Kategori Sendiri / Catatan Kategori <span class="text-rose-500">*</span>

                        </label>

                        <input type="text" name="custom_category" id="custom_category" value="{{ old('custom_category') }}"

                               placeholder="Tuliskan kategori yang dimaksud..."

                               class="w-full px-4 py-2.5 rounded-xl border border-amber-300 bg-white text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">

                    </div>



                    <!-- Judul Pengaduan -->

                    <div>

                        <label for="title" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">

                            Judul Pengaduan <span class="text-rose-500">*</span>

                        </label>

                        <input type="text" name="title" id="title" value="{{ old('title') }}" required

                               placeholder="Contoh: Perbaikan AC Ruang Kontrol Lantai 2"

                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">

                    </div>



                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <!-- Lokasi Kejadian -->

                        <div>

                            <label for="incident_location" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">

                                Lokasi Kejadian <span class="text-rose-500">*</span>

                            </label>

                            <input type="text" name="incident_location" id="incident_location" value="{{ old('incident_location') }}" required

                                   placeholder="Contoh: Gedung Utama Lt 2 / GI Gandul"

                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">

                        </div>



                        <!-- Tanggal Kejadian -->

                        <div>

                            <label for="incident_date" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">

                                Tanggal Kejadian <span class="text-rose-500">*</span>

                            </label>

                            <input type="date" name="incident_date" id="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" required

                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">

                        </div>

                    </div>



                    <!-- Deskripsi Pengaduan -->

                    <div>

                        <label for="description" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">

                            Deskripsi Pengaduan <span class="text-rose-500">*</span>

                        </label>

                        <textarea name="description" id="description" rows="4" required

                                  placeholder="Jelaskan secara rinci kronologi atau keluhan yang dialami..."

                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">{{ old('description') }}</textarea>

                    </div>



                    <!-- Upload Foto / Dokumen Pendukung -->

                    <div>

                        <label for="attachment" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">

                            Upload Foto / Dokumen Pendukung (Opsional)

                        </label>

                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-pln-blue transition bg-slate-50/50">

                            <div class="space-y-2 text-center">

                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400"></i>

                                <div class="flex text-sm text-slate-600 justify-center">

                                    <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-bold text-pln-blue hover:text-pln-hover px-2 py-1 border border-slate-200 shadow-2xs">

                                        <span>Pilih Berkas Lampiran</span>

                                        <input id="attachment" name="attachment" type="file" class="sr-only" onchange="previewFileName(this)">

                                    </label>

                                </div>

                                <p id="file_name_display" class="text-sm text-slate-500">

                                    Mendukung JPG, PNG, PDF, DOCX (Maksimal 10MB)

                                </p>

                            </div>

                        </div>

                    </div>



                </div>

            </div>



            <!-- Tombol Kirim Pengaduan & Catatan Sistem -->

            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">

                <div class="text-sm text-slate-500 flex items-center space-x-2">

                    <i class="fa-solid fa-circle-info text-pln-blue text-base"></i>

                    <span>Setelah dikirim, sistem otomatis menerbitkan <strong>Nomor Tiket</strong> resmi.</span>

                </div>

                <button type="submit"

                        class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-pln-blue hover:bg-pln-hover text-white font-bold text-sm shadow-md hover:shadow-lg transition flex items-center justify-center space-x-2">

                    <i class="fa-solid fa-paper-plane"></i>

                    <span>KIRIM PENGADUAN</span>

                </button>

            </div>

        </form>



    </div>

</div>



@push('scripts')

<script>

function toggleCustomCategory(value) {

    const wrapper = document.getElementById('custom_category_wrapper');

    const customInput = document.getElementById('custom_category');

    const hiddenCatId = document.getElementById('hidden_category_id');



    if (value === 'lainnya') {

        wrapper.classList.remove('hidden');

        customInput.setAttribute('required', 'required');

        hiddenCatId.value = '';

    } else {

        wrapper.classList.add('hidden');

        customInput.removeAttribute('required');

        hiddenCatId.value = value;

    }

}



function previewFileName(input) {

    const display = document.getElementById('file_name_display');

    if (input.files && input.files[0]) {

        display.innerHTML = `<span class="font-bold text-emerald-600"><i class="fa-solid fa-file-check mr-1"></i> File dipilih: ${input.files[0].name} (${(input.files[0].size / 1024 / 1024).toFixed(2)} MB)</span>`;

    }

}

</script>

@endpush

@endsection