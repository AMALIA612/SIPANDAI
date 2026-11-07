<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Complaint::with(['category'])->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%");
            });
        }

        $complaints = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('employee.complaints.index', compact('complaints', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('employee.complaints.create', compact('categories', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'incident_date' => ['required', 'date'],
            'incident_location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip', 'max:10240'],
        ], [
            'category_id.required' => 'Kategori pengaduan wajib dipilih.',
            'title.required' => 'Judul pengaduan wajib diisi.',
            'incident_date.required' => 'Tanggal kejadian wajib diisi.',
            'incident_location.required' => 'Lokasi kejadian wajib diisi.',
            'description.required' => 'Deskripsi pengaduan wajib diisi.',
            'evidence.max' => 'Ukuran berkas bukti maksimal 10MB.',
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('complaint-evidences', 'public');
        }

        // Generate Nomor Tiket Otomatis: SIP-2026-00001
        $latestComplaint = Complaint::latest('id')->first();
        $nextId = $latestComplaint ? $latestComplaint->id + 1 : 1;
        $ticketNumber = 'SIP-' . date('Y') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        $complaint = Complaint::create([
            'ticket_number' => $ticketNumber,
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'incident_date' => $validated['incident_date'],
            'incident_location' => $validated['incident_location'],
            'description' => $validated['description'],
            'evidence' => $evidencePath,
            'status' => 'baru',
        ]);

        // Catat ke log riwayat / timeline
        ComplaintHistory::create([
            'complaint_id' => $complaint->id,
            'user_id' => Auth::id(),
            'status' => 'baru',
            'notes' => 'Pengaduan berhasil diajukan dan masuk ke sistem.',
        ]);

        return redirect()->route('employee.complaints.index')
            ->with('success', 'Pengaduan dengan nomor tiket ' . $ticketNumber . ' berhasil dikirim.');
    }

    public function show($id)
    {
        $complaint = Complaint::with(['category', 'histories.user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('employee.complaints.show', compact('complaint'));
    }

    // ==================== HALAMAN LACAK TIKET ====================
    public function track(Request $request)
    {
        $ticketQuery = $request->get('ticket', '');
        $complaint = null;

        if (!empty($ticketQuery)) {
            $complaint = Complaint::with(['category', 'histories.user', 'user'])
                ->where('ticket_number', $ticketQuery)
                ->first();
        }

        return view('employee.track', compact('complaint', 'ticketQuery'));
    }

    // ==================== HALAMAN PROFIL PEGAWAI ====================
    public function profile()
    {
        $user = Auth::user();
        return view('employee.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
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
                'regex:/[A-Z]/', 
                'regex:/[0-9]/', 
                'regex:/[^A-Za-z0-9]/'
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'current_password.required_with' => 'Kata sandi saat ini wajib diisi jika ingin mengubah kata sandi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password.regex' => 'Kata sandi baru harus mengandung minimal 1 huruf kapital, 1 angka, dan 1 simbol.',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('employee.profile')
            ->with('success', 'Profil Anda berhasil diperbarui!');
    }
}