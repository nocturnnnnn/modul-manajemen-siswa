<?php

namespace Database\Seeders;

use App\Models\Agama;
use Illuminate\Database\Seeder;

class AgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kd_agama' => 1, 'nm_agama' => 'Kristen'],
            ['kd_agama' => 2, 'nm_agama' => 'Katolik'],
            ['kd_agama' => 3, 'nm_agama' => 'Islam'],
            ['kd_agama' => 4, 'nm_agama' => 'Hindu'],
            ['kd_agama' => 5, 'nm_agama' => 'Buddha'],
            ['kd_agama' => 6, 'nm_agama' => 'Konghucu'],
        ];

        foreach ($data as $item) {
            Agama::updateOrCreate(['kd_agama' => $item['kd_agama']], $item);
        }
    }
}
