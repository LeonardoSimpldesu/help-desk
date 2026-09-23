<?php

use App\Filament\Admin\Pages\Auth\Login;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

it('renders the translated login form', function () {
    $response = $this->get(route('filament.admin.auth.login'));

    $response->assertSeeText(['E-mail', 'Senha', 'Lembrar-me', 'Entrar']);
});

it('logs staff into the admin panel', function (string $role) {
    $staff = User::factory()->{$role}()->create();

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $staff->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/admin');

    $this->assertAuthenticatedAs($staff);
})->with([
    'admin' => 'admin',
    'technician' => 'technician',
]);

it('rejects a client trying to log into the admin panel', function () {
    $client = Client::factory()->create();

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $client->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});
