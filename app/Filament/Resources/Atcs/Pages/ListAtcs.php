<?php

namespace App\Filament\Resources\Atcs\Pages;

use App\Filament\Resources\Atcs\AtcResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAtcs extends ListRecords
{
    protected static string $resource = AtcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
