<?php

namespace App\Filament\Admin\Resources\Technicians\Tables;

use App\Models\Schedule;
use App\Models\Technician;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TechniciansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('schedules'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label('Email verificado em')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('schedules')
                    ->label('Disponibilidade')
                    ->state(fn (Technician $record): array => $record->schedules
                        ->sortBy('starts_at')
                        ->map(fn (Schedule $schedule) => $schedule->starts_at->format('H:i'))
                        ->values()
                        ->all())
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(4)
                    ->expandableLimitedList()
                    ->extraAttributes(['class' => 'fi-ta-schedule-badges']),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
