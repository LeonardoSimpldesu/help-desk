<?php

namespace App\Filament\Admin\Resources\Technicians\Pages;

use App\Filament\Admin\Resources\Technicians\Schemas\TechnicianForm;
use App\Filament\Admin\Resources\Technicians\TechnicianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditTechnician extends EditRecord
{
    protected static string $resource = TechnicianResource::class;

    /**
     * @var array<int, int>
     */
    protected array $scheduleIds = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $selectedScheduleIds = $this->record->schedules()->pluck('schedules.id');

        return [
            ...$data,
            ...TechnicianForm::fillScheduleFields($selectedScheduleIds),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->scheduleIds = collect($data)
            ->only(array_keys(TechnicianForm::SCHEDULE_PERIODS))
            ->flatten()
            ->all();

        return Arr::except($data, array_keys(TechnicianForm::SCHEDULE_PERIODS));
    }

    protected function afterSave(): void
    {
        $this->record->schedules()->sync($this->scheduleIds);
    }
}
