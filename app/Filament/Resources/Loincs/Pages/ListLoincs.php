<?php

namespace App\Filament\Resources\Loincs\Pages;

use App\Filament\Resources\Loincs\LoincResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLoincs extends ListRecords
{
    protected static string $resource = LoincResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
