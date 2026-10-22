<?php

use App\Models\Alumni;
use App\Models\Keluarga;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use Database\Seeders\AgamaSeeder;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\SiswaSampleSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

it('rejects duplicate nis on siswa', function () {
    $existing = Siswa::first();

    expect(fn () => Siswa::create([
        'nis' => $existing->nis, // Duplicate
        'nisn' => '9999999999',
        'nik' => '9999999999999999',
        'nm_siswa' => 'Duplicate NIS Student',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2012-01-01',
        'jenis_kelamin' => 'Laki-laki',
        'kd_agama' => 1,
        'alamat' => 'Alamat test',
        'status_siswa' => 'aktif',
    ]))->toThrow(QueryException::class);
});

it('rejects duplicate nisn on siswa', function () {
    $existing = Siswa::first();

    expect(fn () => Siswa::create([
        'nis' => '9999999999',
        'nisn' => $existing->nisn, // Duplicate
        'nik' => '9999999999999999',
        'nm_siswa' => 'Duplicate NISN Student',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2012-01-01',
        'jenis_kelamin' => 'Laki-laki',
        'kd_agama' => 1,
        'alamat' => 'Alamat test',
        'status_siswa' => 'aktif',
    ]))->toThrow(QueryException::class);
});

it('rejects duplicate nik on siswa', function () {
    $existing = Siswa::first();

    expect(fn () => Siswa::create([
        'nis' => '9999999999',
        'nisn' => '9999999999',
        'nik' => $existing->nik, // Duplicate
        'nm_siswa' => 'Duplicate NIK Student',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2012-01-01',
        'jenis_kelamin' => 'Laki-laki',
        'kd_agama' => 1,
        'alamat' => 'Alamat test',
        'status_siswa' => 'aktif',
    ]))->toThrow(QueryException::class);
});

it('rejects duplicate no_pendaftaran on pendaftaran', function () {
    $existing = Pendaftaran::first();
    $siswa = Siswa::where('status_siswa', 'aktif')->orderByDesc('kd_siswa')->first();

    expect(fn () => Pendaftaran::create([
        'kd_siswa' => $siswa->kd_siswa,
        'no_pendaftaran' => $existing->no_pendaftaran, // Duplicate
        'tgl_daftar' => '2026-06-20',
        'jalur' => 'Zonasi',
        'asal_sekolah' => 'SD Negeri 1 Wonosobo',
    ]))->toThrow(QueryException::class);
});

it('enforces 1:1 relationship between siswa and keluarga', function () {
    $existingKeluarga = Keluarga::first();

    expect(fn () => Keluarga::create([
        'kd_siswa' => $existingKeluarga->kd_siswa, // Duplicate kd_siswa
        'no_kk' => '3307999999999999',
        'anak_ke' => 1,
        'jumlah_saudara' => 1,
    ]))->toThrow(QueryException::class);
});

it('enforces 1:1 relationship between kelulusan and alumni', function () {
    $existingAlumni = Alumni::first();

    expect(fn () => Alumni::create([
        'kd_lulus' => $existingAlumni->kd_lulus, // Duplicate kd_lulus
        'kegiatan_lanjutan' => 'Bekerja',
        'instansi' => 'Wiraswasta',
    ]))->toThrow(QueryException::class);
});
