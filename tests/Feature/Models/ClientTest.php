<?php

use App\Models\Client;
use App\Models\User;

it('assigns the client role when created', function () {
    $client = Client::create([
        'name' => 'Maria Souza',
        'email' => 'maria@example.com',
        'password' => 'secret-password',
    ]);

    expect($client->getRoleNames()->all())->toBe(['client']);
});

it('stores clients in the users table', function () {
    $client = Client::factory()->create();

    $this->assertDatabaseHas('users', ['id' => $client->id, 'email' => $client->email]);
});

it('only queries users with the client role', function () {
    $client = Client::factory()->create();
    $admin = User::factory()->admin()->create();
    $technician = User::factory()->technician()->create();
    $userWithoutRole = User::factory()->create();

    $ids = Client::query()->pluck('id')->all();

    expect($ids)->toBe([$client->id])
        ->and(Client::find($admin->id))->toBeNull()
        ->and(Client::find($technician->id))->toBeNull()
        ->and(Client::find($userWithoutRole->id))->toBeNull();
});

it('records roles under the User morph class so both models share them', function () {
    $client = Client::factory()->create();

    $this->assertDatabaseHas('model_has_roles', [
        'model_id' => $client->id,
        'model_type' => User::class,
    ]);
    expect(User::find($client->id)->hasRole('client'))->toBeTrue();
});
