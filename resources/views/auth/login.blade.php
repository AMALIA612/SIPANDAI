@extends('layouts.app')

@section('title', 'SIPANDAI - PT PLN (Persero) UPT Gandul')

@section('content')
<style>
    input::-ms-reveal,
    input::-ms-clear {
        display: none !important;
    }
</style>

<!-- WADAH UTAMA: FULL LAYAR -->
<div class="w-full min-h-screen md:h-full flex flex-col md:flex-row relative bg-slate-50">

    <!-- PANEL KIRI (BRANDING SIPANDAI & LOGO PLN) -->
    <div class="relative z-10 flex flex-col justify-between items-center overflow-hidden w-full md:w-1/2 bg-gradient-to-br from-[#0077b6] via-[#004e80] to-[#002b49] via-pln-navy to-pln-blue text-white p-8 lg:p-14 shrink-0 min-h-[360px] md:min-h-full">

        <!-- LOGO DANANTARA DI POJOK KIRI ATAS -->
        <div class="absolute top-6 left-6 md:top-8 md:left-8 z-20 flex items-center">
            <img src="{{asset('images/logo-danantara.png')}}" alt="Logo Danantara" class="h-10 sm:h-11 w-auto object-contain drop-shadow-sm">
        </div>

        <!-- Dekorasi Gradien Latar -->
        <div class="pointer-events-none">
            <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-cyan-400/10 blur-2xl pointer-events-none"></div>
            <div class="absolute -left-20 -top-20 w-80 h-80 rounded-full bg-blue-400/15 blur-2xl pointer-events-none"></div>
        </div>

        <!-- Wadah Konten Tengah -->
        <div class="w-full flex flex-col items-center text-center justify-center my-auto max-w-lg mx-auto py-4">

            <!-- LOGO PLN -->
            <div class="relative inline-block">
                <div class="relative px-7 py-6 sm:px-9 sm:py-7 rounded-3xl flex items-center justify-center">
                    <img src="{{asset('images/logo-pln.png')}}" alt="Logo PLN" 
                         class="h-20 sm:h-24 object-contain">
                </div>
            </div>

            <!-- Tagline Badge -->
            <div class="mt-6 inline-block px-4 py-1.5 rounded-full text-sm font-bold bg-white/15 text-cyan-200 border border-white/20">
                Portal Pengaduan Internal Pegawai
            </div>

            <!-- Judul Utama: SIPANDAI -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl text-white mt-3 sm:mt-4 font-black tracking-tight leading-tight">
                SIPANDAI
            </h1>

            <!-- Subtitle -->
            <p class="text-base sm:text-lg lg:text-xl text-cyan-100 font-bold mt-1.5">
                Sistem Pengaduan Antar Dan Internal
            </p>

            <p class="block mt-5 text-sm sm:text-base text-cyan-100/90 leading-relaxed max-w-lg mx-auto text-center px-2">
                Layanan terpadu penyampaian aspirasi, kendala kepegawaian, sarana fasilitas kerja, dan pelacakan tindak lanjut laporan secara transparan.
            </p>

        </div>

        <!-- Footer Sisi Kiri -->
        <div class="w-full text-center z-10 mt-auto pt-8 pb-3">
            <p class="text-sm text-cyan-100/80 font-semibold tracking-wide">
                PT PLN (Persero) UIT JBB &bull; UPT Gandul
            </p>
        </div>

    </div>

    <!-- PANEL KANAN (FORMULIR LOGIN) -->
    <div class="bg-white flex flex-col justify-between overflow-y-auto w-full md:w-1/2 p-8 lg:p-14 grow relative">

        <!-- LOGO PLN DI POJOK KANAN ATAS (SEJAJAR DENGAN DANANTARA) -->
        <div class="absolute top-6 right-6 md:top-8 md:right-8 z-20 flex items-center">
            <img src="{{asset('images/logo-pln.png')}}" alt="Logo PLN" class="h-10 sm:h-11 w-auto object-contain">
        </div>
        
        <div class="w-full max-w-md mx-auto my-auto space-y-6 pt-6">

            <!-- Judul Formulir -->
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Masuk ke Sistem
                </h2>
                <p class="text-sm text-slate-500 mt-1.5 leading-relaxed font-normal">
                    Silakan masukkan <span class="font-medium text-slate-700">NIP</span> atau <span class="font-medium text-slate-700">Email PLN</span> dan kata sandi Anda.
                </p>
            </div>

            <!-- Alert Notifikasi -->
            @if(session('info'))
                <div class="bg-sky-50 border border-sky-200/80 text-sky-800 px-4 py-3 rounded-xl text-sm flex items-center space-x-2.5 shadow-xs">
                    <i class="fa-solid fa-circle-info text-sky-600 text-sm"></i>
                    <span>{{session('info')}}</span>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="bg-rose-50 border border-rose-200/80 text-rose-800 px-4 py-3 rounded-xl text-sm space-y-1 shadow-xs">
                    @if(session('error'))
                        <div class="font-bold flex items-center space-x-1.5">
                            <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                            <span>{{session('error')}}</span>
                        </div>
                    @endif
<?php
foreach(
$errors->all()
as
$error
):
?>
                        <div class="flex items-center space-x-1.5 text-rose-700">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 text-[11px]"></i>
                            <span>{{$error}}</span>
                        </div>
<?php
endforeach;
?>
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{route('login')}}" method="POST" autocomplete="off" class="space-y-5">
                @csrf

               <!-- Input Username / NIP / Email -->
                <div>
                    <label for="login" class="block text-sm font-semibold text-slate-700 mb-1.5 tracking-wide">
                        Username
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-pln-blue transition-colors">
                            <i class="fa-solid fa-id-card text-sm"></i>
                        </div>
                        <input type="text" name="login" id="login" required autocomplete="off"
                               readonly onfocus="this.removeAttribute('readonly');"
                               value="{{old('login')}}"
                               placeholder="Masukkan username..."
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 hover:border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-pln-blue/15 focus:border-pln-blue transition-all duration-200 bg-slate-50/60 hover:bg-white focus:bg-white shadow-xs focus:shadow-sm">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-slate-700 tracking-wide">
                            Kata Sandi
                        </label>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-pln-blue transition-colors">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" name="password" id="password" required autocomplete="new-password"
                               readonly onfocus="this.removeAttribute('readonly');"
                               placeholder="Masukkan kata sandi..."
                               class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-200 hover:border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-pln-blue/15 focus:border-pln-blue transition-all duration-200 bg-slate-50/60 hover:bg-white focus:bg-white shadow-xs focus:shadow-sm">
                        <button type="button" onclick="togglePasswordVisibility()" 
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-pln-blue transition-colors focus:outline-none cursor-pointer">
                            <i class="fa-regular fa-eye text-sm" id="password-eye-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Lacak Tiket -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded text-pln-blue focus:ring-pln-blue/20 border-slate-300 accent-pln-blue transition">
                        <span class="text-sm font-medium text-slate-600">Ingat saya</span>
                    </label>
                    <a href="{{route('complaints.track')}}" class="text-sm font-semibold text-pln-blue hover:text-pln-navy transition-colors flex items-center space-x-1 group">
                        <i class="fa-solid fa-magnifying-glass text-[11px] group-hover:scale-110 transition-transform"></i>
                        <span>Lacak Tiket Cepat</span>
                    </a>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit" 
                            class="group relative w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-pln-navy via-pln-blue to-[#0095e0] hover:from-[#003960] hover:via-[#0075b3] hover:to-[#0087cb] text-white font-bold text-sm shadow-lg shadow-pln-blue/25 hover:shadow-xl hover:shadow-pln-blue/35 hover:-translate-y-0.5 active:translate-y-0 active:shadow-md transition-all duration-200 flex items-center justify-center space-x-2.5 overflow-hidden cursor-pointer">
                        <span class="tracking-wide">MASUK KE SISTEM</span>
                        <i class="fa-solid fa-arrow-right text-sm transition-transform duration-200 group-hover:translate-x-1.5"></i>
                    </button>
                </div>
            </form>

          <!-- Informasi Akses Akun Pegawai -->
            <div class="pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400 leading-relaxed">
                    <i class="fa-solid fa-shield-halved mr-1 text-slate-400"></i>
                    Akses khusus internal. Belum memiliki akun atau lupa sandi? Hubungi <span class="font-semibold text-slate-600">Administrator UPT Gandul</span>.
                </p>
            </div>

        </div>

        <!-- NAMA KAMPUS DI BAGIAN BAWAH TENGAH PAS LOGIN -->
        <div class="w-full text-center pt-8 pb-2 mt-auto">
            <p class="text-xs sm:text-xs font-bold text-slate-400 tracking-widest uppercase">
                UNIVERSITAS BINA SARANA INFORMATIKA
            </p>
        </div>

    </div>

</div>

@push('scripts')
<script>
function togglePasswordVisibility() {
    const input = document.getElementById('password');
    const icon = document.getElementById('password-eye-icon');
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
</script>
@endpush
@endsection