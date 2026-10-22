<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kd_kelas' => 1, 'nm_kelas' => '7A', 'tingkat' => 7],
            ['kd_kelas' => 2, 'nm_kelas' => '7B', 'tingkat' => 7],
            ['kd_kelas' => 3, 'nm_kelas' => '7C', 'tingkat' => 7],
            ['kd_kelas' => 4, 'nm_kelas' => '8A', 'tingkat' => 8],
            ['kd_kelas' => 5, 'nm_kelas' => '8B', 'tingkat' => 8],
            ['kd_kelas' => 6, 'nm_kelas' => '8C', 'tingkat' => 8],
            ['kd_kelas' => 7, 'nm_kelas' => '9A', 'tingkat' => 9],
            ['kd_kelas' => 8, 'nm_kelas' => '9B', 'tingkat' => 9],
            ['kd_kelas' => 9, 'nm_kelas' => '9C', 'tingkat' => 9],
        ];

        foreach ($data as $item) {
            Kelas::updateOrCreate(['kd_kelas' => $item['kd_kelas']], $item);
        }
    }
}
