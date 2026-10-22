<?php

use App\Models\Agama;
use App\Models\Alumni;
use App\Models\DokumenSiswa;
use App\Models\JenisDokumen;
use App\Models\Kelas;
use App\Models\Keluarga;
use App\Models\Kelulusan;
use App\Models\KenaikanKelas;
use App\Models\KontakSiswa;
use App\Models\Mutasi;
use App\Models\OrangTuaWali;
use App\Models\PindahSekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Database\Seeders\AgamaSeeder;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\SiswaSampleSeeder;
use Database\Seeders\TahunAjaranSeeder;
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

it('asserts siswa belongs to agama and agama has many siswa', function () {
    $siswa = Siswa::first();
    expect($siswa->agama)->toBeInstanceOf(Agama::class);
    expect($siswa->agama->siswa->pluck('kd_siswa'))->toContain($siswa->kd_siswa);
});

it('asserts siswa has one keluarga', function () {
    $siswa = Siswa::where('nis', '202607001')->first();
    expect($siswa->keluarga)->toBeInstanceOf(Keluarga::class);
    expect($siswa->keluarga->siswa->kd_siswa)->toBe($siswa->kd_siswa);
});

it('asserts siswa has many orang tua wali', function () {
    $siswa = Siswa::where('nis', '202607001')->first();
    expect($siswa->orangTuaWali)->not->toBeEmpty();
    expect($siswa->orangTuaWali->first())->toBeInstanceOf(OrangTuaWali::class);
});

it('asserts siswa has many kontak', function () {
    $siswa = Siswa::where('nis', '202607001')->first();
    expect($siswa->kontak)->not->toBeEmpty();
    expect($siswa->kontak->first())->toBeInstanceOf(KontakSiswa::class);
});

it('asserts siswa has many dokumen and dokumen belongs to jenis_dokumen', function () {
    $siswa = Siswa::where('nis', '202607001')->first();
    expect($siswa->dokumen)->not->toBeEmpty();
    $dokumen = $siswa->dokumen->first();
    expect($dokumen)->toBeInstanceOf(DokumenSiswa::class);
    expect($dokumen->jenis)->toBeInstanceOf(JenisDokumen::class);
});

it('asserts kenaikan kelas belongs to siswa, tahun ajaran, kelas asal, and kelas tujuan', function () {
    $kenaikan = KenaikanKelas::first();
    expect($kenaikan)->not->toBeNull();
    expect($kenaikan->siswa)->toBeInstanceOf(Siswa::class);
    expect($kenaikan->tahunAjaran)->toBeInstanceOf(TahunAjaran::class);
    expect($kenaikan->kelasAsal)->toBeInstanceOf(Kelas::class);
    expect($kenaikan->kelasTujuan)->toBeInstanceOf(Kelas::class);
});

it('asserts kelulusan belongs to siswa and has one alumni', function () {
    $kelulusan = Kelulusan::first();
    expect($kelulusan)->not->toBeNull();
    expect($kelulusan->siswa)->toBeInstanceOf(Siswa::class);
    expect($kelulusan->alumni)->toBeInstanceOf(Alumni::class);
    expect($kelulusan->alumni->kelulusan->kd_lulus)->toBe($kelulusan->kd_lulus);
});

it('asserts siswa has pindah sekolah and mutasi history', function () {
    $siswaPindah = Siswa::where('status_siswa', 'pindah')->first();
    expect($siswaPindah)->not->toBeNull();
    expect($siswaPindah->pindahSekolah)->not->toBeEmpty();
    expect($siswaPindah->mutasi)->not->toBeEmpty();
    expect($siswaPindah->pindahSekolah->first())->toBeInstanceOf(PindahSekolah::class);
    expect($siswaPindah->mutasi->first())->toBeInstanceOf(Mutasi::class);
});
