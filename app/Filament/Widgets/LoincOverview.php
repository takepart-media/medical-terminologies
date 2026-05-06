<?php

namespace App\Filament\Widgets;

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
            Stat::make('Total Loincs', Loinc::count()),

            Stat::make('Total Loinc Answers', LoincAnswer::count()),
        ];
    }
}
