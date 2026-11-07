<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');

        // Jika halaman dibuka/di-refresh biasa (bukan klik AJAX), paksa selalu kembali ke 'all'
        $isAjax = $request->ajax() || $request->wantsJson();
        $activeFilter = $isAjax ? $request->get('filter', 'all') : 'all';

        if (!in_array($activeFilter, ['all', 'baru', 'diverifikasi', 'diproses', 'selesai', 'ditolak'])) {
            $activeFilter = 'all';
        }

        // 1. Statistik Utama
        $stats = [
            'total'         => Complaint::count(),
            'baru'          => Complaint::where('status', 'baru')->count(),
            'diverifikasi'  => Complaint::where('status', 'diverifikasi')->count(),
            'diproses'      => Complaint::where('status', 'diproses')->count(),
            'selesai'       => Complaint::where('status', 'selesai')->count(),
            'ditolak'       => Complaint::where('status', 'ditolak')->count(),
            'total_pegawai' => User::where('role', 'pegawai')->count(),
        ];

        // 2. Query dasar untuk Grafik berdasarkan filter
        $complaintQuery = Complaint::query();
        if ($activeFilter !== 'all') {
            $complaintQuery->where('status', $activeFilter);
        }

        // 3. Data Grafik Status (Tetap kirim proporsi asli agar Donut Chart bisa menerapkan efek Spotlight & Pop-out)
        $statusCounts = [
            'baru'         => $stats['baru'],
            'diverifikasi' => $stats['diverifikasi'],
            'diproses'     => $stats['diproses'],
            'selesai'      => $stats['selesai'],
            'ditolak'      => $stats['ditolak'],
        ];

        // 4. Data Bulanan (Bar Chart)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyCounts = array_fill(1, 12, 0);

        $monthlyQuery = (clone $complaintQuery)->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', $currentYear)
        ->groupBy('month')
        ->pluck('count', 'month');

        foreach ($monthlyQuery as $monthNum => $count) {
            $monthlyCounts[$monthNum] = (int) $count;
        }

        // 5. Statistik Kategori diurutkan dari yang terbanyak ke paling sedikit
        $categoryStats = Category::where('is_active', true)
            ->withCount(['complaints' => function($q) use ($activeFilter) {
                if ($activeFilter !== 'all') {
                    $q->where('status', $activeFilter);
                }
            }])
            ->get()
            ->sortByDesc('complaints_count')
            ->values();

        $categoryChartLabels = $categoryStats->pluck('name')->toArray();
        $categoryChartCounts = $categoryStats->pluck('complaints_count')->toArray();
        $totalFilteredComplaints = ($activeFilter === 'all') ? $stats['total'] : ($stats[$activeFilter] ?? 0);

        $topCat = $categoryStats->first();
        $topCategoryData = ($topCat && $topCat->complaints_count > 0) ? [
            'name'  => $topCat->name,
            'count' => $topCat->complaints_count,
            'pct'   => $totalFilteredComplaints > 0 ? round(($topCat->complaints_count / $totalFilteredComplaints) * 100) : 0
        ] : null;

        // Jika request dari klik kartu (AJAX), kirim JSON
        if ($isAjax) {
            return response()->json([
                'statusCounts'    => array_values($statusCounts),
                'monthlyCounts'   => array_values($monthlyCounts),
                'categoryLabels'  => $categoryChartLabels,
                'categoryCounts'  => $categoryChartCounts,
                'totalFiltered'   => $totalFilteredComplaints,
                'totalAll'        => $stats['total'],
                'avgMonthly'      => round($totalFilteredComplaints / 12, 1),
                'topCategory'     => $topCategoryData,
                'activeFilter'    => $activeFilter
            ]);
        }

        return view('admin.dashboard', compact(
            'stats', 
            'statusCounts',
            'months', 
            'monthlyCounts', 
            'categoryStats',
            'categoryChartLabels',
            'categoryChartCounts',
            'activeFilter'
        ));
    }
}