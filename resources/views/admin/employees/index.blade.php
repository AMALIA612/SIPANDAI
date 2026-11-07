@extends('layouts.app')

@section('title','Kelola Data Pegawai - Admin UPT Gandul')

@section('content')
<div class="w-full space-y-6">

    <!-- Header & Tombol Tambah -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Kelola Data Pegawai</h1>
            <p class="text-sm text-slate-500">Daftar akun pegawai PLN UPT Gandul yang terdaftar dalam SIPANDAI</p>
        </div>
        <button type="button" onclick="openCreateEmployeeModal()" 
                class="inline-flex items-center px-4 py-2.5 rounded-xl bg-pln-blue hover:bg-pln-hover text-white font-bold text-sm shadow transition cursor-pointer">
            <i class="fa-solid fa-user-plus mr-1.5"></i> Tambah Pegawai Baru
        </button>
    </div>

    <!-- Pencarian -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{route('admin.employees.index')}}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{request('search')}}" 
                   placeholder="Cari NIP, nama pegawai, atau unit..."
                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-900 transition cursor-pointer">
                Cari
            </button>
            @if(request('search'))
                <a href="{{route('admin.employees.index')}}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm flex items-center hover:bg-slate-200">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Pegawai -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-5 whitespace-nowrap w-32">NIP</th>
                        <th class="py-3.5 px-5">Nama Lengkap</th>
                        <th class="py-3.5 px-5">Unit / Bagian</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Kontak (HP & Email)</th>
                        <th class="py-3.5 px-5 text-center whitespace-nowrap w-44">Pengaduan Diajukan</th>
                        <th class="py-3.5 px-5 text-center whitespace-nowrap w-40">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if($employees->count()>0)
<?php
foreach(
$employees
as
$emp
):
?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-5 font-mono font-bold text-pln-navy whitespace-nowrap">
                                    {{$emp->nip?:'-'}}
                                </td>
                                <td class="py-3.5 px-5 font-bold text-slate-800 whitespace-nowrap">
                                    {{$emp->name}}
                                </td>
                                <td class="py-3.5 px-5 text-sm text-slate-600">
                                    {{$emp->unit_bagian?:'-'}}
                                </td>
                                <td class="py-3.5 px-5 text-sm whitespace-nowrap">
                                    <div class="text-slate-800 font-medium">{{$emp->phone?:'-'}}</div>
                                    <div class="text-slate-400 text-[11px]">{{$emp->email}}</div>
                                </td>
                                <td class="py-3.5 px-5 text-center whitespace-nowrap w-44">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-pln-light text-pln-navy">
                                        {{$emp->complaints_count}} laporan
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-center whitespace-nowrap w-40">
                                    <div class="inline-flex items-center justify-center">
                                        <button type="button" 
                                                onclick="openResetPasswordModal({{$emp->id}}, '{{addslashes($emp->name)}}', '{{$emp->nip}}')"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 transition border border-amber-200 cursor-pointer">
                                            <i class="fa-solid fa-key mr-1.5"></i> Reset Sandi
                                        </button>
                                    </div>
                                </td>
                            </tr>
<?php
endforeach;
?>
                    @else
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-sm">Belum ada pegawai terdaftar.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{$employees->links()}}
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Pegawai -->
<div id="modalCreateEmployee" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-slate-800">Tambah Akun Pegawai Baru</h3>
            <button type="button" onclick="closeCreateEmployeeModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">&times;</button>
        </div>
        <form action="{{route('admin.employees.store')}}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 uppercase mb-1">NIP <span class="text-rose-500">*</span></label>
                    <input type="text" name="nip" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 uppercase mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 uppercase mb-1">Unit / Bagian <span class="text-rose-500">*</span></label>
                    <select name="unit_bagian" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none bg-white">
<?php
foreach(
$units
as
$u
):
?>
                            <option value="{{$u}}">{{$u}}</option>
<?php
endforeach;
?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 uppercase mb-1">No HP <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-slate-700 uppercase mb-1">Email PLN <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                </div>
                <div class="col-span-2">
                    <label for="create_password_input" class="block text-sm font-bold text-slate-700 uppercase mb-1">Kata Sandi Awal <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="create_password_input" required minlength="6" value="password" 
                               class="w-full pl-3.5 pr-10 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                        <button type="button" onclick="togglePasswordVisibility('create_password_input', 'create_password_icon')" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i id="create_password_icon" class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                    <span class="text-[11px] text-slate-400 block mt-1">Default sandi awal: <code>password</code></span>
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeCreateEmployeeModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-pln-blue text-white rounded-xl text-sm font-bold shadow hover:bg-pln-hover cursor-pointer">Simpan Pegawai</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Sandi (Dilengkapi Ikon Mata) -->
<div id="modalResetPassword" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-200">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-base font-bold text-slate-800">Reset Kata Sandi</h3>
            <button type="button" onclick="closeResetPasswordModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">&times;</button>
        </div>
        <p class="text-sm text-slate-500 mb-4" id="reset_employee_info"></p>
        <form id="formResetPassword" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="new_password_input" class="block text-sm font-bold text-slate-700 uppercase mb-1">Kata Sandi Baru <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="password" name="new_password" id="new_password_input" required minlength="6" value="password"
                           class="w-full pl-3.5 pr-10 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pln-blue focus:outline-none">
                    <button type="button" onclick="togglePasswordVisibility('new_password_input', 'reset_password_icon')" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i id="reset_password_icon" class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 text-white rounded-xl text-sm font-bold shadow hover:bg-amber-700 cursor-pointer">Perbarui Sandi</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function togglePasswordVisibility(inputId, iconId) {
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

function openCreateEmployeeModal() {
    document.getElementById('modalCreateEmployee').classList.remove('hidden');
}
function closeCreateEmployeeModal() {
    document.getElementById('modalCreateEmployee').classList.add('hidden');
}
function openResetPasswordModal(id, name, nip) {
    document.getElementById('formResetPassword').action = '{{url("admin/pegawai")}}/' + id + '/reset-password';
    document.getElementById('reset_employee_info').innerHTML = `Atur kata sandi baru untuk pegawai <strong>${name}</strong> (NIP: ${nip}).`;
    
    // Pastikan kembali tertutup (mode password) setiap kali modal baru dibuka
    const input = document.getElementById('new_password_input');
    const icon = document.getElementById('reset_password_icon');
    input.type = 'password';
    input.value = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');

    document.getElementById('modalResetPassword').classList.remove('hidden');
}
function closeResetPasswordModal() {
    document.getElementById('modalResetPassword').classList.add('hidden');
}
</script>
@endpush
@endsection