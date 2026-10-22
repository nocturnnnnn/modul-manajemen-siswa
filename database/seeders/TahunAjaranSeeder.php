<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kd_tahun' => 1, 'nm_tahun' => '2025/2026', 'semester' => 'Ganjil', 'aktif' => false],
            ['kd_tahun' => 2, 'nm_tahun' => '2025/2026', 'semester' => 'Genap', 'aktif' => false],
            ['kd_tahun' => 3, 'nm_tahun' => '2026/2027', 'semester' => 'Ganjil', 'aktif' => true],
            ['kd_tahun' => 4, 'nm_tahun' => '2026/2027', 'semester' => 'Genap', 'aktif' => false],
        ];

        foreach ($data as $item) {
            TahunAjaran::updateOrCreate(['kd_tahun' => $item['kd_tahun']], $item);
        }
    }
}
