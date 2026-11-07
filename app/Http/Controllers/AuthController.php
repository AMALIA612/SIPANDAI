<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('employee.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'NIP atau Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $fieldType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';
        $remember = $request->boolean('remember');

        if (Auth::attempt([$fieldType => $request->input('login'), 'password' => $request->input('password')], $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang di Panel Admin UPT Gandul, ' . Auth::user()->name . '!');
            }

            return redirect()->intended(route('employee.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        throw ValidationException::withMessages([
            'login' => 'NIP/Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ]);
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('employee.dashboard');
        }

        $unitBagianList = [
            'Manajemen UPT Gandul',
            'Bagian Keuangan dan Umum',
            'Bagian Perencanaan dan Evaluasi',
            'Bagian Konstruksi',
            'Seksi K3L dan Keamanan',
            'Seksi Pelaksana Pengadaan',
            'Seksi Lingkungan',
        ];

        return view('auth.register', compact('unitBagianList'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:users,nip'],
            'name' => ['required', 'string', 'max:255'],
            'unit_bagian' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP ini sudah terdaftar di sistem SIPANDAI.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'unit_bagian.required' => 'Unit / Bagian kerja wajib dipilih.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'unit_bagian' => $validated['unit_bagian'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'role' => 'pegawai',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('employee.dashboard')
            ->with('success', 'Akun Pegawai berhasil dibuat! Selamat datang di SIPANDAI.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah keluar dari sistem.');
    }
}

