<?php

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

it('can view list of siswa with pagination', function () {
    $response = $this->get(route('siswa.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Siswa/Index')
        ->has('siswaList.data')
        ->has('agamaList')
    );
});

it('filters siswa by status aktif, mutasi, and pindah', function () {
    $response = $this->get(route('siswa.index', ['status' => 'pindah']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Siswa/Index')
        ->where('filters.status', 'pindah')
        ->has('siswaList.data', fn (Assert $data) => $data
            ->each(fn (Assert $item) => $item->where('status_siswa', 'pindah')->etc())
        )
    );
});

it('searches siswa by name or nis', function () {
    $targetSiswa = Siswa::first();

    $response = $this->get(route('siswa.index', ['search' => $targetSiswa->nis]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Siswa/Index')
        ->where('filters.search', $targetSiswa->nis)
        ->has('siswaList.data', 1)
        ->where('siswaList.data.0.nis', $targetSiswa->nis)
    );
});

it('filters siswa by agama', function () {
    $response = $this->get(route('siswa.index', ['agama' => 1]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Siswa/Index')
        ->where('filters.agama', '1')
    );
});
