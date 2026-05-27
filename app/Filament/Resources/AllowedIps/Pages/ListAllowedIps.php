<?php

namespace App\Filament\Resources\AllowedIps\Pages;

use App\Filament\Resources\AllowedIps\AllowedIpResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAllowedIps extends ListRecords
{
    protected static string $resource = AllowedIpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
