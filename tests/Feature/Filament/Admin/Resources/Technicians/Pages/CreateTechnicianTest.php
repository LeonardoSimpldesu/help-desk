<?php

use App\Filament\Admin\Resources\Technicians\Pages\CreateTechnician;
use App\Models\Schedule;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('shows the password field', function () {
    Livewire::test(CreateTechnician::class)
        ->assertFormFieldVisible('password');
});

it('creates a technician with a hashed password, the technician role and the selected schedules', function () {
    $morning = Schedule::query()->firstOrCreate(['starts_at' => '08:00']);
    $afternoon = Schedule::query()->firstOrCreate(['starts_at' => '14:00']);

    Livewire::test(CreateTechnician::class)
        ->fillForm([
            'name' => 'Carlos Silva',
            'email' => 'carlos.silva@example.com',
            'password' => 'secret-password',
            'schedules_manha' => [$morning->id],
            'schedules_tarde' => [$afternoon->id],
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $technician = Technician::where('email', 'carlos.silva@example.com')->sole();

    expect(Hash::check('secret-password', $technician->password))->toBeTrue()
        ->and($technician->getRoleNames()->all())->toBe(['technician'])
        ->and($technician->schedules()->pluck('schedules.id')->sort()->values()->all())
        ->toBe([$morning->id, $afternoon->id]);
});

it('rejects a missing password', function () {
    Livewire::test(CreateTechnician::class)
        ->fillForm([
            'name' => 'Carlos Silva',
            'email' => 'carlos.silva@example.com',
            'password' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['password' => 'required']);
});
