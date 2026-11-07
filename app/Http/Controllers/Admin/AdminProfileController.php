<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    // TAMBAHKAN FUNGSI INI AGAR HALAMAN PROFIL BISA DIBUKA
    public function index()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', function ($attribute, $value, $fail) use ($user) {
                if (!empty($value) && !Hash::check($value, $user->password)) {
                    $fail('Kata sandi saat ini yang Anda masukkan salah.');
                }
            }],
            'password' => [
                'nullable', 
                'string', 
                'min:6', 
                'confirmed', 
                'regex:/[A-Z]/',      // Harus ada huruf kapital
                'regex:/[0-9]/',      // Harus ada angka
                'regex:/[^A-Za-z0-9]/' // Harus ada simbol
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'current_password.required_with' => 'Kata sandi saat ini wajib diisi jika ingin mengubah kata sandi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password.regex' => 'Kata sandi baru harus mengandung minimal 1 huruf kapital, 1 angka, dan 1 simbol.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.profile')
            ->with('success', 'Profil dan kata sandi admin berhasil diperbarui!');
    }
}