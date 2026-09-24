<?php

namespace App\Filament\Admin\Resources\Technicians\Schemas;

use App\Models\Schedule;
use App\Rules\BrazilianPhone;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class TechnicianForm
{
    /**
     * Maps each form field to the Schedule::period() label it represents.
     *
     * @var array<string, string>
     */
    public const SCHEDULE_PERIODS = [
        'schedules_manha' => 'Manhã',
        'schedules_tarde' => 'Tarde',
        'schedules_noite' => 'Noite',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Endereço de Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->rules(['lowercase'])
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->tel()
                    ->mask(RawJs::make(BrazilianPhone::MASK))
                    ->stripCharacters(['(', ')', ' ', '-'])
                    ->rules(['nullable', new BrazilianPhone]),
                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->required()
                    ->minLength(6)
                    ->helperText('Mínimo de 6 dígitos')
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->visibleOn('create'),
                Fieldset::make('Horários de atendimento')
                    ->columns(1)
                    ->schema(static::scheduleFields()),
            ]);
    }

    /**
     * @return array<int, ToggleButtons>
     */
    protected static function scheduleFields(): array
    {
        $schedulesByPeriod = static::schedulesByPeriod();

        return collect(self::SCHEDULE_PERIODS)
            ->map(fn (string $period, string $field) => ToggleButtons::make($field)
                ->label($period)
                ->multiple()
                ->inline()
                ->options($schedulesByPeriod->get($period, collect())->pluck('label', 'id')))
            ->values()
            ->all();
    }

    /**
     * Splits the currently selected schedule ids into the per-period form fields, for prefilling the edit form.
     *
     * @param  Collection<int, int>  $selectedScheduleIds
     * @return array<string, array<int, int>>
     */
    public static function fillScheduleFields(Collection $selectedScheduleIds): array
    {
        $schedulesByPeriod = static::schedulesByPeriod();

        return collect(self::SCHEDULE_PERIODS)
            ->mapWithKeys(fn (string $period, string $field) => [
                $field => $schedulesByPeriod->get($period, collect())
                    ->pluck('id')
                    ->intersect($selectedScheduleIds)
                    ->values()
                    ->all(),
            ])
            ->all();
    }

    /**
     * @return Collection<string, Collection<int, Schedule>>
     */
    protected static function schedulesByPeriod(): Collection
    {
        return Schedule::query()->orderBy('starts_at')->get()->groupBy('period');
    }
}
