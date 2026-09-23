<?php

namespace App\Filament\Admin\Resources\Clients\Schemas;

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
                    ->mask(RawJs::make(<<<'JS'
                        $input.replace(/\D/g, '').length > 10
                            ? '(99) 99999-9999'
                            : '(99) 9999-9999'
                    JS))
                    ->stripCharacters(['(', ')', ' ', '-'])
                    ->rules(['nullable', 'digits_between:10,11'])
                    ->validationMessages([
                        'digits_between' => 'Informe um telefone válido com DDD (fixo ou celular).',
                    ]),
            ]);
    }
}
