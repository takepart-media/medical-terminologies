<?php

namespace App\Filament\Resources\LoincAnswers\Pages;

use App\Filament\Resources\LoincAnswers\LoincAnswerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLoincAnswers extends ListRecords
{
    protected static string $resource = LoincAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
