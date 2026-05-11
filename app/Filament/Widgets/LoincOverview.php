<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LoincAnswers\LoincAnswerResource;
use App\Filament\Resources\Loincs\LoincResource;
use App\Models\Loinc;
use App\Models\LoincAnswer;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoincOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected int|array|null $columns = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Loincs', Loinc::count())
                ->url(LoincResource::getUrl('index')),

            Stat::make('Total Loinc Answers', LoincAnswer::count())
                ->url(LoincAnswerResource::getUrl('index')),
        ];
    }
}
