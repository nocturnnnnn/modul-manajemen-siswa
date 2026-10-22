<?php

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

it('can view list of tahun ajaran', function () {
    $response = $this->get(route('master.tahun-ajaran.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Master/TahunAjaran')
        ->has('tahunAjaranList')
    );
});

it('fails validation when format nm_tahun is invalid', function () {
    $response = $this->post(route('master.tahun-ajaran.store'), [
        'nm_tahun' => '2026-2027', // Wrong delimiter, must be 2026/2027
        'semester' => 'Ganjil',
    ]);

    $response->assertSessionHasErrors(['nm_tahun']);
});

it('creates a new tahun ajaran successfully', function () {
    $response = $this->post(route('master.tahun-ajaran.store'), [
        'nm_tahun' => '2027/2028',
        'semester' => 'Ganjil',
        'aktif' => false,
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('tahun_ajaran', [
        'nm_tahun' => '2027/2028',
        'semester' => 'Ganjil',
        'aktif' => false,
    ]);
});

it('ensures only one active academic year exists when a new active year is created', function () {
    $response = $this->post(route('master.tahun-ajaran.store'), [
        'nm_tahun' => '2027/2028',
        'semester' => 'Genap',
        'aktif' => true,
    ]);

    $response->assertSessionHas('success');

    // Exactly one active year should exist
    expect(TahunAjaran::where('aktif', true)->count())->toBe(1);
    expect(TahunAjaran::where('nm_tahun', '2027/2028')->where('semester', 'Genap')->first()->aktif)->toBeTrue();
});

it('can activate an academic year and automatically deactivates other active years', function () {
    $inactive = TahunAjaran::where('aktif', false)->first();

    $response = $this->post(route('master.tahun-ajaran.activate', $inactive->kd_tahun));

    $response->assertSessionHas('success');

    expect(TahunAjaran::where('aktif', true)->count())->toBe(1);
    expect($inactive->fresh()->aktif)->toBeTrue();
});

it('prevents deleting an active academic year', function () {
    $active = TahunAjaran::where('aktif', true)->first();

    $response = $this->delete(route('master.tahun-ajaran.destroy', $active->kd_tahun));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('tahun_ajaran', [
        'kd_tahun' => $active->kd_tahun,
    ]);
});
