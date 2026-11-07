<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total' => Complaint::where('user_id', $user->id)->count(),
            'baru' => Complaint::where('user_id', $user->id)->where('status', 'baru')->count(),
            'diproses' => Complaint::where('user_id', $user->id)->whereIn('status', ['diverifikasi', 'diproses'])->count(),
            'selesai' => Complaint::where('user_id', $user->id)->where('status', 'selesai')->count(),
            'ditolak' => Complaint::where('user_id', $user->id)->where('status', 'ditolak')->count(),
        ];

        $recentComplaints = Complaint::where('user_id', $user->id)
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('employee.dashboard', compact('user', 'stats', 'recentComplaints'));
    }
}

