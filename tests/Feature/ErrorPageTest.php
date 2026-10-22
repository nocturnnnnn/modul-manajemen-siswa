<?php

use Inertia\Testing\AssertableInertia as Assert;

test('renders 404 error page for non-existent routes as guest', function () {
    $response = $this->get('/non-existent-random-route-path');

    $response->assertNotFound();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Error')
        ->where('status', 404)
        ->where('auth.user', null)
    );
});

test('renders 404 error page for non-existent routes when authenticated', function () {
    $user = $this->loginAsAdmin();

    $response = $this->get('/non-existent-random-route-path');

    $response->assertNotFound();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Error')
        ->where('status', 404)
        ->where('auth.user.id', $user->id)
    );
});

test('renders 403 error page when non-superadmin accesses forbidden admin routes', function () {
    $admin = $this->loginAsAdmin();

    $response = $this->get('/admin/users');

    $response->assertForbidden();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Error')
        ->where('status', 403)
        ->where('auth.user.id', $admin->id)
    );
});

test('renders error preview page for status codes 500, 503, 505, and 419 as guest', function () {
    foreach ([500, 503, 505, 419] as $status) {
        $response = $this->get("/errors/{$status}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Error')
            ->where('status', $status)
            ->where('auth.user', null)
        );
    }
});

test('renders error preview page for status codes 500, 503, 505, and 419 when authenticated', function () {
    $user = $this->loginAsAdmin();

    foreach ([500, 503, 505, 419] as $status) {
        $response = $this->get("/errors/{$status}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Error')
            ->where('status', $status)
            ->where('auth.user.id', $user->id)
        );
    }
});
