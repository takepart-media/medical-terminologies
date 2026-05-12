<?php

namespace App\Filament\Resources\Icd10s\Pages;

use App\Filament\Resources\Icd10s\Icd10Resource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIcd10s extends ListRecords
{
    protected static string $resource = Icd10Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
