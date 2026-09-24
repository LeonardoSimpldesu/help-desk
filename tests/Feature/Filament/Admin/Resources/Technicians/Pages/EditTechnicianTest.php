<?php

use App\Filament\Admin\Resources\Technicians\Pages\EditTechnician;
use App\Models\Schedule;
use App\Models\Technician;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('hides the password field', function () {
    $technician = Technician::factory()->create();

    Livewire::test(EditTechnician::class, ['record' => $technician->id])
        ->assertFormFieldHidden('password');
});

it('fills the form with the schedules the technician already works, split by period', function () {
    $morning = Schedule::query()->firstOrCreate(['starts_at' => '08:00']);
    $afternoon = Schedule::query()->firstOrCreate(['starts_at' => '14:00']);
    $night = Schedule::query()->firstOrCreate(['starts_at' => '20:00']);

    $technician = Technician::factory()->create();
    $technician->schedules()->attach([$morning->id, $night->id]);

    Livewire::test(EditTechnician::class, ['record' => $technician->id])
        ->assertSchemaStateSet([
            'schedules_manha' => [$morning->id],
            'schedules_tarde' => [],
            'schedules_noite' => [$night->id],
        ]);

    expect($afternoon)->not->toBeNull();
});

it('syncs the selected schedules when saved', function () {
    $morning = Schedule::query()->firstOrCreate(['starts_at' => '08:00']);
    $afternoon = Schedule::query()->firstOrCreate(['starts_at' => '14:00']);

    $technician = Technician::factory()->create();
    $technician->schedules()->attach($morning->id);

    Livewire::test(EditTechnician::class, ['record' => $technician->id])
        ->fillForm([
            'schedules_manha' => [],
            'schedules_tarde' => [$afternoon->id],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($technician->schedules()->pluck('schedules.id')->all())->toBe([$afternoon->id]);
});

it('does not change the password when saved', function () {
    $technician = Technician::factory()->create();
    $originalPassword = $technician->password;

    Livewire::test(EditTechnician::class, ['record' => $technician->id])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($technician->fresh()->password)->toBe($originalPassword);
});
