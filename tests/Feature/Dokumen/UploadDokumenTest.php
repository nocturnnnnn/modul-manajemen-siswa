<?php

use App\Models\DokumenSiswa;
use App\Models\JenisDokumen;
use App\Models\Siswa;
use Database\Seeders\AgamaSeeder;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\SiswaSampleSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

it('allows valid pdf or image document upload', function () {
    Storage::fake('public');

    $siswa = Siswa::first();
    $jenis = JenisDokumen::where('kode', 'KK')->first();

    $file = UploadedFile::fake()->create('kartu_keluarga.pdf', 500, 'application/pdf');

    $response = $this->post(route('dokumen.upload'), [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_jenis' => $jenis->kd_jenis,
        'nomor_dokumen' => 'KK-3307011234567890',
        'file' => $file,
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('dokumen_siswa', [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_jenis' => $jenis->kd_jenis,
        'nomor_dokumen' => 'KK-3307011234567890',
        'status_verifikasi' => 'belum',
    ]);

    $dokumen = DokumenSiswa::latest('kd_dokumen')->first();
    Storage::disk('public')->assertExists($dokumen->file_path);
});

it('rejects unsupported file formats like exe or docx', function () {
    Storage::fake('public');

    $siswa = Siswa::first();
    $jenis = JenisDokumen::first();

    $badFile = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');

    $response = $this->post(route('dokumen.upload'), [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_jenis' => $jenis->kd_jenis,
        'nomor_dokumen' => 'DOC-001',
        'file' => $badFile,
    ]);

    $response->assertSessionHasErrors('file');
});

it('associates uploaded document with correct siswa and jenis_dokumen', function () {
    Storage::fake('public');

    $siswa = Siswa::first();
    $jenis = JenisDokumen::where('kode', 'AKTA')->first();

    $file = UploadedFile::fake()->create('akta_kelahiran.jpg', 200, 'image/jpeg');

    $response = $this->post(route('dokumen.upload'), [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_jenis' => $jenis->kd_jenis,
        'nomor_dokumen' => 'AKTA-2012-001',
        'file' => $file,
    ]);

    $response->assertSessionHasNoErrors();

    $dokumen = DokumenSiswa::where('nomor_dokumen', 'AKTA-2012-001')->first();
    expect($dokumen)->not->toBeNull();
    expect($dokumen->siswa->kd_siswa)->toBe($siswa->kd_siswa);
    expect($dokumen->jenis->kd_jenis)->toBe($jenis->kd_jenis);
    expect($dokumen->status_verifikasi)->toBe('belum');
});

it('validates file size does not exceed 3MB', function () {
    Storage::fake('public');

    $siswa = Siswa::first();
    $jenis = JenisDokumen::first();

    // 4000 KB (> 3072 KB)
    $oversizedFile = UploadedFile::fake()->create('dokumen_besar.pdf', 4000, 'application/pdf');

    $response = $this->post(route('dokumen.upload'), [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_jenis' => $jenis->kd_jenis,
        'nomor_dokumen' => 'DOC-BESAR',
        'file' => $oversizedFile,
    ]);

    $response->assertSessionHasErrors('file');
});

it('validates required fields on upload', function () {
    $response = $this->post(route('dokumen.upload'), []);

    $response->assertSessionHasErrors(['kd_siswa', 'kd_jenis', 'file']);
});
