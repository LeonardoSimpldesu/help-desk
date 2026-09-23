<?php

use App\Models\Client;
use App\Models\User;

it('redirects a guest to the admin login page', function () {
    $response = $this->get('/admin');

    $response->assertRedirectToRoute('filament.admin.auth.login');
});

it('renders the dashboard for staff', function (string $role) {
    $staff = User::factory()->{$role}()->create();

    $response = $this->actingAs($staff)->get('/admin');

    $response->assertOk();
})->with([
    'admin' => 'admin',
    'technician' => 'technician',
]);

it('forbids a client from accessing the admin panel', function () {
    $client = Client::factory()->create();

    $response = $this->actingAs($client)->get('/admin');

    $response->assertForbidden();
});
