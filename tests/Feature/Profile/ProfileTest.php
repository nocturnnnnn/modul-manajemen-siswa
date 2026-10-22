<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('guest cannot access profile page', function () {
    $response = $this->get('/profile');

    $response->assertRedirect('/login');
});

test('authenticated user can view profile edit page', function () {
    $user = $this->loginAsAdmin();

    $response = $this->get('/profile');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile/Edit')
        ->has('user')
        ->where('user.id', $user->id)
        ->where('user.username', $user->username)
    );
});

test('user can update their name, username, and email', function () {
    $user = $this->loginAsAdmin();

    $response = $this->patch('/profile', [
        'name' => 'Nama Baru User',
        'username' => 'user_baru',
        'email' => 'user_baru@sekolah.sch.id',
    ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->name)->toBe('Nama Baru User')
        ->and($user->username)->toBe('user_baru')
        ->and($user->email)->toBe('user_baru@sekolah.sch.id');
});

test('user cannot update or escalate their role via profile update', function () {
    $user = $this->loginAsAdmin();
    expect($user->role)->toBe('admin');

    $response = $this->patch('/profile', [
        'name' => $user->name,
        'username' => $user->username,
        'email' => $user->email,
        'role' => 'superadmin', // Attack: Attempt privilege escalation
    ]);

    $response->assertRedirect('/profile');

    $user->refresh();
    expect($user->role)->toBe('admin'); // Must strictly remain admin
});

test('user can update their password with valid current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
        'role' => 'admin',
    ]);
    $this->loginAsAdmin($user);

    $response = $this->put('/profile/password', [
        'current_password' => 'password123',
        'password' => 'passwordBaru123',
        'password_confirmation' => 'passwordBaru123',
    ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check('passwordBaru123', $user->password))->toBeTrue();
});

test('user cannot update password with incorrect current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
        'role' => 'admin',
    ]);
    $this->loginAsAdmin($user);

    $response = $this->put('/profile/password', [
        'current_password' => 'wrongpassword',
        'password' => 'passwordBaru123',
        'password_confirmation' => 'passwordBaru123',
    ]);

    $response->assertSessionHasErrors(['current_password']);

    $user->refresh();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});
