<?php

namespace App\Filament\Resources\Atcs\Pages;

use App\Filament\Resources\Atcs\AtcResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAtc extends EditRecord
{
    protected static string $resource = AtcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
