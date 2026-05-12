<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Icd10s\Icd10Resource;
use App\Models\Icd10;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Icd10Overview extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected int|array|null $columns = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Icd10 codes', Icd10::count())
                ->url(Icd10Resource::getUrl('index')),
        ];
    }
}
