<?php

use App\Models\Client;
use App\Models\User;

it('redirects a guest to the login page', function () {
    $response = $this->get(route('portal.index'));

    $response->assertRedirectToRoute('login');
});

it('welcomes the authenticated client by name', function () {
    $client = Client::factory()->create(['name' => 'Maria Souza']);

    $response = $this->actingAs($client)->get(route('portal.index'));

    $response->assertSeeText('Bem-vindo, Maria Souza');
});

it('forbids staff from accessing the portal', function (string $role) {
    $staff = User::factory()->{$role}()->create();

    $response = $this->actingAs($staff)->get(route('portal.index'));

    $response->assertForbidden();
})->with([
    'admin' => 'admin',
    'technician' => 'technician',
]);

it('forbids a user without any role from accessing the portal', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('portal.index'));

    $response->assertForbidden();
});

it('escapes the client name', function () {
    $client = Client::factory()->create(['name' => "O'Reilly <script>alert('xss')</script>"]);

    $response = $this->actingAs($client)->get(route('portal.index'));

    $response->assertSee('&lt;script&gt;', escape: false);
    $response->assertDontSee("<script>alert('xss')</script>", escape: false);
});
