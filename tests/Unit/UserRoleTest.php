<?php

use App\Models\User;

test('user defaults to admin role from factory', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe('admin')
        ->and($user->isAdmin())->toBeTrue()
        ->and($user->isSuperadmin())->toBeFalse();
});

test('user superadmin state creates superadmin role', function () {
    $user = User::factory()->superadmin()->create();

    expect($user->role)->toBe('superadmin')
        ->and($user->isSuperadmin())->toBeTrue()
        ->and($user->isAdmin())->toBeFalse();
});

test('role column is fillable on user model', function () {
    $user = new User;

    expect($user->getFillable())->toContain('role')
        ->and($user->getFillable())->toContain('username');
});
