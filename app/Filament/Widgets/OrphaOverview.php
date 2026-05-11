<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orphas\OrphaResource;
use App\Models\Orpha;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrphaOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    protected int|array|null $columns = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Orpha codes', Orpha::count())
                ->url(OrphaResource::getUrl('index')),
        ];
    }
}
