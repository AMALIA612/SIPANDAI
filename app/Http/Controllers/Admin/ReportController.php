<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $monthInput = $request->get('month', 'all');
        $selectedMonth = ($monthInput === '' || $monthInput === null || $monthInput === 'all') ? null : (int)$monthInput;

        $yearInput = $request->get('year', 'all');
        $selectedYear = ($yearInput === '' || $yearInput === null || $yearInput === 'all') ? 'all' : (int)$yearInput;

        $selectedStatus = $request->get('status', 'all');
        $selectedCategory = $request->get('category_id', 'all');

        $query = Complaint::with(['user', 'category']);

        if ($selectedYear !== 'all') {
            $query->whereYear('created_at', $selectedYear);
        }

        if ($selectedMonth) {
            $query->whereMonth('created_at', $selectedMonth);
        }

        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('category_id', $selectedCategory);
        }

        $complaints = $query->latest()->get();

        $statusSummary = [
            'total' => $complaints->count(),
            'baru' => $complaints->where('status', 'baru')->count(),
            'diverifikasi' => $complaints->where('status', 'diverifikasi')->count(),
            'diproses' => $complaints->where('status', 'diproses')->count(),
            'selesai' => $complaints->where('status', 'selesai')->count(),
            'ditolak' => $complaints->where('status', 'ditolak')->count(),
        ];

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.reports.index', compact(
            'complaints',
            'statusSummary',
            'categories',
            'selectedMonth',
            'selectedYear',
            'selectedStatus',
            'selectedCategory'
        ));
    }

    public function exportCsv(Request $request)
    {
        $monthInput = $request->get('month', 'all');
        $selectedMonth = ($monthInput === '' || $monthInput === null || $monthInput === 'all') ? null : (int)$monthInput;

        $yearInput = $request->get('year', 'all');
        $selectedYear = ($yearInput === '' || $yearInput === null || $yearInput === 'all') ? 'all' : (int)$yearInput;

        $selectedStatus = $request->get('status', 'all');
        $selectedCategory = $request->get('category_id', 'all');

        $query = Complaint::with(['user', 'category']);

        if ($selectedYear !== 'all') {
            $query->whereYear('created_at', $selectedYear);
        }

        if ($selectedMonth) {
            $query->whereMonth('created_at', $selectedMonth);
        }

        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('category_id', $selectedCategory);
        }

        $complaints = $query->latest()->get();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $periodeBulanStr = ($selectedMonth === null) ? 'Seluruh Bulan' : ($monthNames[$selectedMonth] ?? 'Semua');
        $periodeTahunStr = ($selectedYear === 'all') ? 'Semua Tahun' : $selectedYear;
        $periodeStr = "{$periodeBulanStr} {$periodeTahunStr}";
        
        $fileName = "Laporan_SIPANDAI_UPT_Gandul_{$periodeBulanStr}_{$periodeTahunStr}.xls";

        $response = new StreamedResponse(function () use ($complaints, $periodeStr) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Rekapitulasi SIPANDAI</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>';
            echo 'body { font-family: Calibri, Arial, sans-serif; }';
            echo '.title { font-size: 13pt; font-weight: bold; color: #003865; }';
            echo '.subtitle { font-size: 11pt; font-weight: bold; color: #1e293b; }';
            echo '.meta { font-size: 9.5pt; color: #475569; }';
            echo 'table { border-collapse: collapse; width: 100%; }';
            echo 'th { background-color: #0084c7; color: #ffffff; font-weight: bold; font-size: 10pt; text-align: center; border: 1px solid #005f93; padding: 7px 6px; }';
            echo 'td { border: 1px solid #cbd5e1; padding: 5px 7px; font-size: 9.5pt; vertical-align: top; color: #0f172a; }';
            echo '.text-center { text-align: center; }';
            echo '.text-bold { font-weight: bold; }';
            echo '.bg-zebra { background-color: #f8fafc; }';
            echo '.mso-text { mso-number-format:"\@"; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            
            echo '<table>';
            echo '<tr><td colspan="15" class="subtitle" style="border:none;">LAPORAN REKAPITULASI PENGADUAN INTERNAL PEGAWAI (SIPANDAI)</td></tr>';
            echo '<tr><td colspan="15" class="meta" style="border:none;">Periode: ' . $periodeStr . ' &bull; Diunduh pada: ' . date('d/m/Y H:i') . ' WIB &bull; Total: ' . $complaints->count() . ' Laporan</td></tr>';
            echo '<tr><td colspan="15" style="border:none; height: 12px;"></td></tr>';
            
            echo '<tr>';
            echo '<th style="width: 45px;">No.</th>';
            echo '<th style="width: 140px;">No. Tiket</th>';
            echo '<th style="width: 130px;">Tgl Masuk</th>';
            echo '<th style="width: 130px;">NIP Pelapor</th>';
            echo '<th style="width: 180px;">Nama Pelapor</th>';
            echo '<th style="width: 200px;">Unit / Bagian</th>';
            echo '<th style="width: 130px;">Nomor HP</th>';
            echo '<th style="width: 140px;">Kategori</th>';
            echo '<th style="width: 230px;">Judul Pengaduan</th>';
            echo '<th style="width: 160px;">Lokasi Kejadian</th>';
            echo '<th style="width: 120px;">Tgl Kejadian</th>';
            echo '<th style="width: 280px;">Deskripsi Pengaduan</th>';
            echo '<th style="width: 130px;">Status</th>';
            echo '<th style="width: 250px;">Catatan Admin</th>';
            echo '<th style="width: 160px;">Tgl Selesai</th>';
            echo '</tr>';

            $no = 1;
            foreach ($complaints as $c) {
                $bgClass = ($no % 2 === 0) ? ' class="bg-zebra"' : '';

                $tglSelesai = '-';
                $tglSelesaiStyle = 'mso-number-format:\'\@\'; text-align: center;';
                if ($c->status === 'selesai') {
                    $resDate = $c->resolved_at ?? $c->updated_at;
                    $tglSelesai = $resDate ? $resDate->format('d/m/Y H:i') : '-';
                    $tglSelesaiStyle .= ' font-weight: bold; color: #059669;';
                } elseif ($c->status === 'ditolak') {
                    $tglSelesai = 'Ditolak';
                    $tglSelesaiStyle .= ' color: #e11d48;';
                } else {
                    $tglSelesai = 'Belum Selesai (' . $c->status_label . ')';
                    $tglSelesaiStyle .= ' color: #64748b; font-style: italic;';
                }

                echo '<tr' . $bgClass . '>';
                echo '<td align="center">' . $no++ . '</td>';
                echo '<td align="left" style="mso-number-format:\'\@\'; font-weight: bold; color:#004777;">' . htmlspecialchars($c->ticket_number) . '</td>';
                echo '<td align="center" style="mso-number-format:\'\@\';">' . ($c->created_at ? $c->created_at->format('d/m/Y H:i') : '-') . '</td>';
                echo '<td align="center" style="mso-number-format:\'\@\';">' . htmlspecialchars($c->user?->nip ?? '-') . '</td>';
                echo '<td align="left" style="font-weight: bold;">' . htmlspecialchars($c->user?->name ?? '-') . '</td>';
                echo '<td align="left">' . htmlspecialchars($c->user?->unit_bagian ?? '-') . '</td>';
                echo '<td align="center" style="mso-number-format:\'\@\';">' . htmlspecialchars($c->user?->phone ?? '-') . '</td>';
                echo '<td align="center">' . htmlspecialchars($c->category_label) . '</td>';
                echo '<td align="left">' . htmlspecialchars($c->title) . '</td>';
                echo '<td align="left">' . htmlspecialchars($c->incident_location ?? '-') . '</td>';
                echo '<td align="center" style="mso-number-format:\'\@\';">' . ($c->incident_date ? $c->incident_date->format('d/m/Y') : '-') . '</td>';
                echo '<td align="left">' . htmlspecialchars($c->description) . '</td>';
                echo '<td align="center" style="font-weight: bold;">' . htmlspecialchars(strtoupper($c->status_label)) . '</td>';
                echo '<td align="left">' . htmlspecialchars($c->admin_notes ?: '-') . '</td>';
                echo '<td align="center" style="' . $tglSelesaiStyle . '">' . htmlspecialchars($tglSelesai) . '</td>';
                echo '</tr>';
            }

            echo '</table>';
            echo '</body>';
            echo '</html>';
        });

        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}