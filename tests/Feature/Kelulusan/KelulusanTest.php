<?php

use App\Models\Kelas;
use App\Models\Kelulusan;
use App\Models\KenaikanKelas;
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

it('renders kelulusan management page with correct props', function () {
    $response = $this->get(route('kelulusan.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Kelulusan/Index')
        ->has('calonLulusan')
        ->has('kelulusanList.data')
        ->has('tahunAjaranList')
        ->has('stats')
        ->has('filters')
    );
});

it('graduates grade 9 siswa and assigns unique no ijazah', function () {
    $siswa = Siswa::where('status_siswa', 'aktif')->first();
    $kelas9 = Kelas::where('tingkat', 9)->first();
    $tahunAjaran = TahunAjaran::first();

    // Assign student to grade 9
    KenaikanKelas::create([
        'kd_siswa' => $siswa->kd_siswa,
        'kd_tahun' => $tahunAjaran->kd_tahun,
        'kd_kelas_asal' => 4, // 8A
        'kd_kelas_tujuan' => $kelas9->kd_kelas,
        'status' => 'naik',
    ]);

    $response = $this->post(route('kelulusan.store'), [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_tahun' => $tahunAjaran->kd_tahun,
        'tgl_lulus' => '2026-06-15',
        'no_ijazah' => 'DN-03/D-SMP/26/000999',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('kelulusan', [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_tahun' => $tahunAjaran->kd_tahun,
        'no_ijazah' => 'DN-03/D-SMP/26/000999',
    ]);

    $siswa->refresh();
    expect($siswa->status_siswa)->toBe('lulus');
});

it('prevents non-grade 9 siswa from being graduated', function () {
    // Siswa 1 is in grade 7 without grade 9 kenaikan kelas
    $siswa = Siswa::where('nis', '202607001')->first();
    $tahunAjaran = TahunAjaran::first();

    $response = $this->post(route('kelulusan.store'), [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_tahun' => $tahunAjaran->kd_tahun,
        'tgl_lulus' => '2026-06-15',
        'no_ijazah' => 'DN-03/TEST/001',
    ]);

    $response->assertSessionHasErrors(['kd_siswa']);
    $this->assertDatabaseMissing('kelulusan', [
        'kd_siswa' => $siswa->kd_siswa,
    ]);
});

it('validates unique no ijazah', function () {
    $siswa = Siswa::where('status_siswa', 'aktif')->first();
    $kelas9 = Kelas::where('tingkat', 9)->first();
    $tahunAjaran = TahunAjaran::first();

    KenaikanKelas::create([
        'kd_siswa' => $siswa->kd_siswa,
        'kd_tahun' => $tahunAjaran->kd_tahun,
        'kd_kelas_asal' => 4,
        'kd_kelas_tujuan' => $kelas9->kd_kelas,
        'status' => 'naik',
    ]);

    // Samuel Setiawan has seeded no_ijazah 'DN-03/D-SMP/26/000123'
    $response = $this->post(route('kelulusan.store'), [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_tahun' => $tahunAjaran->kd_tahun,
        'tgl_lulus' => '2026-06-15',
        'no_ijazah' => 'DN-03/D-SMP/26/000123',
    ]);

    $response->assertSessionHasErrors(['no_ijazah']);
});

it('can cancel or delete graduation record and restores status to aktif', function () {
    $kelulusan = Kelulusan::with('siswa')->whereHas('siswa', fn ($q) => $q->where('nis', '202307003'))->first();
    $siswa = $kelulusan->siswa;

    $response = $this->delete(route('kelulusan.destroy', $kelulusan->kd_lulus));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('kelulusan', [
        'kd_lulus' => $kelulusan->kd_lulus,
    ]);

    $siswa->refresh();
    expect($siswa->status_siswa)->toBe('aktif');
});
