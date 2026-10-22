<?php

use App\Models\Alumni;
use App\Models\DokumenSiswa;
use App\Models\Kelas;
use App\Models\Kelulusan;
use App\Models\Siswa;
use Database\Seeders\AgamaSeeder;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\SiswaSampleSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        AgamaSeeder::class,
        JenisDokumenSeeder::class,
        TahunAjaranSeeder::class,
        KelasSeeder::class,
        SiswaSampleSeeder::class,
    ]);
});

it('renders dashboard with correct aggregate student counts and pending queues', function () {
    $totalSiswa = Siswa::count();
    $siswaAktif = Siswa::where('status_siswa', 'aktif')->count();
    $totalKelas = Kelas::count();
    $dokumenPending = DokumenSiswa::where('status_verifikasi', 'belum')->count();
    $totalAlumni = Alumni::count();
    $totalLulus = Kelulusan::count();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('stats.totalSiswa', $totalSiswa)
        ->where('stats.siswaAktif', $siswaAktif)
        ->where('stats.totalKelas', $totalKelas)
        ->where('stats.dokumenPending', $dokumenPending)
        ->where('stats.totalAlumni', $totalAlumni)
        ->where('stats.totalLulus', $totalLulus)
        ->has('stats.lakiLaki')
        ->has('stats.perempuan')
        ->has('pendingDocuments')
    );
});
