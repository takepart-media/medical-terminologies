<?php

namespace App\Filament\Resources\Icd10s\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class Icd10Form
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('icd10_code')
                    ->required(),
                TextInput::make('related_text')
                    ->translatable(),
            ]);
    }
}
