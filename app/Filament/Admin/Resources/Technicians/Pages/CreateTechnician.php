<?php

namespace App\Filament\Admin\Resources\Technicians\Pages;

use App\Filament\Admin\Resources\Technicians\Schemas\TechnicianForm;
use App\Filament\Admin\Resources\Technicians\TechnicianResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateTechnician extends CreateRecord
{
    protected static string $resource = TechnicianResource::class;

    /**
     * @var array<int, int>
     */
    protected array $scheduleIds = [];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->scheduleIds = collect($data)
            ->only(array_keys(TechnicianForm::SCHEDULE_PERIODS))
            ->flatten()
            ->all();

        return Arr::except($data, array_keys(TechnicianForm::SCHEDULE_PERIODS));
    }

    protected function afterCreate(): void
    {
        $this->record->schedules()->sync($this->scheduleIds);
    }
}
