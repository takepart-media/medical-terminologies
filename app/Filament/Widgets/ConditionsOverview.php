<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Icd10s\Icd10Resource;
use App\Filament\Resources\Orphas\OrphaResource;
use App\Models\Icd10;
use App\Models\Orpha;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConditionsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected int|array|null $columns = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Orpha codes', Orpha::count())
                ->url(OrphaResource::getUrl('index')),

            Stat::make('Total Icd10 codes', Icd10::count())
                ->url(Icd10Resource::getUrl('index')),
        ];
    }
}
