<?php

use App\Models\Alumni;
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

it('renders alumni portal page with correct props', function () {
    $response = $this->get(route('alumni.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Kelulusan/Alumni')
        ->has('alumniList.data')
        ->has('lulusanBelumAlumni')
        ->has('stats')
        ->has('filters')
    );
});

it('registers graduated siswa into alumni directory and sets status to alumni', function () {
    // Create new graduated siswa (status lulus, but not yet alumni)
    $siswa = Siswa::create([
        'nis' => '202307099',
        'nisn' => '0079998877',
        'nik' => '3307039903070099',
        'nm_siswa' => 'Grace Natalia',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2010-08-20',
        'jenis_kelamin' => 'Perempuan',
        'kd_agama' => 1,
        'alamat' => 'Jl. Dieng No. 45, Wonosobo',
        'status_siswa' => 'lulus',
    ]);

    $kelulusan = Kelulusan::create([
        'kd_siswa' => $siswa->kd_siswa,
        'kd_tahun' => 2,
        'tgl_lulus' => '2026-06-15',
        'no_ijazah' => 'DN-03/D-SMP/26/000998',
    ]);

    $response = $this->post(route('alumni.store'), [
        'kd_lulus' => $kelulusan->kd_lulus,
        'kegiatan_lanjutan' => 'Melanjutkan SMA',
        'instansi' => 'SMA Negeri 1 Wonosobo',
        'kontak' => '081298765432',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('alumni', [
        'kd_lulus' => $kelulusan->kd_lulus,
        'kegiatan_lanjutan' => 'Melanjutkan SMA',
        'instansi' => 'SMA Negeri 1 Wonosobo',
        'kontak' => '081298765432',
    ]);

    $siswa->refresh();
    expect($siswa->status_siswa)->toBe('alumni');
});

it('validates required fields when storing alumni', function () {
    $response = $this->post(route('alumni.store'), [
        'kd_lulus' => '',
        'kegiatan_lanjutan' => '',
        'instansi' => '',
    ]);

    $response->assertSessionHasErrors(['kd_lulus', 'kegiatan_lanjutan', 'instansi']);
});

it('can delete alumni record and reverts status to lulus', function () {
    $alumni = Alumni::with('kelulusan.siswa')->first();
    $siswa = $alumni->kelulusan->siswa;

    $response = $this->delete(route('alumni.destroy', $alumni->kd_alumni));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('alumni', [
        'kd_alumni' => $alumni->kd_alumni,
    ]);

    $siswa->refresh();
    expect($siswa->status_siswa)->toBe('lulus');
});
