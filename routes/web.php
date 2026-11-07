<?php



use App\Http\Controllers\Admin\AdminComplaintController;

use App\Http\Controllers\Admin\AdminDashboardController;

use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Admin\EmployeeController;

use App\Http\Controllers\Admin\ReportController;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\ChatbotController;

use App\Http\Controllers\Employee\ComplaintController;

use App\Http\Controllers\Employee\DashboardController;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;



// Redirect root ke dashboard atau login

Route::get('/', function () {

    if (Auth::check()) {

        return Auth::user()->isAdmin()

            ? redirect()->route('admin.dashboard')

            : redirect()->route('employee.dashboard');

    }

    return redirect()->route('login');

});



// Autentikasi

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    //Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

    //Route::post('/register', [AuthController::class, 'register']);

});



Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');



// Tracking Pengaduan (Bisa diakses langsung dengan nomor tiket)

Route::get('/lacak', [ComplaintController::class, 'track'])->name('complaints.track');



// Rute Khusus Pegawai & Admin

Route::middleware(['auth'])->group(function () {

    // Dashboard & Pengaduan Pegawai

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('employee.dashboard');

    Route::get('/pengaduan', [ComplaintController::class, 'index'])->name('employee.complaints.index');

    Route::get('/pengaduan/buat', [ComplaintController::class, 'create'])->name('employee.complaints.create');

    Route::post('/pengaduan', [ComplaintController::class, 'store'])->name('employee.complaints.store');

    Route::get('/pengaduan/{id}', [ComplaintController::class, 'show'])->name('employee.complaints.show');



    // Profil Pegawai

    Route::get('/profil', [ComplaintController::class, 'profile'])->name('employee.profile');

    Route::put('/profil', [ComplaintController::class, 'updateProfile'])->name('employee.profile.update');



    // Chatbot AI SIPANDAI

    Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])->name('chatbot.message');

});



// Rute Khusus Admin UPT Gandul

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');



    // Kelola Pengaduan

    Route::get('/pengaduan', [AdminComplaintController::class, 'index'])->name('complaints.index');

    Route::get('/pengaduan/{id}', [AdminComplaintController::class, 'show'])->name('complaints.show');

    Route::post('/pengaduan/{id}/status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.update_status');



    // Kelola Kategori

    Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');

    Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');

    Route::put('/kategori/{id}', [CategoryController::class, 'update'])->name('categories.update');

    Route::delete('/kategori/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');



    // Kelola Pegawai

    Route::get('/pegawai', [EmployeeController::class, 'index'])->name('employees.index');

    Route::post('/pegawai', [EmployeeController::class, 'store'])->name('employees.store');

    Route::post('/pegawai/{id}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset_password');



    // Laporan & Rekap Statistik

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/laporan/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export_csv');

    // Route Profil Admin
     Route::get('/profil', [App\Http\Controllers\Admin\AdminProfileController::class, 'index'])->name('profile');

    Route::put('/profil', [App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('profile.update');
    // Contoh untuk rute Admin
Route::middleware(['auth', 'prevent.back'])->prefix('admin')->name('admin.')->group(function () {
    // Route admin di sini...
});

// Contoh untuk rute Pegawai / Umum yang butuh login
Route::middleware(['auth', 'prevent.back'])->group(function () {
    // Route pegawai di sini...
});
});