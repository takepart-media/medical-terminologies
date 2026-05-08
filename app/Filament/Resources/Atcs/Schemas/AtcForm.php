<?php

namespace App\Filament\Resources\Atcs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AtcForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('atc_code')
                    ->required(),
                TextInput::make('unit'),
                TextInput::make('name')
                    ->translatable(),
                TextInput::make('route'),
                TextInput::make('ddd_info')
                    ->translatable(),
                TextInput::make('note')
                    ->translatable(),
            ]);
    }
}
