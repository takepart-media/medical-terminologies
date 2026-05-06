<?php

namespace App\Filament\Resources\Loincs\Pages;

use App\Filament\Resources\Loincs\LoincResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLoinc extends EditRecord
{
    protected static string $resource = LoincResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
