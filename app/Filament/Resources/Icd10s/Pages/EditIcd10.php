<?php

namespace App\Filament\Resources\Icd10s\Pages;

use App\Filament\Resources\Icd10s\Icd10Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIcd10 extends EditRecord
{
    protected static string $resource = Icd10Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
