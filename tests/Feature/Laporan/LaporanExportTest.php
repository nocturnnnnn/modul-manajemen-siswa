<?php

use App\Models\Kelas;
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

it('renders laporan index page with filter options and master data', function () {
    $response = $this->get(route('laporan.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Laporan/Index')
        ->where('reportType', 'master_siswa')
        ->has('reportData.rows')
        ->has('kelasList')
        ->has('tahunAjaranList')
        ->has('agamaList')
    );
});

it('filters student reports by class and academic year', function () {
    $kelas = Kelas::where('nm_kelas', '8A')->first();
    $tahun = TahunAjaran::first();

    $response = $this->get(route('laporan.index', [
        'type' => 'rombel_kelas',
        'kd_kelas' => $kelas->kd_kelas,
        'kd_tahun' => $tahun->kd_tahun,
    ]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Laporan/Index')
        ->where('reportType', 'rombel_kelas')
        ->has('reportData.rows')
        ->where('reportData.title', 'Laporan Pembagian Siswa per Rombel Kelas')
    );
});

it('filters mutasi and pindah sekolah reports', function () {
    $response = $this->get(route('laporan.index', [
        'type' => 'mutasi',
        'jenis_mutasi' => 'pindah',
    ]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Laporan/Index')
        ->where('reportType', 'mutasi')
        ->has('reportData.rows')
        ->where('reportData.title', 'Laporan Rekapitulasi Mutasi & Pindah Sekolah')
    );
});

it('filters kelulusan and alumni reports', function () {
    $response = $this->get(route('laporan.index', [
        'type' => 'kelulusan_alumni',
        'kegiatan_lanjutan' => 'Melanjutkan SMA',
    ]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Laporan/Index')
        ->where('reportType', 'kelulusan_alumni')
        ->has('reportData.rows')
        ->where('reportData.title', 'Laporan Rekapitulasi Kelulusan & Penelusuran Alumni')
    );
});

it('renders printable view with official school header', function () {
    $response = $this->get(route('laporan.cetak', [
        'type' => 'master_siswa',
    ]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Laporan/Cetak')
        ->has('reportData.rows')
        ->has('tanggalCetak')
        ->has('tahunAktif')
    );
});
