<?php

namespace App\Filament\Resources\AllowedIps\Pages;

use App\Filament\Resources\AllowedIps\AllowedIpResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAllowedIp extends ViewRecord
{
    protected static string $resource = AllowedIpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
