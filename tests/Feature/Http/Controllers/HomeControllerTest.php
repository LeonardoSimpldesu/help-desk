<?php

use App\Models\Client;
use App\Models\User;

it('redirects a guest to the login page', function () {
    $response = $this->get('/');

    $response->assertRedirectToRoute('login');
});

it('redirects a client to the portal', function () {
    $client = Client::factory()->create();

    $response = $this->actingAs($client)->get('/');

    $response->assertRedirectToRoute('portal.index');
});

it('redirects staff to the admin dashboard', function (string $role) {
    $staff = User::factory()->{$role}()->create();

    $response = $this->actingAs($staff)->get('/');

    $response->assertRedirectToRoute('filament.admin.pages.dashboard');
})->with([
    'admin' => 'admin',
    'technician' => 'technician',
]);

it('signs out a user without a role and explains why', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirectToRoute('login');
    $response->assertSessionHasErrors(['email' => 'Sua conta não possui acesso ao sistema.']);
    $this->assertGuest();
});
