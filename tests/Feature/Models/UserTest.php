<?php

use App\Models\Client;
use App\Models\User;
use Filament\Facades\Filament;

it('lets staff access the admin panel', function (string $role) {
    $staff = User::factory()->{$role}()->create();

    expect($staff->canAccessPanel(Filament::getPanel('admin')))->toBeTrue();
})->with([
    'admin' => 'admin',
    'technician' => 'technician',
]);

it('does not let a client access the admin panel', function () {
    $client = Client::factory()->create();

    expect($client->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
});

it('does not let a user without a role access the admin panel', function () {
    $user = User::factory()->create();

    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
});

it('hides the password and remember token when serialized', function () {
    $user = User::factory()->create();

    expect($user->toArray())->not->toHaveKeys(['password', 'remember_token']);
});

it('hashes the password when it is set', function () {
    $user = User::factory()->create(['password' => 'plain-password']);

    expect($user->password)->not->toBe('plain-password')
        ->and(password_verify('plain-password', $user->password))->toBeTrue();
});
