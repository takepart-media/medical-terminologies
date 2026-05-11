<?php

namespace App\Filament\Resources\Orphas\Pages;

use App\Filament\Resources\Orphas\OrphaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrphas extends ListRecords
{
    protected static string $resource = OrphaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
