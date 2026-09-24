<?php

use App\Filament\Admin\Resources\Technicians\Pages\ListTechnicians;
use App\Models\Schedule;
use App\Models\Technician;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('shows the technician schedules ordered by time in the availability column', function () {
    $afternoon = Schedule::query()->firstOrCreate(['starts_at' => '14:00']);
    $morning = Schedule::query()->firstOrCreate(['starts_at' => '08:00']);

    $technician = Technician::factory()->create();
    $technician->schedules()->attach([$afternoon->id, $morning->id]);

    Livewire::test(ListTechnicians::class)
        ->assertTableColumnStateSet('schedules', ['08:00', '14:00'], record: $technician);
});
