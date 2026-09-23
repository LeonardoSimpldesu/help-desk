<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * A valid registration payload, optionally overridden.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function registrationPayload(array $overrides = []): array
{
    return [
        'name' => 'Maria Souza',
        'email' => 'maria@example.com',
        'phone' => '(11) 98765-4321',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
        ...$overrides,
    ];
}

it('renders the registration page for guests', function () {
    $response = $this->get(route('register'));

    $response->assertViewIs('auth.register');
});

it('registers a client, logs them in and redirects to the portal', function () {
    $response = $this->post(route('register'), registrationPayload());

    $response->assertRedirectToRoute('portal.index');
    $response->assertSessionHas('success', 'Cadastro realizado com sucesso!');

    $client = Client::query()->where('email', 'maria@example.com')->sole();
    expect($client->name)->toBe('Maria Souza')
        ->and($client->hasRole('client'))->toBeTrue()
        ->and(Hash::check('secret-password', $client->password))->toBeTrue();

    $this->assertAuthenticated();
    expect(auth()->id())->toBe($client->id);
});

it('stores the phone as digits only', function () {
    $this->post(route('register'), registrationPayload(['phone' => '(21) 3333-4444']));

    $this->assertDatabaseHas('users', [
        'email' => 'maria@example.com',
        'phone' => '2133334444',
    ]);
});

it('registers a client without a phone', function () {
    $response = $this->post(route('register'), registrationPayload(['phone' => '']));

    $response->assertRedirectToRoute('portal.index');
    $this->assertDatabaseHas('users', [
        'email' => 'maria@example.com',
        'phone' => null,
    ]);
});

it('rejects an empty payload', function () {
    $response = $this->post(route('register'), []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
    $response->assertSessionDoesntHaveErrors('phone');
    $this->assertDatabaseCount('users', 0);
    $this->assertGuest();
});

it('rejects an invalid phone with the user-facing message', function () {
    $response = $this->post(route('register'), registrationPayload(['phone' => '(11) 8765-43210']));

    $response->assertSessionHasErrors(['phone' => 'Informe um telefone válido com DDD (fixo ou celular).']);
    $this->assertDatabaseCount('users', 0);
    $this->assertGuest();
});

it('rejects an email that is already registered', function () {
    $existing = User::factory()->create(['email' => 'maria@example.com']);

    $response = $this->post(route('register'), registrationPayload());

    $response->assertSessionHasErrors('email');
    $this->assertDatabaseCount('users', 1);
    $this->assertModelExists($existing);
    $this->assertGuest();
});

it('rejects an invalid field value', function (array $overrides, string $field) {
    $response = $this->post(route('register'), registrationPayload($overrides));

    $response->assertSessionHasErrors($field);
    $this->assertDatabaseCount('users', 0);
    $this->assertGuest();
})->with([
    'email with uppercase letters' => [['email' => 'Maria@Example.com'], 'email'],
    'email with invalid format' => [['email' => 'not-an-email'], 'email'],
    'name longer than 255 characters' => [['name' => str_repeat('a', 256)], 'name'],
    'password shorter than 8 characters' => [['password' => 'short12', 'password_confirmation' => 'short12'], 'password'],
    'password confirmation mismatch' => [['password_confirmation' => 'different-password'], 'password'],
]);

it('ignores a role sent in the payload', function () {
    $this->post(route('register'), registrationPayload(['role' => 'admin', 'roles' => ['admin']]));

    $user = User::query()->where('email', 'maria@example.com')->sole();
    expect($user->getRoleNames()->all())->toBe(['client']);
});

it('redirects an authenticated user away from the registration page', function () {
    $client = Client::factory()->create();

    $response = $this->actingAs($client)->get(route('register'));

    $response->assertRedirect('/');
});
