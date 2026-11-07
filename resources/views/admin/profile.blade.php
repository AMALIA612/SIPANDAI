@extends('layouts.app')

@section('title', 'Profil Administrator - SIPANDAI')

@section('content')
<div class="space-y-6 max-w-none">

    <!-- Header Halaman -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Profil Akun Administrator</h1>
            <p class="text-sm text-slate-500 mt-1">Perbarui informasi kontak dan keamanan akun administrator UPT Gandul</p>
        </div>
    </div>

    <!-- Form Profil -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{route('admin.profile.update')}}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{old('name', $user->name)}}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:outline-none focus:border-pln-blue focus:ring-2 focus:ring-pln-light transition">
                    @error('name') <span class="text-xs font-semibold text-rose-500 mt-1 block">{{$message}}</span> @enderror
                </div>

                <!-- Email PLN -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{old('email', $user->email)}}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:outline-none focus:border-pln-blue focus:ring-2 focus:ring-pln-light transition">
                    @error('email') <span class="text-xs font-semibold text-rose-500 mt-1 block">{{$message}}</span> @enderror
                </div>

                <!-- Nomor HP / WhatsApp -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nomor HP / WhatsApp</label>
                    <input type="text" name="phone" value="{{old('phone', $user->phone)}}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:outline-none focus:border-pln-blue focus:ring-2 focus:ring-pln-light transition">
                    @error('phone') <span class="text-xs font-semibold text-rose-500 mt-1 block">{{$message}}</span> @enderror
                </div>

                <!-- Role / Hak Akses (Readonly) -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Hak Akses Sistem</label>
                    <input type="text" value="Administrator UPT Gandul" disabled
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <hr class="border-slate-100 my-4">

            <!-- Bagian Ubah Kata Sandi -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 text-pln-navy font-bold text-sm">
                    <i class="fa-solid fa-lock text-pln-blue"></i>
                    <span>UBAH KATA SANDI (KOSONGKAN JIKA TIDAK DIUBAH)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 1. Kata Sandi Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kata Sandi Saat Ini</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" placeholder="Masukkan sandi saat ini..."
                                   class="w-full pl-4 pr-11 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:outline-none focus:border-pln-blue focus:ring-2 focus:ring-pln-light transition">
                            <button type="button" onclick="toggleProfilePassword('current_password', 'icon_current_password')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 cursor-pointer">
                                <i id="icon_current_password" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('current_password') <span class="text-xs font-semibold text-rose-500 mt-1 block">{{$message}}</span> @enderror
                    </div>

                    <!-- 2. Kata Sandi Baru -->
                    <div>
                        <label for="new_password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                        <div class="relative">
                            <input type="password" id="new_password" name="password" placeholder="Minimal 6 karakter..."
                                   class="w-full pl-4 pr-11 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:outline-none focus:border-pln-blue focus:ring-2 focus:ring-pln-light transition">
                            <button type="button" onclick="toggleProfilePassword('new_password', 'icon_new_password')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 cursor-pointer">
                                <i id="icon_new_password" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password') <span class="text-xs font-semibold text-rose-500 mt-1 block">{{$message}}</span> @enderror
                    </div>

                    <!-- 3. Konfirmasi Kata Sandi Baru -->
                    <div class="md:col-span-2">
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi Baru</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi sandi baru..."
                                   class="w-full pl-4 pr-11 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:outline-none focus:border-pln-blue focus:ring-2 focus:ring-pln-light transition">
                            <button type="button" onclick="toggleProfilePassword('password_confirmation', 'icon_password_confirmation')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 cursor-pointer">
                                <i id="icon_password_confirmation" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Indikator Validasi Kekuatan Password -->
                    <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs space-y-2">
                        <div class="font-bold text-slate-700 mb-1">Ketentuan Kata Sandi Baru:</div>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 font-medium">
                            <li id="length-check" class="flex items-center text-slate-400">
                                <i class="fa-solid fa-circle-xmark mr-2 text-rose-500"></i> Minimal 6 Karakter
                            </li>
                            <li id="capital-check" class="flex items-center text-slate-400">
                                <i class="fa-solid fa-circle-xmark mr-2 text-rose-500"></i> Ada Huruf Besar (Kapital)
                            </li>
                            <li id="number-check" class="flex items-center text-slate-400">
                                <i class="fa-solid fa-circle-xmark mr-2 text-rose-500"></i> Ada Angka
                            </li>
                            <li id="symbol-check" class="flex items-center text-slate-400">
                                <i class="fa-solid fa-circle-xmark mr-2 text-rose-500"></i> Ada Simbol (Contoh: @, #, $, dll)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-pln-blue hover:bg-pln-hover text-white text-sm font-bold shadow-sm transition flex items-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Script Toggle Mata & Validasi Password Interaktif -->
<script>
function toggleProfilePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.getElementById('new_password').addEventListener('input', function(e) {
    const val = e.target.value;
    updateRule('length-check', val.length >= 6);
    updateRule('capital-check', /[A-Z]/.test(val));
    updateRule('number-check', /[0-9]/.test(val));
    updateRule('symbol-check', /[^A-Za-z0-9]/.test(val));
});

function updateRule(elementId, isValid) {
    const el = document.getElementById(elementId);
    const icon = el.querySelector('i');
    if (isValid) {
        el.classList.remove('text-slate-400');
        el.classList.add('text-emerald-600', 'font-bold');
        icon.className = 'fa-solid fa-circle-check mr-2 text-emerald-500';
    } else {
        el.classList.remove('text-emerald-600', 'font-bold');
        el.classList.add('text-slate-400');
        icon.className = 'fa-solid fa-circle-xmark mr-2 text-rose-500';
    }
}
</script>
@endsection