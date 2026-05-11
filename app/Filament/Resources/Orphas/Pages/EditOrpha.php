<?php

namespace App\Filament\Resources\Orphas\Pages;

use App\Filament\Resources\Orphas\OrphaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrpha extends EditRecord
{
    protected static string $resource = OrphaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
