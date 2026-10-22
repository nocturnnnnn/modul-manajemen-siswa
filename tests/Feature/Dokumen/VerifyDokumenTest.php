<?php

use App\Models\DokumenSiswa;
use Database\Seeders\AgamaSeeder;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\SiswaSampleSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

it('can view dokumen index page with statistics and paginated list', function () {
    $response = $this->get(route('dokumen.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dokumen/Index')
        ->has('dokumenList.data')
        ->has('jenisDokumenList')
        ->has('stats')
        ->has('filters')
    );
});

it('allows admin to approve document and sets status to terverifikasi', function () {
    $dokumen = DokumenSiswa::where('status_verifikasi', 'belum')->first();
    if (! $dokumen) {
        $dokumen = DokumenSiswa::first();
    }

    $response = $this->put(route('dokumen.verify', $dokumen->kd_dokumen), [
        'status_verifikasi' => 'terverifikasi',
        'catatan_verifikasi' => 'Dokumen lengkap dan sah.',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $dokumen->refresh();
    expect($dokumen->status_verifikasi)->toBe('terverifikasi');
    expect($dokumen->catatan_verifikasi)->toBe('Dokumen lengkap dan sah.');
});

it('allows admin to reject document with rejection reason', function () {
    $dokumen = DokumenSiswa::first();

    $response = $this->put(route('dokumen.verify', $dokumen->kd_dokumen), [
        'status_verifikasi' => 'ditolak',
        'catatan_verifikasi' => 'Foto dokumen buram, mohon pindai ulang.',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $dokumen->refresh();
    expect($dokumen->status_verifikasi)->toBe('ditolak');
    expect($dokumen->catatan_verifikasi)->toBe('Foto dokumen buram, mohon pindai ulang.');
});

it('requires rejection note when status is ditolak', function () {
    $dokumen = DokumenSiswa::first();

    $response = $this->put(route('dokumen.verify', $dokumen->kd_dokumen), [
        'status_verifikasi' => 'ditolak',
        'catatan_verifikasi' => '',
    ]);

    $response->assertSessionHasErrors('catatan_verifikasi');
});

it('can delete uploaded document and its physical file', function () {
    Storage::fake('public');

    // Create a dummy physical file
    $filePath = 'dokumen_siswa/sample_to_delete.pdf';
    Storage::disk('public')->put($filePath, 'sample content');

    $dokumen = DokumenSiswa::first();
    $dokumen->update(['file_path' => $filePath]);

    $response = $this->delete(route('dokumen.destroy', $dokumen->kd_dokumen));

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseMissing('dokumen_siswa', [
        'kd_dokumen' => $dokumen->kd_dokumen,
    ]);

    Storage::disk('public')->assertMissing($filePath);
});
