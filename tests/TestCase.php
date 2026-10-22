<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected ?User $defaultUser = null;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->shouldAuthenticateByDefault()) {
            $this->loginAsAdmin();
        }
    }

    /**
     * Authenticate a test user as admin.
     */
    public function loginAsAdmin(?User $user = null): User
    {
        $this->defaultUser = $user ?? User::factory()->create([
            'username' => 'admin_test_'.uniqid(),
            'role' => 'admin',
        ]);

        $this->actingAs($this->defaultUser);

        return $this->defaultUser;
    }

    /**
     * Authenticate a test user as superadmin.
     */
    public function loginAsSuperadmin(?User $user = null): User
    {
        $this->defaultUser = $user ?? User::factory()->superadmin()->create([
            'username' => 'superadmin_test_'.uniqid(),
        ]);

        $this->actingAs($this->defaultUser);

        return $this->defaultUser;
    }

    /**
     * Determine whether tests should authenticate an admin user by default.
     */
    protected function shouldAuthenticateByDefault(): bool
    {
        $className = static::class;

        // Skip default authentication for Auth, Profile, Admin, Error feature tests and Unit tests
        return ! str_contains($className, 'Auth')
            && ! str_contains($className, 'Profile')
            && ! str_contains($className, 'Admin')
            && ! str_contains($className, 'Error')
            && ! str_contains($className, 'Unit');
    }
}
