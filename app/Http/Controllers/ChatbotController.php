<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle incoming chatbot message (Hybrid: Gemini AI with Smart Local Fallback).
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $user = Auth::user();
        $message = trim($request->input('message'));
        $history = $request->input('history', []);

        // 1. Cek apakah ada API Key Gemini di .env
        $geminiApiKey = config('services.gemini.key', env('GEMINI_API_KEY'));

        if (!empty($geminiApiKey)) {
            try {
                $geminiReply = $this->callGeminiApi($message, $user, $history, $geminiApiKey);
                if ($geminiReply) {
                    return response()->json([
                        'status' => 'success',
                        'reply' => $geminiReply,
                        'source' => 'gemini_ai',
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Gemini API call failed, falling back to local engine: ' . $e->getMessage());
            }
        }

        // 2. Fallback cerdas lokal (Smart Local Rule Engine)
        $localReply = $this->generateLocalResponse($message, $user);

        return response()->json([
            'status' => 'success',
            'reply' => $localReply,
            'source' => 'local_smart_engine',
        ]);
    }

    /**
     * Memanggil Google Gemini API (Model: gemini-1.5-flash)
     */
    protected function callGeminiApi(string $message, $user, array $history, string $apiKey): ?string
    {
        // Siapkan ringkasan data tiket user yang relevan
        $userTicketsSummary = $this->getUserComplaintsContext($user);
        $categoriesList = Category::where('is_active', true)->pluck('name')->implode(', ');

        $systemInstruction = "Anda adalah 'Si Pandai AI', asisten virtual cerdas dan ramah untuk sistem SIPANDAI (Sistem Pengaduan Antar Dan Internal) di PT PLN (Persero) UPT Gandul. "
            . "Pengguna saat ini adalah: {$user->name} (Role: {$user->role}, Unit/Bagian: " . ($user->unit_bagian ?? 'Umum') . "). "
            . "Kategori pengaduan fasilitas kantor yang tersedia: [{$categoriesList}]. "
            . "Konteks data tiket pengguna saat ini:\n{$userTicketsSummary}\n\n"
            . "Pedoman interaksi:\n"
            . "1. Gunakan bahasa Indonesia yang ramah, sopan, profesional, dan empatik khas PLN.\n"
            . "2. Jika ditanyakan status tiket, gunakan informasi tiket di atas secara akurat.\n"
            . "3. Jika ada keluhan fasilitas (AC, lampu, toilet, pintu, stop kontak, dll), berikan saran kategori yang cocok dan arahkan untuk membuat aduan lewat menu 'Buat Pengaduan'.\n"
            . "4. Jelaskan tahapan status jika ditanyakan: Baru (menunggu verifikasi), Diverifikasi (disetujui admin), Diproses (perbaikan teknis oleh tim), Selesai (perbaikan tuntas dengan bukti foto), Ditolak (laporan tidak valid).\n"
            . "5. Format jawaban dengan rapi menggunakan poin-poin atau baris baru agar mudah dibaca.\n"
            . "6. Jawab dengan ringkas, padat, dan langsung to the point tanpa bertele-tele.";

        // Format riwayat chat untuk Gemini
        $contents = [];
        
        // Batasi history maksimal 4 percakapan terakhir agar hemat token dan relevan
        $recentHistory = array_slice($history, -4);
        foreach ($recentHistory as $item) {
            if (isset($item['role']) && isset($item['content'])) {
                $role = $item['role'] === 'user' ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => (string)$item['content']]]
                ];
            }
        }

        // Tambahkan prompt saat ini
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        $apiKey = trim($apiKey);
        $candidateModels = ['gemini-3-flash-preview', 'gemini-2.5-flash-lite'];

        foreach ($candidateModels as $model) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $response = Http::withoutVerifying()->timeout(15)->post($endpoint, [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($text) {
                    return trim($text);
                }
            } else {
                Log::info("Gemini call with {$model} failed (HTTP {$response->status()}), trying next model if available.");
            }
        }

        return null;
    }

    /**
     * Mesin Penjawab Pintar Lokal (Tanpa Internet / Tanpa Biaya)
     */
    protected function generateLocalResponse(string $message, $user): string
    {
        $msgLower = strtolower($message);

        // 1. Cek apakah ada penyebutan nomor tiket spesifik (contoh: SIP-2026-000001)
        if (preg_match('/SIP-\d{4}-\d{6}/i', $message, $matches)) {
            $ticketNumber = strtoupper($matches[0]);
            $complaint = Complaint::with(['category', 'histories'])->where('ticket_number', $ticketNumber)->first();

            if ($complaint) {
                // Pastikan otorisasi: admin bisa lihat semua, pegawai hanya miliknya
                if ($user->isAdmin() || $complaint->user_id === $user->id) {
                    $statusLabel = match($complaint->status) {
                        'baru' => 'Baru (Menunggu Verifikasi Admin)',
                        'diverifikasi' => 'Diverifikasi (Disetujui & Siap Ditindaklanjuti)',
                        'proses' => 'Diproses (Sedang Dikerjakan Teknisi Sarpras)',
                        'selesai' => 'Selesai (Perbaikan Rampung)',
                        'ditolak' => 'Ditolak',
                        default => ucfirst($complaint->status)
                    };

                    $tgl = $complaint->created_at ? $complaint->created_at->format('d M Y, H:i') : '-';
                    $catName = $complaint->category ? $complaint->category->name : ($complaint->custom_category ?? 'Umum');

                    $reply = "📌 **Detail Tiket {$complaint->ticket_number}**\n";
                    $reply .= "• **Judul:** {$complaint->title}\n";
                    $reply .= "• **Kategori:** {$catName}\n";
                    $reply .= "• **Lokasi:** {$complaint->incident_location}\n";
                    $reply .= "• **Tanggal Lapor:** {$tgl}\n";
                    $reply .= "• **Status Terkini:** **{$statusLabel}**\n";

                    if ($complaint->status === 'ditolak' && !empty($complaint->admin_notes)) {
                        $reply .= "• **Alasan Ditolak:** {$complaint->admin_notes}\n";
                    } elseif ($complaint->status === 'selesai') {
                        $reply .= "• **Catatan Penyelesaian:** " . ($complaint->admin_notes ?? 'Perbaikan telah selesai dilaksanakan.') . "\n";
                        if ($complaint->resolution_proof) {
                            $reply .= "• **Bukti Foto:** Sudah diunggah oleh admin pada detail tiket.\n";
                        }
                    }

                    $detailUrl = $user->isAdmin() ? route('admin.complaints.show', $complaint->id) : route('employee.complaints.show', $complaint->id);
                    $reply .= "\n👉 Anda dapat melihat rincian lengkapnya di: [Buka Detail Tiket]({$detailUrl})";
                    return $reply;
                } else {
                    return "Maaf, nomor tiket **{$ticketNumber}** bukan milik akun Anda sehingga Anda tidak memiliki akses untuk melihat detailnya.";
                }
            } else {
                return "Maaf, tiket dengan nomor **{$ticketNumber}** tidak ditemukan di sistem SIPANDAI. Pastikan format nomor tiket sudah benar ya.";
            }
        }

        // 2. Cek apakah user menanyakan "tiket saya" / "status pengaduan"
        if (str_contains($msgLower, 'tiket saya') || str_contains($msgLower, 'status saya') || str_contains($msgLower, 'aduan saya') || str_contains($msgLower, 'laporan saya')) {
            $myComplaints = Complaint::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();

            if ($myComplaints->isEmpty()) {
                $createUrl = route('employee.complaints.create');
                return "Halo {$user->name}, saat ini Anda belum memiliki tiket pengaduan aktif. Jika ada kendala fasilitas kantor PLN yang ingin dilaporkan, silakan buat pengaduan baru melalui menu [Buat Pengaduan]({$createUrl}).";
            }

            $reply = "Halo {$user->name}, berikut adalah 3 pengaduan terbaru milik Anda:\n\n";
            foreach ($myComplaints as $idx => $c) {
                $num = $idx + 1;
                $statusBadge = match($c->status) {
                    'baru' => '🟡 Baru',
                    'diverifikasi' => '🟣 Diverifikasi',
                    'proses' => '🟠 Diproses',
                    'selesai' => '🟢 Selesai',
                    'ditolak' => '🔴 Ditolak',
                    default => ucfirst($c->status)
                };
                $reply .= "{$num}. **{$c->ticket_number}** — {$c->title}\n";
                $reply .= "   Status: **{$statusBadge}** | Lokasi: {$c->incident_location}\n";
            }
            $indexUrl = route('employee.complaints.index');
            $reply .= "\n👉 Selengkapnya bisa Anda pantau di menu [Daftar Pengaduan Saya]({$indexUrl}).";
            return $reply;
        }

        // 3. Sapaan Ramah
        if (preg_match('/\b(halo|hai|hi|pagi|siang|sore|malam|assalamualaikum|assalamu\'alaikum)\b/i', $msgLower)) {
            return "Halo {$user->name}! 👋\nSaya **Si Pandai AI**, asisten virtual sarana & prasarana PLN UPT Gandul.\n\nAda yang bisa saya bantu hari ini?\n• *Cek status pengaduan* (contoh: ketik nomor tiket atau 'tiket saya')\n• *Cara melaporkan kendala fasilitas* (AC, listrik, sanitasi, dll)\n• *Informasi SOP perbaikan sarpras*";
        }

        // 4. Kategori: AC & Pendingin Ruangan
        if (str_contains($msgLower, 'ac') || str_contains($msgLower, 'panas') || str_contains($msgLower, 'bocor') || str_contains($msgLower, 'freon') || str_contains($msgLower, 'remote ac')) {
            $createUrl = route('employee.complaints.create');
            return "Untuk kendala seperti **AC bocor, tidak dingin, atau remote AC rusak**, Anda dapat membuat pengaduan dengan memilih Kategori: **AC & Pendingin Ruangan**.\n\n"
                . "📌 **Tips Pengisian Form:**\n"
                . "1. Tuliskan lokasi ruangan secara spesifik (misal: *Lantai 2 - Ruang Rapat Utama*).\n"
                . "2. Lampirkan foto kondisi AC agar tim teknisi sarpras dapat menyiapkan peralatan yang sesuai.\n\n"
                . "👉 [Klik di sini untuk Membuat Aduan AC]({$createUrl})";
        }

        // 5. Kategori: Kelistrikan & Penerangan
        if (str_contains($msgLower, 'listrik') || str_contains($msgLower, 'lampu') || str_contains($msgLower, 'saklar') || str_contains($msgLower, 'stop kontak') || str_contains($msgLower, 'konslet') || str_contains($msgLower, 'mati lampu')) {
            $createUrl = route('employee.complaints.create');
            return "Untuk kendala **lampu padam, stop kontak longgar, atau saklar rusak**, silakan laporkan dengan Kategori: **Kelistrikan & Penerangan**.\n\n"
                . "⚠️ *Utamakan keselamatan: Jangan memegang instalasi kabel terbuka jika terlihat ada percikan listrik.*\n\n"
                . "👉 [Klik di sini untuk Membuat Aduan Kelistrikan]({$createUrl})";
        }

        // 6. Kategori: Sanitasi & Kebersihan
        if (str_contains($msgLower, 'toilet') || str_contains($msgLower, 'wc') || str_contains($msgLower, 'wastafel') || str_contains($msgLower, 'kran') || str_contains($msgLower, 'air') || str_contains($msgLower, 'kebersihan')) {
            $createUrl = route('employee.complaints.create');
            return "Untuk kendala **toilet mampet, kran bocor, wastafel tersumbat, atau ketersediaan air**, pilih Kategori: **Sanitasi & Kebersihan**.\n\n"
                . "Tim kebersihan dan sarpras akan segera memeriksa ke lokasi yang Anda sebutkan.\n\n"
                . "👉 [Klik di sini untuk Melaporkan]({$createUrl})";
        }

        // 7. Kategori: Meja / Kursi / Pintu / Bangunan
        if (str_contains($msgLower, 'meja') || str_contains($msgLower, 'kursi') || str_contains($msgLower, 'pintu') || str_contains($msgLower, 'jendela') || str_contains($msgLower, 'plafon') || str_contains($msgLower, 'kunci')) {
            $createUrl = route('employee.complaints.create');
            return "Untuk kerusakan furnitur atau bagian bangunan seperti **meja, kursi kerja, gagang pintu, jendela, atau plafon**, silakan masukkan ke Kategori: **Sarana Fisik & Bangunan** (atau pilih *Lainnya* jika tidak tersedia).\n\n"
                . "👉 [Buka Formulir Pengaduan]({$createUrl})";
        }

        // 8. SOP & Alur Penanganan
        if (str_contains($msgLower, 'cara lapor') || str_contains($msgLower, 'alur') || str_contains($msgLower, 'sop') || str_contains($msgLower, 'prosedur') || str_contains($msgLower, 'berapa lama')) {
            return "📋 **Alur Penanganan Pengaduan di SIPANDAI PLN UPT Gandul:**\n\n"
                . "1. **Pengajuan (Status: Baru)**\n"
                . "   Pegawai mengisi form aduan. Sistem otomatis menerbitkan nomor tiket `SIP-YYYY-XXXXXX`.\n\n"
                . "2. **Verifikasi Admin (Status: Diverifikasi)**\n"
                . "   Admin memeriksa urgensi dan kevalidan laporan.\n\n"
                . "3. **Penanganan Teknis (Status: Diproses)**\n"
                . "   Tim teknisi sarpras diterjunkan ke lokasi untuk perbaikan.\n\n"
                . "4. **Penyelesaian (Status: Selesai)**\n"
                . "   Admin mengunggah foto bukti hasil perbaikan dan menutup tiket secara resmi.";
        }

        // 9. Ucapan Terima Kasih
        if (preg_match('/\b(makasih|terima kasih|thanks|thank you|matur nuwun)\b/i', $msgLower)) {
            return "Sama-sama {$user->name}! 😊\nSenang bisa membantu. Jika ada fasilitas PLN yang bermasalah lagi, jangan ragu untuk chat saya atau langsung laporkan ke SIPANDAI. Semangat bertugas!";
        }

        // 10. Pertanyaan Admin Khusus
        if ($user->isAdmin() && (str_contains($msgLower, 'rekap') || str_contains($msgLower, 'laporan') || str_contains($msgLower, 'export'))) {
            $reportUrl = route('admin.reports.index');
            return "Halo Admin! Untuk melihat statistik agregat, grafik aduan, atau mendownload rekap laporan periode bulanan dalam format CSV/Excel, silakan akses menu [Rekap Laporan]({$reportUrl}).";
        }

        // 11. Jawaban Default Pintar
        $createUrl = route('employee.complaints.create');
        $indexUrl = route('employee.complaints.index');

        return "Terima kasih atas pertanyaannya, {$user->name}.\n"
            . "Sebagai asisten SIPANDAI PLN UPT Gandul, saya dapat membantu Anda seputar:\n"
            . "• **Pengecekan Tiket:** Ketik nomor tiket (misal: `SIP-2026-000001`) atau ketik *'tiket saya'*.\n"
            . "• **Bantuan Pelaporan:** Ketik kendala seperti *'AC rusak'*, *'lampu mati'*, atau *'toilet bocor'*.\n"
            . "• **Menu Cepat:** [Buat Pengaduan Baru]({$createUrl}) | [Lihat Riwayat Aduan]({$indexUrl})\n\n"
            . "*Ada hal lain yang dapat saya bantu?*";
    }

    /**
     * Konteks tiket user untuk prompt AI
     */
    protected function getUserComplaintsContext($user): string
    {
        $query = Complaint::with('category')->latest();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        $complaints = $query->take(5)->get();

        if ($complaints->isEmpty()) {
            return "Pengguna belum memiliki catatan pengaduan.";
        }

        $lines = [];
        foreach ($complaints as $c) {
            $catName = $c->category ? $c->category->name : ($c->custom_category ?? 'Lainnya');
            $lines[] = "- Tiket: {$c->ticket_number} | Judul: '{$c->title}' | Kategori: {$catName} | Lokasi: {$c->incident_location} | Status: {$c->status} | Tgl: " . $c->created_at->format('d/m/Y');
        }
        return implode("\n", $lines);
    }
}

