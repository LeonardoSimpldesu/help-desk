<?php

use App\Filament\Admin\Resources\Clients\ClientResource;
use App\Filament\Admin\Resources\Clients\Pages\EditClient;
use App\Models\Client;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('fills the form with the client data', function () {
    $client = Client::factory()->create(['phone' => '11987654321']);

    Livewire::test(EditClient::class, ['record' => $client->id])
        ->assertSchemaStateSet([
            'name' => $client->name,
            'email' => $client->email,
            'phone' => '11987654321',
        ]);
});

it('updates the client and stores the phone as digits only', function () {
    $client = Client::factory()->create();

    Livewire::test(EditClient::class, ['record' => $client->id])
        ->fillForm([
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
            'phone' => '(21) 3333-4444',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $this->assertDatabaseHas('users', [
        'id' => $client->id,
        'name' => 'Maria Souza',
        'email' => 'maria@example.com',
        'phone' => '2133334444',
    ]);
});

it('allows clearing the phone', function () {
    $client = Client::factory()->create(['phone' => '11987654321']);

    Livewire::test(EditClient::class, ['record' => $client->id])
        ->fillForm(['phone' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('users', ['id' => $client->id, 'phone' => null]);
});

it('keeps the client email when saved unchanged', function () {
    $client = Client::factory()->create();

    Livewire::test(EditClient::class, ['record' => $client->id])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('rejects invalid form data', function (array $data, array $errors) {
    $client = Client::factory()->create(['name' => 'Original Name']);

    Livewire::test(EditClient::class, ['record' => $client->id])
        ->fillForm($data)
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNotNotified();

    $this->assertDatabaseHas('users', ['id' => $client->id, 'name' => 'Original Name']);
})->with([
    'name is required' => [['name' => null], ['name' => 'required']],
    'name is max 255 characters' => [['name' => str_repeat('a', 256)], ['name' => 'max']],
    'email is required' => [['email' => null], ['email' => 'required']],
    'email must be valid' => [['email' => 'not-an-email'], ['email' => 'email']],
    'email must be lowercase' => [['email' => 'Maria@Example.com'], ['email' => 'lowercase']],
]);

it('rejects an email that belongs to another user', function () {
    $client = Client::factory()->create();
    $other = User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test(EditClient::class, ['record' => $client->id])
        ->fillForm(['email' => $other->email])
        ->call('save')
        ->assertHasFormErrors(['email' => 'unique']);
});

it('rejects an invalid phone with the user-facing message', function () {
    $client = Client::factory()->create();

    Livewire::test(EditClient::class, ['record' => $client->id])
        ->fillForm(['phone' => '(11) 8765-43210'])
        ->call('save')
        ->assertHasFormErrors(['phone'])
        ->assertSee('Informe um telefone válido com DDD (fixo ou celular).');
});

it('deletes the client', function () {
    $client = Client::factory()->create();

    Livewire::test(EditClient::class, ['record' => $client->id])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect(ClientResource::getUrl('index'));

    $this->assertModelMissing($client);
});

it('returns 404 when editing a user who is not a client', function () {
    $technician = User::factory()->technician()->create();

    $response = $this->get(ClientResource::getUrl('edit', ['record' => $technician]));

    $response->assertNotFound();
});
