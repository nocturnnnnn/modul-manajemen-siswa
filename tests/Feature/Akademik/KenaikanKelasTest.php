<?php

use App\Models\Kelas;
use App\Models\KenaikanKelas;
use App\Models\Siswa;
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

it('renders kenaikan kelas evaluation page with active academic year and classes', function () {
    $response = $this->get(route('akademik.kenaikan-kelas.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Akademik/KenaikanKelas')
        ->has('tahunAjaranList')
        ->has('selectedTahun')
        ->has('kelasList')
        ->has('selectedKelas')
        ->has('siswaList')
        ->has('riwayatEvaluasi')
    );
});

it('records naik kelas with target class of higher level', function () {
    $tahun = TahunAjaran::where('aktif', true)->first();
    $kelas7A = Kelas::where('nm_kelas', '7A')->first();
    $kelas8A = Kelas::where('nm_kelas', '8A')->first();
    $siswa = Siswa::where('status_siswa', 'aktif')->first();

    $response = $this->post(route('akademik.kenaikan-kelas.process'), [
        'kd_tahun' => $tahun->kd_tahun,
        'kd_kelas_asal' => $kelas7A->kd_kelas,
        'evaluasi' => [
            [
                'kd_siswa' => $siswa->kd_siswa,
                'status' => 'naik',
                'kd_kelas_tujuan' => $kelas8A->kd_kelas,
            ],
        ],
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('kenaikan_kelas', [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_tahun' => $tahun->kd_tahun,
        'kd_kelas_asal' => $kelas7A->kd_kelas,
        'kd_kelas_tujuan' => $kelas8A->kd_kelas,
        'status' => 'naik',
    ]);
});

it('records tinggal kelas with target class of same level', function () {
    $tahun = TahunAjaran::where('aktif', true)->first();
    $kelas7A = Kelas::where('nm_kelas', '7A')->first();
    $kelas7B = Kelas::where('nm_kelas', '7B')->first();
    $siswa = Siswa::where('status_siswa', 'aktif')->first();

    $response = $this->post(route('akademik.kenaikan-kelas.process'), [
        'kd_tahun' => $tahun->kd_tahun,
        'kd_kelas_asal' => $kelas7A->kd_kelas,
        'evaluasi' => [
            [
                'kd_siswa' => $siswa->kd_siswa,
                'status' => 'tinggal',
                'kd_kelas_tujuan' => $kelas7B->kd_kelas,
            ],
        ],
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->assertDatabaseHas('kenaikan_kelas', [
        'kd_siswa' => $siswa->kd_siswa,
        'kd_tahun' => $tahun->kd_tahun,
        'kd_kelas_asal' => $kelas7A->kd_kelas,
        'kd_kelas_tujuan' => $kelas7B->kd_kelas,
        'status' => 'tinggal',
    ]);
});

it('prevents naik kelas with invalid target class level', function () {
    $tahun = TahunAjaran::where('aktif', true)->first();
    $kelas7A = Kelas::where('nm_kelas', '7A')->first();
    $kelas9A = Kelas::where('nm_kelas', '9A')->first(); // Skip level 8
    $siswa = Siswa::where('status_siswa', 'aktif')->first();

    $response = $this->post(route('akademik.kenaikan-kelas.process'), [
        'kd_tahun' => $tahun->kd_tahun,
        'kd_kelas_asal' => $kelas7A->kd_kelas,
        'evaluasi' => [
            [
                'kd_siswa' => $siswa->kd_siswa,
                'status' => 'naik',
                'kd_kelas_tujuan' => $kelas9A->kd_kelas,
            ],
        ],
    ]);

    $response->assertSessionHasErrors('evaluasi.0.kd_kelas_tujuan');
});

it('prevents naik kelas for grade 9 students', function () {
    $tahun = TahunAjaran::where('aktif', true)->first();
    $kelas9A = Kelas::where('nm_kelas', '9A')->first();
    $kelas8A = Kelas::where('nm_kelas', '8A')->first();
    $siswa = Siswa::where('status_siswa', 'aktif')->first();

    $response = $this->post(route('akademik.kenaikan-kelas.process'), [
        'kd_tahun' => $tahun->kd_tahun,
        'kd_kelas_asal' => $kelas9A->kd_kelas,
        'evaluasi' => [
            [
                'kd_siswa' => $siswa->kd_siswa,
                'status' => 'naik',
                'kd_kelas_tujuan' => $kelas8A->kd_kelas,
            ],
        ],
    ]);

    $response->assertSessionHasErrors('evaluasi.0.status');
});

it('handles bulk evaluation for an entire class roster', function () {
    $tahun = TahunAjaran::where('aktif', true)->first();
    $kelas7A = Kelas::where('nm_kelas', '7A')->first();
    $kelas8A = Kelas::where('nm_kelas', '8A')->first();
    $kelas7B = Kelas::where('nm_kelas', '7B')->first();

    $siswas = Siswa::where('status_siswa', 'aktif')->take(2)->get();

    $response = $this->post(route('akademik.kenaikan-kelas.process'), [
        'kd_tahun' => $tahun->kd_tahun,
        'kd_kelas_asal' => $kelas7A->kd_kelas,
        'evaluasi' => [
            [
                'kd_siswa' => $siswas[0]->kd_siswa,
                'status' => 'naik',
                'kd_kelas_tujuan' => $kelas8A->kd_kelas,
            ],
            [
                'kd_siswa' => $siswas[1]->kd_siswa,
                'status' => 'tinggal',
                'kd_kelas_tujuan' => $kelas7B->kd_kelas,
            ],
        ],
    ]);

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('kenaikan_kelas', [
        'kd_siswa' => $siswas[0]->kd_siswa,
        'kd_tahun' => $tahun->kd_tahun,
        'status' => 'naik',
        'kd_kelas_tujuan' => $kelas8A->kd_kelas,
    ]);

    $this->assertDatabaseHas('kenaikan_kelas', [
        'kd_siswa' => $siswas[1]->kd_siswa,
        'kd_tahun' => $tahun->kd_tahun,
        'status' => 'tinggal',
        'kd_kelas_tujuan' => $kelas7B->kd_kelas,
    ]);
});

it('can delete or cancel a promotion record', function () {
    $kenaikan = KenaikanKelas::first();

    $response = $this->delete(route('akademik.kenaikan-kelas.destroy', $kenaikan->kd_kenaikan));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('kenaikan_kelas', [
        'kd_kenaikan' => $kenaikan->kd_kenaikan,
    ]);
});
