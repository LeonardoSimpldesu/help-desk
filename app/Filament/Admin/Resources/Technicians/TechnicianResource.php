<?php

namespace App\Filament\Admin\Resources\Technicians;

use App\Filament\Admin\Resources\Technicians\Pages\CreateTechnician;
use App\Filament\Admin\Resources\Technicians\Pages\EditTechnician;
use App\Filament\Admin\Resources\Technicians\Pages\ListTechnicians;
use App\Filament\Admin\Resources\Technicians\Schemas\TechnicianForm;
use App\Filament\Admin\Resources\Technicians\Tables\TechniciansTable;
use App\Models\Technician;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TechnicianResource extends Resource
{
    protected static ?string $model = Technician::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TechnicianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TechniciansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTechnicians::route('/'),
            'create' => CreateTechnician::route('/create'),
            'edit' => EditTechnician::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Técnico';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Técnicos';
    }
}
