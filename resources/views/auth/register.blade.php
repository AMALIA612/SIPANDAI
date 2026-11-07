@extends('layouts.app')

@section('title', 'Daftar Akun Pegawai - SIPANDAI')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-6">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden w-full max-w-3xl">
        
        <!-- Header Registrasi -->
        <div class="bg-gradient-to-r from-pln-navy to-pln-blue text-white p-6 sm:p-8 flex items-center justify-between">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-white/20 text-cyan-100 mb-2">
                    Registrasi Mandiri Pegawai
                </span>
                <h1 class="text-2xl font-extrabold tracking-tight">Daftar Akun SIPANDAI</h1>
                <p class="text-sm text-slate-200 mt-1">PT PLN (Persero) UIT JBB - UPT Gandul</p>
            </div>
            <div class="hidden sm:block bg-white/10 p-3 rounded-2xl">
                <i class="fa-solid fa-user-plus text-3xl text-cyan-200"></i>
            </div>
        </div>

        <!-- Form Fields -->
        <form action="{{ route('register') }}" method="POST" class="p-6 sm:p-8 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- NIP -->
                <div>
                    <label for="nip" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        NIP Pegawai <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nip" id="nip" value="{{ old('nip') }}" required
                           placeholder="Contoh: 199203152018021002"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap Sesuai SAP <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           placeholder="Masukkan nama lengkap..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>

                <!-- Unit / Bagian -->
                <div>
                    <label for="unit_bagian" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Unit / Bagian Kerja <span class="text-rose-500">*</span>
                    </label>
                    <select name="unit_bagian" id="unit_bagian" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
                        <option value="">-- Pilih Unit / Bagian --</option>
                        @foreach($unitBagianList as $unit)
                            <option value="{{ $unit }}" {{ old('unit_bagian') == $unit ? 'selected' : '' }}>
                                {{ $unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- No HP / WhatsApp -->
                <div>
                    <label for="phone" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor HP / WhatsApp Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                           placeholder="Contoh: 08123456789"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email (PLN / Aktif) <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           placeholder="nama@pln.co.id"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required minlength="6"
                           placeholder="Minimal 6 karakter..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Ulangi Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                           placeholder="Ulangi kata sandi di atas..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-500 hover:text-pln-navy">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Sudah punya akun? Masuk
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 rounded-xl bg-pln-blue hover:bg-pln-hover text-white font-bold text-sm shadow-md transition">
                    <i class="fa-solid fa-user-check mr-1.5"></i> Buat Akun Pegawai
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

