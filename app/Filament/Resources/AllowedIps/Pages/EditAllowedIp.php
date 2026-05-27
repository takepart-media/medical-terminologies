<?php

namespace App\Filament\Resources\AllowedIps\Pages;

use App\Filament\Resources\AllowedIps\AllowedIpResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAllowedIp extends EditRecord
{
    protected static string $resource = AllowedIpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
