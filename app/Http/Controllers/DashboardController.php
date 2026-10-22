<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\DokumenSiswa;
use App\Models\Kelas;
use App\Models\Kelulusan;
use App\Models\Siswa;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard with real-time summary statistics and pending queues.
     */
    public function index(): Response
    {
        $stats = [
            'totalSiswa' => Siswa::count(),
            'siswaAktif' => Siswa::where('status_siswa', 'aktif')->count(),
            'totalKelas' => Kelas::count(),
            'dokumenPending' => DokumenSiswa::where('status_verifikasi', 'belum')->count(),
            'totalAlumni' => Alumni::count(),
            'totalLulus' => Kelulusan::count(),
            'lakiLaki' => Siswa::where('status_siswa', 'aktif')->where('jenis_kelamin', 'Laki-laki')->count(),
            'perempuan' => Siswa::where('status_siswa', 'aktif')->where('jenis_kelamin', 'Perempuan')->count(),
        ];

        $pendingDocuments = DokumenSiswa::with(['siswa', 'jenis'])
            ->where('status_verifikasi', 'belum')
            ->latest('kd_dokumen')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'pendingDocuments' => $pendingDocuments,
        ]);
    }
}
