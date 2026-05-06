<?php

namespace App\Filament\Resources\LoincAnswers\Pages;

use App\Filament\Resources\LoincAnswers\LoincAnswerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLoincAnswer extends EditRecord
{
    protected static string $resource = LoincAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
