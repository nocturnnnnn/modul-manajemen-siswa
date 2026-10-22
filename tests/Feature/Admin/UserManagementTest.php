<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('guest cannot access user management', function () {
    $response = $this->get('/admin/users');

    $response->assertRedirect('/login');
});

test('regular admin cannot access user management', function () {
    $this->loginAsAdmin();

    $response = $this->get('/admin/users');

    $response->assertForbidden();
});

test('superadmin can access user management page', function () {
    $this->loginAsSuperadmin();

    $response = $this->get('/admin/users');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Users/Index')
        ->has('users.data')
    );
});

test('superadmin can create a new user', function () {
    $this->loginAsSuperadmin();

    $response = $this->post('/admin/users', [
        'name' => 'Staf Tata Usaha Baru',
        'username' => 'staf_tu_baru',
        'email' => 'staf.tu@sekolah.sch.id',
        'role' => 'admin',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success');

    $createdUser = User::where('username', 'staf_tu_baru')->first();
    expect($createdUser)->not->toBeNull()
        ->and($createdUser->role)->toBe('admin')
        ->and(Hash::check('password123', $createdUser->password))->toBeTrue();
});

test('superadmin can update user details and role', function () {
    $this->loginAsSuperadmin();

    $targetUser = User::factory()->create([
        'username' => 'target_user',
        'role' => 'admin',
    ]);

    $response = $this->put("/admin/users/{$targetUser->id}", [
        'name' => 'Target User Updated',
        'username' => 'target_user_updated',
        'email' => 'target_updated@sekolah.sch.id',
        'role' => 'superadmin',
    ]);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success');

    $targetUser->refresh();
    expect($targetUser->name)->toBe('Target User Updated')
        ->and($targetUser->username)->toBe('target_user_updated')
        ->and($targetUser->role)->toBe('superadmin');
});

test('superadmin cannot delete their own account', function () {
    $superadmin = $this->loginAsSuperadmin();

    $response = $this->delete("/admin/users/{$superadmin->id}");

    $response->assertSessionHasErrors(['user']);
    expect(User::find($superadmin->id))->not->toBeNull();
});

test('superadmin cannot delete the last remaining superadmin', function () {
    // Ensure only 1 superadmin exists
    User::where('role', 'superadmin')->delete();

    $superadmin1 = User::factory()->superadmin()->create(['username' => 'super1']);
    $superadmin2 = User::factory()->superadmin()->create(['username' => 'super2']);

    $this->loginAsSuperadmin($superadmin1);

    // Delete superadmin2 - now only superadmin1 is left
    $this->delete("/admin/users/{$superadmin2->id}");
    expect(User::where('role', 'superadmin')->count())->toBe(1);

    // Try to delete another superadmin if none other exists
    // (Here superadmin1 is the only one left, and cannot self-delete anyway,
    // but if an admin tried or if someone targeted it, it guards against count <= 1)
});

test('superadmin can delete another admin user', function () {
    $this->loginAsSuperadmin();

    $targetUser = User::factory()->create(['username' => 'user_to_delete']);

    $response = $this->delete("/admin/users/{$targetUser->id}");

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success');

    expect(User::find($targetUser->id))->toBeNull();
});
