<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Kategori Default UPT Gandul
        $categories = [
            ['name' => 'Update Keluarga', 'description' => 'Pembaruan data tanggungan keluarga pegawai'],
            ['name' => 'Update Kesehatan', 'description' => 'Pembaruan data asuransi dan kepesertaan kesehatan'],
            ['name' => 'Cuti', 'description' => 'Kendala pengajuan cuti tahunan, besar, atau melahirkan'],
            ['name' => 'Kendala SPPD', 'description' => 'Kendala sistem perjalanan dinas dan reimbursement'],
            ['name' => 'Kendala PJJ', 'description' => 'Kendala fasilitas dan teknis Pelatihan Jarak Jauh'],
            ['name' => 'Kendala PTB', 'description' => 'Kendala Program Tugas Belajar pegawai'],
            ['name' => 'Usulan Mutasi', 'description' => 'Permohonan dan tindak lanjut usulan mutasi internal'],
            ['name' => 'Fasilitas & Sarana', 'description' => 'Kerusakan atau kebutuhan fasilitas kantor / lingkungan UPT'],
            ['name' => 'IT & Jaringan', 'description' => 'Kendala perangkat komputer, akses email PLN, atau jaringan'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = Category::create([
                'name' => $cat['name'],
                'description' => $cat['description'],
                'is_active' => true,
            ]);
        }

        // 2. Seed Admin UPT Gandul
        $admin = User::create([
            'name' => 'Admin UPT Gandul',
            'nip' => '198501012010121001',
            'email' => 'admin@pln.co.id',
            'role' => 'admin',
            'unit_bagian' => 'Sub Bidang SDM & Umum',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
        ]);

        // 3. Seed Pegawai
        $pegawai1 = User::create([
            'name' => 'Ahmad Fauzi',
            'nip' => '199203152018021002',
            'email' => 'fauzi@pln.co.id',
            'role' => 'pegawai',
            'unit_bagian' => 'Pemeliharaan Jaringan & GI',
            'phone' => '081298765432',
            'password' => Hash::make('password'),
        ]);

        $pegawai2 = User::create([
            'name' => 'Dewi Lestari',
            'nip' => '199508182020122003',
            'email' => 'dewi@pln.co.id',
            'role' => 'pegawai',
            'unit_bagian' => 'Operasi Sistem & Proteksi',
            'phone' => '081377889900',
            'password' => Hash::make('password'),
        ]);

        // 4. Sample Complaints
        // Tiket 1: SIP-2026-000125 (Sedang Diproses - sesuai foto wireframe user)
        $complaint1 = Complaint::create([
            'ticket_number' => 'SIP-2026-000125',
            'user_id' => $pegawai1->id,
            'category_id' => $categoryModels['Fasilitas & Sarana']->id,
            'title' => 'Perbaikan AC Ruang Kontrol Lantai 2',
            'incident_location' => 'Gedung Utama Lantai 2',
            'incident_date' => '2026-09-09',
            'description' => 'Suhu AC tidak dingin dan mengeluarkan bunyi bising saat operasional piket siang sehingga mengganggu monitoring transmisi.',
            'status' => 'diproses',
            'admin_notes' => 'Laporan telah diverifikasi oleh tim umum. Teknisi pendingin ruangan sedang melakukan pemeriksaan unit outdoor.',
        ]);

        ComplaintHistory::create([
            'complaint_id' => $complaint1->id,
            'user_id' => $pegawai1->id,
            'status' => 'baru',
            'notes' => 'Pengaduan berhasil dibuat oleh pegawai',
            'created_at' => now()->subDays(4),
        ]);
        ComplaintHistory::create([
            'complaint_id' => $complaint1->id,
            'user_id' => $admin->id,
            'status' => 'diverifikasi',
            'notes' => 'Pengaduan telah diperiksa dan dinyatakan valid oleh Admin UPT Gandul',
            'created_at' => now()->subDays(3),
        ]);
        ComplaintHistory::create([
            'complaint_id' => $complaint1->id,
            'user_id' => $admin->id,
            'status' => 'diproses',
            'notes' => 'Sedang ditindaklanjuti oleh teknisi fasilitas & sarana gedung',
            'created_at' => now()->subDays(1),
        ]);

        // Tiket 2: SIP-2026-000124 (Selesai)
        $complaint2 = Complaint::create([
            'ticket_number' => 'SIP-2026-000124',
            'user_id' => $pegawai2->id,
            'category_id' => $categoryModels['Update Kesehatan']->id,
            'title' => 'Pengajuan Pembaruan Data Asuransi Kesehatan Keluarga',
            'incident_location' => 'Kantor UPT Gandul',
            'incident_date' => '2026-09-05',
            'description' => 'Penambahan data anak kedua untuk kepesertaan asuransi kesehatan pegawai PLN.',
            'status' => 'selesai',
            'admin_notes' => 'Data dan berkas akta kelahiran telah divalidasi. Kartu kepesertaan digital telah terbit di sistem kepegawaian.',
            'resolved_at' => now()->subDays(2),
        ]);

        ComplaintHistory::create([
            'complaint_id' => $complaint2->id,
            'user_id' => $pegawai2->id,
            'status' => 'baru',
            'notes' => 'Pengaduan dibuat',
            'created_at' => now()->subDays(7),
        ]);
        ComplaintHistory::create([
            'complaint_id' => $complaint2->id,
            'user_id' => $admin->id,
            'status' => 'diverifikasi',
            'notes' => 'Berkas lengkap dan diverifikasi',
            'created_at' => now()->subDays(5),
        ]);
        ComplaintHistory::create([
            'complaint_id' => $complaint2->id,
            'user_id' => $admin->id,
            'status' => 'diproses',
            'notes' => 'Proses input ke SAP PLN SDM',
            'created_at' => now()->subDays(3),
        ]);
        ComplaintHistory::create([
            'complaint_id' => $complaint2->id,
            'user_id' => $admin->id,
            'status' => 'selesai',
            'notes' => 'Pembaruan data telah rampung dan kartu aktif.',
            'created_at' => now()->subDays(2),
        ]);

        // Tiket 3: SIP-2026-000126 (Baru)
        $complaint3 = Complaint::create([
            'ticket_number' => 'SIP-2026-000126',
            'user_id' => $pegawai1->id,
            'category_id' => $categoryModels['Kendala SPPD']->id,
            'title' => 'Kendala Validasi Tiket Kereta di Aplikasi e-SPPD',
            'incident_location' => 'Sistem e-SPPD',
            'incident_date' => '2026-09-13',
            'description' => 'Nomor tiket dinas perjalanan ke Bandung tidak terbaca otomatis di sistem pertanggungjawaban SPPD.',
            'status' => 'baru',
        ]);

        ComplaintHistory::create([
            'complaint_id' => $complaint3->id,
            'user_id' => $pegawai1->id,
            'status' => 'baru',
            'notes' => 'Pengaduan berhasil dikirim oleh pelapor',
            'created_at' => now()->subHours(5),
        ]);
    }
}
