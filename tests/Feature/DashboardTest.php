<?php

use App\Models\DokumenSiswa;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
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

it('renders dashboard with aggregate student and academic statistics', function () {
    $totalSiswa = Siswa::count();
    $siswaAktif = Siswa::where('status_siswa', 'aktif')->count();
    $totalKelas = Kelas::count();
    $dokumenPending = DokumenSiswa::where('status_verifikasi', 'belum')->count();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('stats.totalSiswa', $totalSiswa)
        ->where('stats.siswaAktif', $siswaAktif)
        ->where('stats.totalKelas', $totalKelas)
        ->where('stats.dokumenPending', $dokumenPending)
    );
});

it('shares the active academic year in inertia shared props', function () {
    $activeYear = TahunAjaran::where('aktif', true)->first();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->where('tahunAjaranAktif.kd_tahun', $activeYear->kd_tahun)
        ->where('tahunAjaranAktif.nm_tahun', $activeYear->nm_tahun)
        ->where('tahunAjaranAktif.aktif', true)
    );
});

it('shares session flash messages to inertia props', function () {
    $response = $this->withSession(['success' => 'Operasi berhasil dieksekusi'])
        ->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->where('flash.success', 'Operasi berhasil dieksekusi')
    );
});
