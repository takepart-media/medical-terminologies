<?php

namespace App\Filament\Resources\Orphas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrphaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('orpha_code')
                    ->required(),
                TextInput::make('label')
                    ->translatable(),
                TextInput::make('label_type')
                    ->translatable(),
                TextInput::make('classification_level')
                    ->translatable(),
            ]);
    }
}
