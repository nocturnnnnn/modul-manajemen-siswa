<?php

use App\Models\Agama;
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

it('can view list of agama', function () {
    $response = $this->get(route('master.agama.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Master/Agama')
        ->has('agamaList')
    );
});

it('fails validation on duplicate agama name', function () {
    $existing = Agama::first();

    $response = $this->post(route('master.agama.store'), [
        'nm_agama' => $existing->nm_agama,
    ]);

    $response->assertSessionHasErrors(['nm_agama']);
});

it('creates a new agama successfully', function () {
    $response = $this->post(route('master.agama.store'), [
        'nm_agama' => 'Kepercayaan Lainnya',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('agama', [
        'nm_agama' => 'Kepercayaan Lainnya',
    ]);
});

it('updates an existing agama successfully', function () {
    $agama = Agama::where('nm_agama', 'Kristen')->first();

    $response = $this->put(route('master.agama.update', $agama->kd_agama), [
        'nm_agama' => 'Kristen Protestan',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('agama', [
        'kd_agama' => $agama->kd_agama,
        'nm_agama' => 'Kristen Protestan',
    ]);
});

it('prevents deleting agama that has associated students', function () {
    $siswa = Siswa::first();
    $agamaInUse = $siswa->agama;

    $response = $this->delete(route('master.agama.destroy', $agamaInUse->kd_agama));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('agama', [
        'kd_agama' => $agamaInUse->kd_agama,
    ]);
});

it('deletes an unused agama successfully', function () {
    $unusedAgama = Agama::create([
        'nm_agama' => 'Agama Baru Dummy',
    ]);

    $response = $this->delete(route('master.agama.destroy', $unusedAgama->kd_agama));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('agama', [
        'kd_agama' => $unusedAgama->kd_agama,
    ]);
});
