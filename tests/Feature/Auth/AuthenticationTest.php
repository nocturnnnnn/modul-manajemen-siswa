<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the login page for guests', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Auth/Login')
    );
});

it('redirects unauthenticated users from root / to /login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

it('renders dashboard for authenticated users visiting root /', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
    );
});

it('redirects authenticated users from /login to dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('login'));

    $response->assertRedirect(route('dashboard'));
});

it('protects management routes from unauthenticated access', function () {
    $protectedRoutes = [
        route('dashboard'),
        route('siswa.index'),
        route('dokumen.index'),
        route('akademik.kenaikan-kelas.index'),
        route('mutasi.index'),
        route('kelulusan.index'),
        route('alumni.index'),
        route('laporan.index'),
        route('master.kelas.index'),
        route('master.agama.index'),
    ];

    foreach ($protectedRoutes as $url) {
        $response = $this->get($url);
        $response->assertRedirect(route('login'));
    }
});

it('authenticates users using valid username and password', function () {
    $user = User::factory()->create([
        'username' => 'staff_tu_1',
        'password' => Hash::make('rahasia123'),
    ]);

    $response = $this->post('/login', [
        'username' => 'staff_tu_1',
        'password' => 'rahasia123',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard'));
});

it('rejects invalid password', function () {
    User::factory()->create([
        'username' => 'staff_tu_2',
        'password' => Hash::make('rahasia123'),
    ]);

    $response = $this->post('/login', [
        'username' => 'staff_tu_2',
        'password' => 'password_salah',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('username');
});

it('rejects nonexistent username', function () {
    $response = $this->post('/login', [
        'username' => 'user_tidak_terdaftar',
        'password' => 'sembarang123',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('username');
});

it('validates required username and password fields', function () {
    $response = $this->post('/login', [
        'username' => '',
        'password' => '',
    ]);

    $response->assertSessionHasErrors(['username', 'password']);
});

it('throttles excessive failed login attempts', function () {
    $user = User::factory()->create([
        'username' => 'staff_tu_throttle',
        'password' => Hash::make('password_asli'),
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'username' => 'staff_tu_throttle',
            'password' => 'salah',
        ]);
    }

    $response = $this->post('/login', [
        'username' => 'staff_tu_throttle',
        'password' => 'salah',
    ]);

    $response->assertSessionHasErrors('username');
    $errors = session('errors')->get('username');
    expect(implode(' ', $errors))->toContain('Terlalu banyak percobaan');
});

it('logs user out and clears session', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});
