<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'pegawai')->withCount('complaints');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nip', 'LIKE', "%{$search}%")
                    ->orWhere('unit_bagian', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->orderByRaw('LOWER(name) ASC')->paginate(10)->withQueryString();
        $units = [
            'Manajemen UPT Gandul',
            'Bagian Keuangan dan Umum',
            'Bagian Perencanaan dan Evaluasi',
            'Bagian Konstruksi',
            'Seksi K3L dan Keamanan',
            'Seksi Pelaksana Pengadaan',
            'Seksi Lingkungan',
        ];

        return view('admin.employees.index', compact('employees', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:users,nip'],
            'name' => ['required', 'string', 'max:255'],
            'unit_bagian' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'unit_bagian' => $validated['unit_bagian'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'role' => 'pegawai',
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Akun pegawai berhasil ditambahkan.');
    }

    public function resetPassword(Request $request, $id)
    {
        $employee = User::where('role', 'pegawai')->findOrFail($id);

        $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        $employee->password = Hash::make($request->new_password);
        $employee->save();

        return back()->with('success', "Kata sandi untuk {$employee->name} ({$employee->nip}) berhasil direset.");
    }
}

