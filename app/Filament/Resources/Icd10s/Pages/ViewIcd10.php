<?php

namespace App\Filament\Resources\Icd10s\Pages;

use App\Filament\Resources\Icd10s\Icd10Resource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIcd10 extends ViewRecord
{
    protected static string $resource = Icd10Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
