<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

it('requires a unique username in database', function () {
    User::factory()->create([
        'username' => 'petugas_tu',
    ]);

    expect(fn () => User::factory()->create([
        'username' => 'petugas_tu',
    ]))->toThrow(QueryException::class);
});

it('seeds default admin with proper username and hashed password', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::where('username', 'admin')->first();

    expect($admin)->not->toBeNull();
    expect($admin->username)->toBe('admin');
    expect($admin->email)->toBe('admin@smpkristen1wonosobo.sch.id');
    expect(Hash::check('password123', $admin->password))->toBeTrue();
});
