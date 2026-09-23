<?php

namespace App\Filament\Admin\Resources\Clients\Schemas;

use App\Rules\BrazilianPhone;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ClientForm
{
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
            ]);
    }
}
