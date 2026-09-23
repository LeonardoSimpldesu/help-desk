<?php

use App\Filament\Admin\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('lists only clients', function () {
    $clients = Client::factory()->count(3)->create();
    $technician = User::factory()->technician()->create();

    Livewire::test(ListClients::class)
        ->assertCanSeeTableRecords($clients)
        ->assertCanNotSeeTableRecords([$technician, auth()->user()])
        ->assertCountTableRecords(3);
});

it('searches clients by name, email and phone', function (string $field) {
    $target = Client::factory()->create([
        'name' => 'Maria Souza',
        'email' => 'maria@example.com',
        'phone' => '11987654321',
    ]);
    $other = Client::factory()->create([
        'name' => 'João Lima',
        'email' => 'joao@example.com',
        'phone' => '2133334444',
    ]);

    Livewire::test(ListClients::class)
        ->searchTable($target->{$field})
        ->assertCanSeeTableRecords([$target])
        ->assertCanNotSeeTableRecords([$other]);
})->with([
    'name' => 'name',
    'email' => 'email',
    'phone' => 'phone',
]);

it('bulk deletes the selected clients', function () {
    $clients = Client::factory()->count(2)->create();
    $kept = Client::factory()->create();

    Livewire::test(ListClients::class)
        ->selectTableRecords($clients)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertNotified();

    $clients->each(fn (Client $client) => $this->assertModelMissing($client));
    $this->assertModelExists($kept);
});
