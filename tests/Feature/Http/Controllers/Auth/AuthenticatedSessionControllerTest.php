<?php

use App\Models\Client;
use App\Models\User;

it('renders the login page for guests', function () {
    $response = $this->get(route('login'));

    $response->assertViewIs('auth.login');
});

it('redirects an authenticated user away from the login page', function () {
    $client = Client::factory()->create();

    $response = $this->actingAs($client)->get(route('login'));

    $response->assertRedirect('/');
});

it('logs a client in and redirects to the portal', function () {
    $client = Client::factory()->create();

    $response = $this->post(route('login'), [
        'email' => $client->email,
        'password' => 'password',
    ]);

    $response->assertRedirectToRoute('portal.index');
    $this->assertAuthenticatedAs($client);
});

it('logs staff in and redirects to the admin panel', function (string $role) {
    $staff = User::factory()->{$role}()->create();

    $response = $this->post(route('login'), [
        'email' => $staff->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/admin');
    $this->assertAuthenticatedAs($staff);
})->with([
    'admin' => 'admin',
    'technician' => 'technician',
]);

it('sets the remember cookie when remember me is checked', function () {
    $client = Client::factory()->create();

    $response = $this->post(route('login'), [
        'email' => $client->email,
        'password' => 'password',
        'remember' => 'on',
    ]);

    $response->assertCookie(auth()->guard()->getRecallerName());
});

it('rejects a wrong password with the invalid credentials message', function () {
    $client = Client::factory()->create();

    $response = $this->from(route('login'))->post(route('login'), [
        'email' => $client->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirectToRoute('login');
    $response->assertSessionHasErrors(['email' => 'Credenciais inválidas.']);
    $this->assertGuest();
});

it('rejects an empty payload', function () {
    $response = $this->post(route('login'), []);

    $response->assertSessionHasErrors(['email', 'password']);
    $this->assertGuest();
});

it('rejects an email with an invalid format', function () {
    $response = $this->post(route('login'), [
        'email' => 'not-an-email',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('logs the user out and redirects to the login page', function () {
    $client = Client::factory()->create();

    $response = $this->actingAs($client)->post(route('logout'));

    $response->assertRedirectToRoute('login');
    $this->assertGuest();
});

it('redirects a guest trying to log out to the login page', function () {
    $response = $this->post(route('logout'));

    $response->assertRedirectToRoute('login');
});
