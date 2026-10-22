<?php

namespace Database\Seeders;

use App\Models\JenisDokumen;
use Illuminate\Database\Seeder;

class JenisDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kd_jenis' => 1, 'kode' => 'KK', 'nm_jenis' => 'Kartu Keluarga', 'wajib' => true],
            ['kd_jenis' => 2, 'kode' => 'AKTA', 'nm_jenis' => 'Akta Kelahiran', 'wajib' => true],
            ['kd_jenis' => 3, 'kode' => 'KIP', 'nm_jenis' => 'Kartu Indonesia Pintar', 'wajib' => false],
            ['kd_jenis' => 4, 'kode' => 'KKS', 'nm_jenis' => 'Kartu Keluarga Sejahtera', 'wajib' => false],
            ['kd_jenis' => 5, 'kode' => 'LAINNYA', 'nm_jenis' => 'Ijazah / Dokumen Lainnya', 'wajib' => false],
        ];

        foreach ($data as $item) {
            JenisDokumen::updateOrCreate(['kd_jenis' => $item['kd_jenis']], $item);
        }
    }
}
