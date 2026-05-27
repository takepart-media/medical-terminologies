<?php

namespace App\Filament\Resources\AllowedIps\Pages;

use App\Filament\Resources\AllowedIps\AllowedIpResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAllowedIp extends CreateRecord
{
    protected static string $resource = AllowedIpResource::class;
}
