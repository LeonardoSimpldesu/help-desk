<?php

use App\Filament\Admin\Resources\Clients\ClientResource;
use App\Models\Client;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('shows the client details', function () {
    $client = Client::factory()->create([
        'name' => 'Maria Souza',
        'email' => 'maria@example.com',
        'phone' => '11987654321',
    ]);

    $response = $this->get(ClientResource::getUrl('view', ['record' => $client]));

    $response->assertSeeText(['Maria Souza', 'maria@example.com', '11987654321']);
});

it('returns 404 when viewing a user who is not a client', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->get(ClientResource::getUrl('view', ['record' => $admin]));

    $response->assertNotFound();
});
