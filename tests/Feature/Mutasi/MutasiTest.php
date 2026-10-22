<?php

use App\Models\Mutasi;
use App\Models\PindahSekolah;
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

it('renders mutasi and pindah sekolah management page', function () {
    $response = $this->get(route('mutasi.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Mutasi/Index')
        ->has('mutasiList.data')
        ->has('pindahList.data')
        ->has('siswaList')
        ->has('stats')
        ->has('filters')
    );
});

it('records pindah sekolah and changes status_siswa to pindah', function () {
    $siswa = Siswa::where('status_siswa', 'aktif')->first();

    $response = $this->post(route('mutasi.pindah'), [
        'kd_siswa' => $siswa->kd_siswa,
        'sekolah_tujuan' => 'SMP Negeri 1 Wonosobo',
        'tgl_pindah' => '2026-10-15',
        'alasan' => 'Mengikuti kepindahan domisili orang tua ke luar kota.',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('pindah_sekolah', [
        'kd_siswa' => $siswa->kd_siswa,
        'sekolah_tujuan' => 'SMP Negeri 1 Wonosobo',
        'alasan' => 'Mengikuti kepindahan domisili orang tua ke luar kota.',
    ]);

    $siswa->refresh();
    expect($siswa->status_siswa)->toBe('pindah');
});

it('records incoming or internal mutasi correctly', function () {
    $siswa = Siswa::where('status_siswa', 'aktif')->first();

    $response = $this->post(route('mutasi.store'), [
        'kd_siswa' => $siswa->kd_siswa,
        'jenis_mutasi' => 'Internal',
        'tgl_mutasi' => '2026-09-01',
        'no_surat' => '421.3/085/SMPK.1/2026',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('mutasi', [
        'kd_siswa' => $siswa->kd_siswa,
        'jenis_mutasi' => 'Internal',
        'no_surat' => '421.3/085/SMPK.1/2026',
    ]);

    $siswa->refresh();
    expect($siswa->status_siswa)->toBe('aktif');
});

it('records outgoing mutasi and updates status_siswa to mutasi', function () {
    $siswa = Siswa::where('status_siswa', 'aktif')->first();

    $response = $this->post(route('mutasi.store'), [
        'kd_siswa' => $siswa->kd_siswa,
        'jenis_mutasi' => 'Keluar',
        'tgl_mutasi' => '2026-10-20',
        'no_surat' => '421.3/099/SMPK.1/2026',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('mutasi', [
        'kd_siswa' => $siswa->kd_siswa,
        'jenis_mutasi' => 'Keluar',
        'no_surat' => '421.3/099/SMPK.1/2026',
    ]);

    $siswa->refresh();
    expect($siswa->status_siswa)->toBe('mutasi');
});

it('requires valid reason and destination school for pindah sekolah', function () {
    $siswa = Siswa::first();

    $response = $this->post(route('mutasi.pindah'), [
        'kd_siswa' => $siswa->kd_siswa,
        'sekolah_tujuan' => '',
        'tgl_pindah' => '',
        'alasan' => '',
    ]);

    $response->assertSessionHasErrors(['sekolah_tujuan', 'tgl_pindah', 'alasan']);
});

it('can delete mutasi and restore student status', function () {
    $siswa = Siswa::where('status_siswa', 'aktif')->first();

    // Create mutasi keluar
    $mutasi = Mutasi::create([
        'kd_siswa' => $siswa->kd_siswa,
        'jenis_mutasi' => 'Keluar',
        'tgl_mutasi' => '2026-10-01',
        'no_surat' => '421.3/TEST/2026',
    ]);
    $siswa->update(['status_siswa' => 'mutasi']);

    $response = $this->delete(route('mutasi.destroy', $mutasi->kd_mutasi));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('mutasi', [
        'kd_mutasi' => $mutasi->kd_mutasi,
    ]);

    $siswa->refresh();
    expect($siswa->status_siswa)->toBe('aktif');
});

it('can delete pindah sekolah record and restore student status', function () {
    $siswa = Siswa::where('status_siswa', 'aktif')->first();

    // Create pindah sekolah
    $pindah = PindahSekolah::create([
        'kd_siswa' => $siswa->kd_siswa,
        'sekolah_tujuan' => 'SMP Luar Kota',
        'tgl_pindah' => '2026-10-02',
        'alasan' => 'Pindah domisili',
    ]);
    $siswa->update(['status_siswa' => 'pindah']);

    $response = $this->delete(route('mutasi.destroy-pindah', $pindah->kd_pindah));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('pindah_sekolah', [
        'kd_pindah' => $pindah->kd_pindah,
    ]);

    $siswa->refresh();
    expect($siswa->status_siswa)->toBe('aktif');
});
