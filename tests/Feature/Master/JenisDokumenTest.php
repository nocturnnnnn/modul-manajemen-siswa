<?php

use App\Models\DokumenSiswa;
use App\Models\JenisDokumen;
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

it('can view list of jenis dokumen', function () {
    $response = $this->get(route('master.jenis-dokumen.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Master/JenisDokumen')
        ->has('jenisDokumenList')
    );
});

it('validates uniqueness of kode jenis dokumen', function () {
    $existing = JenisDokumen::where('kode', 'KK')->first();

    $response = $this->post(route('master.jenis-dokumen.store'), [
        'kode' => $existing->kode,
        'nm_jenis' => 'Kartu Keluarga Duplikat',
        'wajib' => true,
    ]);

    $response->assertSessionHasErrors(['kode']);
});

it('creates a new jenis dokumen successfully', function () {
    $response = $this->post(route('master.jenis-dokumen.store'), [
        'kode' => 'RAPOR',
        'nm_jenis' => 'Buku Rapor SD Asal',
        'wajib' => false,
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('jenis_dokumen', [
        'kode' => 'RAPOR',
        'nm_jenis' => 'Buku Rapor SD Asal',
        'wajib' => false,
    ]);
});

it('updates an existing jenis dokumen successfully', function () {
    $jenis = JenisDokumen::where('kode', 'LAINNYA')->first();

    $response = $this->put(route('master.jenis-dokumen.update', $jenis->kd_jenis), [
        'kode' => 'LAINNYA',
        'nm_jenis' => 'Dokumen Penunjang Lainnya',
        'wajib' => false,
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('jenis_dokumen', [
        'kd_jenis' => $jenis->kd_jenis,
        'nm_jenis' => 'Dokumen Penunjang Lainnya',
    ]);
});

it('prevents deleting jenis dokumen that is in use by uploaded student documents', function () {
    $dokumenSiswa = DokumenSiswa::first();
    $jenisInUse = $dokumenSiswa->jenis;

    $response = $this->delete(route('master.jenis-dokumen.destroy', $jenisInUse->kd_jenis));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('jenis_dokumen', [
        'kd_jenis' => $jenisInUse->kd_jenis,
    ]);
});

it('deletes an unused jenis dokumen successfully', function () {
    $newJenis = JenisDokumen::create([
        'kode' => 'SERTIFIKAT',
        'nm_jenis' => 'Sertifikat Lomba',
        'wajib' => false,
    ]);

    $response = $this->delete(route('master.jenis-dokumen.destroy', $newJenis->kd_jenis));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('jenis_dokumen', [
        'kd_jenis' => $newJenis->kd_jenis,
    ]);
});
