<?php

namespace App\Filament\Admin\Resources\Technicians\Pages;

use App\Filament\Admin\Resources\Technicians\TechnicianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTechnicians extends ListRecords
{
    protected static string $resource = TechnicianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
