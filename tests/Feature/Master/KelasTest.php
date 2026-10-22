<?php

use App\Models\Kelas;
use App\Models\KenaikanKelas;
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

it('can view list of rombel kelas', function () {
    $response = $this->get(route('master.kelas.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Master/Kelas')
        ->has('kelasList')
    );
});

it('fails validation when creating kelas with empty name or invalid tingkat', function () {
    $response = $this->post(route('master.kelas.store'), [
        'nm_kelas' => '',
        'tingkat' => 10, // Invalid, only 7, 8, 9
    ]);

    $response->assertSessionHasErrors(['nm_kelas', 'tingkat']);
});

it('fails validation when creating kelas with duplicate name', function () {
    $existing = Kelas::first();

    $response = $this->post(route('master.kelas.store'), [
        'nm_kelas' => $existing->nm_kelas,
        'tingkat' => 7,
    ]);

    $response->assertSessionHasErrors(['nm_kelas']);
});

it('creates a new kelas successfully', function () {
    $response = $this->post(route('master.kelas.store'), [
        'nm_kelas' => '7Z',
        'tingkat' => 7,
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('kelas', [
        'nm_kelas' => '7Z',
        'tingkat' => 7,
    ]);
});

it('updates an existing kelas successfully', function () {
    $kelas = Kelas::where('nm_kelas', '7A')->first();

    $response = $this->put(route('master.kelas.update', $kelas->kd_kelas), [
        'nm_kelas' => '7-Unggulan',
        'tingkat' => 7,
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('kelas', [
        'kd_kelas' => $kelas->kd_kelas,
        'nm_kelas' => '7-Unggulan',
    ]);
});

it('deletes an unused kelas successfully', function () {
    $kelas = Kelas::create([
        'nm_kelas' => '9X',
        'tingkat' => 9,
    ]);

    $response = $this->delete(route('master.kelas.destroy', $kelas->kd_kelas));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('kelas', [
        'kd_kelas' => $kelas->kd_kelas,
    ]);
});

it('prevents deleting kelas that is referenced in academic kenaikan_kelas', function () {
    $kenaikan = KenaikanKelas::first();
    $kelasAsal = $kenaikan->kelasAsal;

    $response = $this->delete(route('master.kelas.destroy', $kelasAsal->kd_kelas));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('kelas', [
        'kd_kelas' => $kelasAsal->kd_kelas,
    ]);
});
