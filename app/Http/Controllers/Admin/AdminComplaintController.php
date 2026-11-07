<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminComplaintController extends Controller
{
  public function index(Request $request)
    {
        $query = Complaint::with(['user', 'category']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // DIPERBAIKI: Abaikan jika nilainya 'all'
        if ($request->filled('month') && $request->month !== 'all') {
            $query->whereMonth('created_at', $request->month);
        }

        // DIPERBAIKI: Abaikan jika nilainya 'all'
        if ($request->filled('year') && $request->year !== 'all') {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('nip', 'LIKE', "%{$search}%");
                    });
            });
        }

        $complaints = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.complaints.index', compact('complaints', 'categories'));
    }

    public function show($id)
    {
        $complaint = Complaint::with(['user', 'category', 'histories.user'])->findOrFail($id);
        $categories = Category::where('is_active', true)->get();

        return view('admin.complaints.show', compact('complaint', 'categories'));
    }

    public function updateStatus(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:baru,diverifikasi,diproses,selesai,ditolak'],
            'admin_notes' => ['nullable', 'string'],
            'resolution_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip', 'max:10240'],
        ], [
            'status.required' => 'Pilih status baru.',
            'resolution_proof.max' => 'Ukuran berkas bukti penyelesaian maksimal 10MB.',
        ]);

        $proofPath = $complaint->resolution_proof;
        if ($request->hasFile('resolution_proof')) {
            $proofPath = $request->file('resolution_proof')->store('resolution-proofs', 'public');
        }

        $complaint->status = $validated['status'];
        if (!empty($validated['admin_notes'])) {
            $complaint->admin_notes = $validated['admin_notes'];
        }
        $complaint->resolution_proof = $proofPath;

        if ($validated['status'] === 'selesai' && !$complaint->resolved_at) {
            $complaint->resolved_at = now();
        }

        $complaint->save();

        // Tambahkan ke log riwayat / timeline
        $statusLabels = [
            'baru' => 'Pengaduan Baru',
            'diverifikasi' => 'Pengaduan Diverifikasi',
            'diproses' => 'Pengaduan Sedang Ditindaklanjuti',
            'selesai' => 'Pengaduan Selesai',
            'ditolak' => 'Pengaduan Ditolak',
        ];

        ComplaintHistory::create([
            'complaint_id' => $complaint->id,
            'user_id' => Auth::id(),
            'status' => $validated['status'],
            'notes' => $validated['admin_notes'] ?: ('Status diubah menjadi: ' . ($statusLabels[$validated['status']] ?? $validated['status'])),
            'created_at' => now(),
        ]);

        return redirect()->route('admin.complaints.show', $complaint->id)
            ->with('success', 'Status pengaduan ' . $complaint->ticket_number . ' berhasil diperbarui.');
    }
}