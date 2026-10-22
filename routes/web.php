<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenSiswaController;
use App\Http\Controllers\KelulusanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Master\AgamaController;
use App\Http\Controllers\Master\JenisDokumenController;
use App\Http\Controllers\Master\KelasController;
use App\Http\Controllers\Master\TahunAjaranController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Root Route Redirection
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? app(DashboardController::class)->index()
        : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Protected Application Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Modul Siswa & Pendaftaran Siswa Baru
    |--------------------------------------------------------------------------
    */
    Route::resource('siswa', SiswaController::class);

    /*
    |--------------------------------------------------------------------------
    | Modul Pengelolaan Dokumen Digital
    |--------------------------------------------------------------------------
    */
    Route::prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/', [DokumenSiswaController::class, 'index'])->name('index');
        Route::post('/upload', [DokumenSiswaController::class, 'upload'])->name('upload');
        Route::put('/{dokumenSiswa}/verify', [DokumenSiswaController::class, 'verify'])->name('verify');
        Route::delete('/{dokumenSiswa}', [DokumenSiswaController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Evaluasi Akademik & Kenaikan Kelas
    |--------------------------------------------------------------------------
    */
    Route::prefix('akademik')->name('akademik.')->group(function () {
        Route::get('/kenaikan-kelas', [AkademikController::class, 'index'])->name('kenaikan-kelas.index');
        Route::post('/kenaikan-kelas', [AkademikController::class, 'process'])->name('kenaikan-kelas.process');
        Route::delete('/kenaikan-kelas/{kenaikanKelas}', [AkademikController::class, 'destroy'])->name('kenaikan-kelas.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Mutasi & Pindah Sekolah
    |--------------------------------------------------------------------------
    */
    Route::prefix('mutasi')->name('mutasi.')->group(function () {
        Route::get('/', [MutasiController::class, 'index'])->name('index');
        Route::post('/catat', [MutasiController::class, 'storeMutasi'])->name('store');
        Route::post('/pindah', [MutasiController::class, 'storePindah'])->name('pindah');
        Route::delete('/{mutasi}', [MutasiController::class, 'destroyMutasi'])->name('destroy');
        Route::delete('/pindah/{pindahSekolah}', [MutasiController::class, 'destroyPindah'])->name('destroy-pindah');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Kelulusan & Pengelolaan Alumni
    |--------------------------------------------------------------------------
    */
    Route::prefix('kelulusan')->name('kelulusan.')->group(function () {
        Route::get('/', [KelulusanController::class, 'index'])->name('index');
        Route::post('/proses', [KelulusanController::class, 'store'])->name('store');
        Route::delete('/{kelulusan}', [KelulusanController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('alumni')->name('alumni.')->group(function () {
        Route::get('/', [AlumniController::class, 'index'])->name('index');
        Route::post('/', [AlumniController::class, 'store'])->name('store');
        Route::delete('/{alumni}', [AlumniController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Pelaporan & Cetak Dokumen
    |--------------------------------------------------------------------------
    */
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/cetak', [LaporanController::class, 'cetak'])->name('cetak');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Master Data Referensi
    |--------------------------------------------------------------------------
    */
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('agama', AgamaController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('kelas', KelasController::class)
            ->parameters(['kelas' => 'kelas'])
            ->except(['create', 'show', 'edit']);

        Route::post('tahun-ajaran/{tahunAjaran}/activate', [TahunAjaranController::class, 'activate'])
            ->name('tahun-ajaran.activate');

        Route::resource('tahun-ajaran', TahunAjaranController::class)
            ->parameters(['tahun-ajaran' => 'tahunAjaran'])
            ->except(['create', 'show', 'edit']);

        Route::resource('jenis-dokumen', JenisDokumenController::class)
            ->parameters(['jenis-dokumen' => 'jenisDokumen'])
            ->except(['create', 'show', 'edit']);
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Profil Pengguna
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Administrasi Sistem (Khusus Superadmin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:superadmin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)
            ->parameters(['users' => 'user'])
            ->except(['create', 'show', 'edit']);
    });
});

/*
|--------------------------------------------------------------------------
| Preview Halaman Error (Testing & Dev)
|--------------------------------------------------------------------------
*/
if (app()->environment(['local', 'testing'])) {
    Route::get('/errors/{status}', function (int $status) {
        abort_if(! in_array($status, [403, 404, 419, 500, 503, 505]), 404);

        return Inertia::render('Error', [
            'status' => $status,
        ]);
    })->name('errors.preview');
}
