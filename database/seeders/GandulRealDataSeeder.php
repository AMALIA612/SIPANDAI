<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GandulRealDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Kategori Lengkap (10 Kategori Resmi)
        $categoriesData = [
            ['name' => 'Update Keluarga', 'description' => 'Pembaruan data tanggungan keluarga pegawai'],
            ['name' => 'Update Kesehatan', 'description' => 'Pembaruan data asuransi dan kepesertaan kesehatan'],
            ['name' => 'Cuti', 'description' => 'Kendala pengajuan cuti tahunan, besar, atau melahirkan'],
            ['name' => 'Kendala SPPD', 'description' => 'Kendala sistem perjalanan dinas dan reimbursement'],
            ['name' => 'Kendala PJJ', 'description' => 'Kendala fasilitas dan teknis Pelatihan Jarak Jauh'],
            ['name' => 'Kendala PTB', 'description' => 'Kendala Program Tugas Belajar pegawai'],
            ['name' => 'Usulan Mutasi', 'description' => 'Permohonan dan tindak lanjut usulan mutasi internal'],
            ['name' => 'Fasilitas & Sarana', 'description' => 'Kerusakan atau kebutuhan fasilitas kantor / lingkungan UPT'],
            ['name' => 'IT & Jaringan', 'description' => 'Kendala perangkat komputer, akses email PLN, atau jaringan'],
            ['name' => 'Lainnya', 'description' => 'Buat catatan anda sendiri'],
        ];

        $categoriesMap = [];
        foreach ($categoriesData as $idx => $cData) {
            $cat = Category::updateOrCreate(
                ['name' => $cData['name']],
                ['description' => $cData['description'], 'is_active' => true]
            );
            $categoriesMap[$cData['name']] = $cat->id;
        }

        // 2. Baca Data Pegawai Riil Gandul dari JSON
        $jsonPath = 'C:/Users/AMALIA INDAH/.gemini/antigravity/brain/6bf43932-3a59-42c4-9727-975a822c9611/scratch/cleaned_gandul_employees.json';
        if (!file_exists($jsonPath)) {
            $this->command->error("File cleaned_gandul_employees.json tidak ditemukan!");
            return;
        }

        $employees = json_decode(file_get_contents($jsonPath), true);
        $userModels = [];

        // Hapus data pengaduan & riwayat lama serta pegawai selain admin sistem
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ComplaintHistory::truncate();
        Complaint::truncate();
        User::where('email', '!=', 'admin@pln.co.id')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Pastikan Akun Admin Sistem Utama tetap ada
        $adminSystem = User::firstOrCreate(
            ['email' => 'admin@pln.co.id'],
            [
                'name' => 'Admin Sarpras UPT Gandul',
                'nip' => '198501012010121001',
                'role' => 'admin',
                'unit_bagian' => 'Bagian Keuangan dan Umum',
                'phone' => '081288889999',
                'password' => Hash::make('password'),
            ]
        );

        $defaultPassword = Hash::make('password');

        foreach ($employees as $emp) {
            $phone = '0812' . rand(10000000, 99999999);
            $user = User::updateOrCreate(
                ['nip' => $emp['nip']],
                [
                    'name' => $emp['name'],
                    'email' => $emp['email'],
                    'role' => $emp['role'],
                    'unit_bagian' => $emp['unit_bagian'],
                    'phone' => $phone,
                    'password' => $defaultPassword,
                ]
            );
            $userModels[] = $user;
        }

        echo "Berhasil memuat " . count($userModels) . " pegawai resmi UPT Gandul (Kantor Induk) ke tabel users.\n";

        // 3. Generate Data Pengaduan Realistis Khusus UPT Gandul (75 Tiket Jan-Sep 2026)
        $complaintTemplates = [
            'Fasilitas & Sarana' => [
                ['title' => 'Kerusakan AC Ruang Rapat Sinergi UPT Gandul', 'desc' => 'AC split di ruang rapat sinergi tidak dingin dan mengeluarkan tetesan air, mengganggu jalannya rapat koordinasi.'],
                ['title' => 'Lampu Penerangan Lobi Utama Kantor UPT Gandul Redup', 'desc' => 'Beberapa lampu downlight LED di area lobi depan padam dan perlu penggantian bohlam baru.'],
                ['title' => 'Perbaikan Wastafel & Saluran Air Toilet Lt 2 Kantor UPT', 'desc' => 'Pipa pembuangan air wastafel toilet pria lantai 2 bocor dan air meluap ke lantai.'],
                ['title' => 'Pintu Otomatis Sensor Kaca Lobi Kantor Gandul Macet', 'desc' => 'Sensor pintu kaca masuk lobi utama tidak merespons gerakan sehingga harus dibuka secara manual.'],
                ['title' => 'Exhaust Fan Toilet Pegawai Lt 1 Rusak', 'desc' => 'Kipas exhaust sirkulasi udara toilet lantai 1 mati total dan menimbulkan bau pengap.'],
                ['title' => 'Plafon Gypsum Ruang Perencanaan UPT Gandul Bocor', 'desc' => 'Terdapat rembesan air hujan dari atap lantai 2 yang menyebabkan plafon gypsum melengkung bernoda.'],
                ['title' => 'Engsel Pintu Masuk Ruang Arsip UPT Gandul Rusak', 'desc' => 'Pintu kayu ruang arsip sulit ditutup rapat karena engsel bagian atas longgar.'],
                ['title' => 'Penggantian Kursi Kerja Ruang Konstruksi UPT Gandul', 'desc' => 'Dua unit kursi kerja staf patah pada roda dan tuas hidrolik sudah turun.'],
            ],
            'IT & Jaringan' => [
                ['title' => 'Koneksi Jaringan LAN Ruang Rapat Utama Terputus', 'desc' => 'Port LAN meja rapat tidak mendeteksi koneksi jaringan lokal PLN, menghambat zoom meeting.'],
                ['title' => 'Kendala Login Akun SAP Korporat Pegawai UPT Gandul', 'desc' => 'Sistem SAP memunculkan peringatan user locked saat hendak memproses transaksi dokumen.'],
                ['title' => 'Printer Jaringan Ruang Keuangan Paper Jam Berulang', 'desc' => 'Roller penarik kertas printer HP LaserJet jaringan aus sehingga kertas macet saat cetak voucher.'],
                ['title' => 'PC Komputer Ruang Perencanaan Sering Blue Screen', 'desc' => 'Komputer kerja staf tiba-tiba restart sendiri dan muncul layar biru (memory dump).'],
                ['title' => 'Permohonan Reset Password Email Korporat PLN', 'desc' => 'Lupa kata sandi akun email resmi PLN setelah jadwal rotasi password berkala.'],
                ['title' => 'Gangguan Akses Portal Intranet & VPN Transmisi', 'desc' => 'Tidak bisa terhubung ke server portal intranet PLN melalui koneksi internal kantor.'],
            ],
            'Update Keluarga' => [
                ['title' => 'Pembaruan Data Akta Lahir & Kartu Keluarga Pegawai', 'desc' => 'Mengajukan penambahan tanggungan anak kedua pada sistem SAP SDM dan lampiran KK baru.'],
                ['title' => 'Penyesuaian Status Pernikahan & Tunjangan Istri', 'desc' => 'Pembaruan data buku nikah resmi untuk pemutakhiran tunjangan keluarga kepegawaian.'],
                ['title' => 'Koreksi Nomor Induk Kependudukan (NIK) Anak di SAP', 'desc' => 'Terdapat salah input 1 digit pada NIK tanggungan anak di data induk kepegawaian.'],
            ],
            'Update Kesehatan' => [
                ['title' => 'Pembaruan Fasilitas Kesehatan Faskes 1 BPJS Kesehatan', 'desc' => 'Pengajuan pindah klinik Faskes 1 BPJS Kesehatan pegawai dan keluarga ke area Depok/Cinere.'],
                ['title' => 'Klaim Reimbursement Kacamata Resep Dokter', 'desc' => 'Mengajukan klaim bantuan pergantian lensa kacamata kerja sesuai plafon tahunan pegawai.'],
                ['title' => 'Kendala Kartu Peserta Asuransi Kesehatan Tambahan', 'desc' => 'Kartu Mandiri Inhealth belum aktif saat digunakan di rumah sakit rujukan.'],
            ],
            'Cuti' => [
                ['title' => 'Sinkronisasi Sisa Kuota Cuti Tahunan Pegawai', 'desc' => 'Jumlah sisa cuti di portal web SIPANDAI berbeda dengan rekap absensi SAP pusat.'],
                ['title' => 'Pengajuan Cuti Besar Masa Kerja 6 Tahun', 'desc' => 'Pengajuan hak cuti besar tahun berjalan untuk keperluan keluarga.'],
                ['title' => 'Pengajuan Cuti Melahirkan & Pendampingan', 'desc' => 'Pengajuan cuti melahirkan 3 bulan sesuai surat rujukan dokter spesialis kandungan.'],
            ],
            'Kendala SPPD' => [
                ['title' => 'Koreksi Nilai Reimbursement Transportasi Tiket SPPD', 'desc' => 'Kwitansi tiket perjalanan dinas belum masuk dalam total transfer uang muka dinas.'],
                ['title' => 'Kendala Approval SPPD Perjalanan Dinas UIT JBB', 'desc' => 'Tiket SPPD dinas ke kantor UIT JBB tertahan di status approval atasan.'],
                ['title' => 'Penyesuaian Tanggal Pelaksanaan Tugas SPPD Kantor', 'desc' => 'Jadwal penugasan dinas diundur sehingga tanggal tiket SPPD perlu disesuaikan.'],
            ],
            'Kendala PJJ' => [
                ['title' => 'Akses Video Materi E-Learning PJJ K3L Error', 'desc' => 'Video modul pembelajaran sertifikasi K3L tidak bisa diputar di peramban web.'],
                ['title' => 'Sertifikat Kelulusan Webinar PJJ Belum Diterbitkan', 'desc' => 'Ujian post-test sudah lulus di atas passing grade namun sertifikat belum muncul di profil.'],
            ],
            'Kendala PTB' => [
                ['title' => 'Pengajuan Surat Keterangan Izin Belajar / PTB', 'desc' => 'Permohonan surat pengantar resmi dari UPT Gandul untuk kampus pascasarjana.'],
                ['title' => 'Pencairan Bantuan Biaya Pendidikan Semester Genap PTB', 'desc' => 'Berkas bukti pembayaran kuliah semester berjalan telah diunggah dan menunggu verifikasi.'],
            ],
            'Usulan Mutasi' => [
                ['title' => 'Permohonan Tindak Lanjut Usulan Mutasi Domisili', 'desc' => 'Permohonan koordinasi berkas usulan mutasi internal sesuai form rekomendasi.'],
                ['title' => 'Konsultasi Peminatan Jalur Karir Staf UPT Gandul', 'desc' => 'Pengajuan konsultasi karir pengembangan kompetensi pegawai UPT Gandul.'],
            ],
            'Lainnya' => [
                ['title' => 'Usulan Tempat Sampah Pilah Khusus B3 di Gudang', 'desc' => 'Kebutuhan wadah sampah limbah B3 kain majun berminyak dan baterai bekas.'],
                ['title' => 'Permohonan Papan Petunjuk Jalur Evakuasi Halaman Belakang', 'desc' => 'Rambu petunjuk titik kumpul (assembly point) di area parkir UPT Gandul pudar.'],
                ['title' => 'Pengadaan Alat APD Rompi & Helm Tamu Kantor Gandul', 'desc' => 'Stok rompi visitor di pos sekuriti gerbang UPT Gandul menipis saat kunjungan audit.'],
            ],
        ];

        $locations = [
            'Gedung Kantor UPT Gandul - Lantai 1',
            'Gedung Kantor UPT Gandul - Lantai 2',
            'Ruang Rapat Sinergi UPT Gandul',
            'Ruang Enjiniring & Perencanaan UPT Gandul',
            'Ruang Keuangan & Administrasi UPT Gandul',
            'Ruang K3L & Keamanan UPT Gandul',
            'Gudang Logistik & Sarpras UPT Gandul',
            'Area Parkir & Pos Keamanan UPT Gandul',
            'Ruang Server & IT Kantor UPT Gandul',
            'Lobi Utama Kantor UPT Gandul',
        ];

        // Buat 75 tiket pengaduan proporsional untuk 44 pegawai
        $year = 2026;

        // Distribusi tiket per bulan (Jan - Sep 2026)
        $monthDistribution = [
            1 => 8,  // Jan
            2 => 7,  // Feb
            3 => 9,  // Mar
            4 => 8,  // Apr
            5 => 10, // Mei
            6 => 9,  // Jun
            7 => 11, // Jul
            8 => 8,  // Agu
            9 => 5,  // Sep (bulan berjalan)
        ];

        $ticketCounter = 1;

        foreach ($monthDistribution as $month => $count) {
            for ($i = 0; $i < $count; $i++) {
                $day = rand(1, ($month == 2 ? 28 : ($month == 9 ? 18 : 30)));
                $createdDate = Carbon::create($year, $month, $day, rand(8, 16), rand(0, 59), rand(0, 59));
                
                // Pilih kategori acak dengan pembobotan realistis (Fasilitas & IT lebih sering)
                $catWeights = [
                    'Fasilitas & Sarana' => 30,
                    'IT & Jaringan' => 25,
                    'Kendala SPPD' => 12,
                    'Cuti' => 10,
                    'Update Kesehatan' => 8,
                    'Update Keluarga' => 5,
                    'Kendala PJJ' => 3,
                    'Kendala PTB' => 2,
                    'Usulan Mutasi' => 2,
                    'Lainnya' => 3,
                ];

                $rand = rand(1, 100);
                $cum = 0;
                $chosenCat = 'Fasilitas & Sarana';
                foreach ($catWeights as $catName => $weight) {
                    $cum += $weight;
                    if ($rand <= $cum) {
                        $chosenCat = $catName;
                        break;
                    }
                }

                $categoryId = $categoriesMap[$chosenCat];
                $templates = $complaintTemplates[$chosenCat];
                $template = $templates[array_rand($templates)];

                // Pilih user pelapor acak dari daftar pegawai UPT Gandul
                $randomUser = $userModels[array_rand($userModels)];
                $location = $locations[array_rand($locations)];

                // Tentukan status berdasarkan usia tiket
                if ($month <= 7) {
                    // Jan - Jul: 95% Selesai, 5% Ditolak
                    $status = (rand(1, 100) <= 95) ? 'selesai' : 'ditolak';
                } elseif ($month == 8) {
                    // Agustus: 80% Selesai, 15% Diproses, 5% Ditolak
                    $rStat = rand(1, 100);
                    if ($rStat <= 80) $status = 'selesai';
                    elseif ($rStat <= 95) $status = 'diproses';
                    else $status = 'ditolak';
                } else {
                    // September (Bulan Ini): Beragam
                    $rStat = rand(1, 100);
                    if ($rStat <= 20) $status = 'selesai';
                    elseif ($rStat <= 60) $status = 'diproses';
                    elseif ($rStat <= 85) $status = 'diverifikasi';
                    else $status = 'baru';
                }

                $resolvedAt = null;
                $adminNotes = null;

                if ($status === 'selesai') {
                    $durationDays = rand(1, 5);
                    $resolvedAt = (clone $createdDate)->addDays($durationDays)->addHours(rand(1, 4));
                    $adminNotes = 'Pekerjaan perbaikan / tindak lanjut telah diselesaikan dengan baik oleh tim Sarpras/Teknisi.';
                } elseif ($status === 'ditolak') {
                    $adminNotes = 'Pengaduan tidak dapat diproses: Bukan merupakan wewenang pemeliharaan fasilitas UPT Gandul.';
                } elseif ($status === 'diproses') {
                    $adminNotes = 'Tiket telah diteruskan ke tim teknisi lapangan dan sedang dalam tahap penanganan.';
                } elseif ($status === 'diverifikasi') {
                    $adminNotes = 'Laporan telah diverifikasi oleh Admin Sarpras dan dijadwalkan untuk penanganan teknisi.';
                }

                $ticketNumber = 'SIP-' . $year . '-' . str_pad($ticketCounter, 6, '0', STR_PAD_LEFT);

                $complaint = Complaint::create([
                    'ticket_number' => $ticketNumber,
                    'user_id' => $randomUser->id,
                    'category_id' => $categoryId,
                    'custom_category' => null,
                    'title' => $template['title'],
                    'incident_location' => $location,
                    'incident_date' => $createdDate->format('Y-m-d'),
                    'description' => $template['desc'] . " Mohon dapat ditindaklanjuti demi kelancaran operasional.",
                    'attachment' => null,
                    'status' => $status,
                    'admin_notes' => $adminNotes,
                    'resolution_proof' => null,
                    'resolved_at' => $resolvedAt,
                    'created_at' => $createdDate,
                    'updated_at' => $resolvedAt ?: $createdDate,
                ]);

                // Riwayat 1: Pengaduan Dibuat
                ComplaintHistory::create([
                    'complaint_id' => $complaint->id,
                    'user_id' => $randomUser->id,
                    'status' => 'baru',
                    'notes' => 'Pengaduan baru berhasil diajukan oleh pegawai.',
                    'created_at' => $createdDate,
                ]);

                // Riwayat jika Diverifikasi
                if (in_array($status, ['diverifikasi', 'diproses', 'selesai'])) {
                    $verifDate = (clone $createdDate)->addHours(rand(2, 6));
                    ComplaintHistory::create([
                        'complaint_id' => $complaint->id,
                        'user_id' => $adminSystem->id,
                        'status' => 'diverifikasi',
                        'notes' => 'Pengaduan telah diverifikasi oleh Admin Sarpras UPT Gandul.',
                        'created_at' => $verifDate,
                    ]);
                }

                // Riwayat jika Diproses
                if (in_array($status, ['diproses', 'selesai'])) {
                    $processDate = (clone $createdDate)->addDays(1)->addHours(rand(1, 3));
                    ComplaintHistory::create([
                        'complaint_id' => $complaint->id,
                        'user_id' => $adminSystem->id,
                        'status' => 'diproses',
                        'notes' => 'Tim teknisi telah ditugaskan dan penanganan sedang berjalan di lapangan.',
                        'created_at' => $processDate,
                    ]);
                }

                // Riwayat jika Selesai
                if ($status === 'selesai') {
                    ComplaintHistory::create([
                        'complaint_id' => $complaint->id,
                        'user_id' => $adminSystem->id,
                        'status' => 'selesai',
                        'notes' => 'Pekerjaan perbaikan telah selesai dilaksanakan dan fasilitas berfungsi normal kembali.',
                        'created_at' => $resolvedAt,
                    ]);
                }

                // Riwayat jika Ditolak
                if ($status === 'ditolak') {
                    $rejectDate = (clone $createdDate)->addHours(rand(4, 12));
                    ComplaintHistory::create([
                        'complaint_id' => $complaint->id,
                        'user_id' => $adminSystem->id,
                        'status' => 'ditolak',
                        'notes' => 'Pengaduan ditolak: Bukan wewenang pemeliharaan fasilitas UPT Gandul.',
                        'created_at' => $rejectDate,
                    ]);
                }

                $ticketCounter++;
            }
        }

        echo "Berhasil membuat " . ($ticketCounter - 1) . " data pengaduan realistis beserta timeline riwayatnya!\n";
    }
}
