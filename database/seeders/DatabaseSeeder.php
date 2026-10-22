<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super Administrator Default
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator SMP Kristen 1',
                'role' => 'superadmin',
                'email' => 'superadmin@smpkristen1wonosobo.sch.id',
                'password' => Hash::make('password123'),
            ]
        );

        // Admin Tata Usaha Default
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Tata Usaha SMP Kristen 1',
                'role' => 'admin',
                'email' => 'admin@smpkristen1wonosobo.sch.id',
                'password' => Hash::make('password123'),
            ]
        );

        $this->call([
            AgamaSeeder::class,
            JenisDokumenSeeder::class,
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            SiswaSampleSeeder::class,
        ]);
    }
}
