<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Atcs\AtcResource;
use App\Models\Atc;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MedicationsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    protected int|array|null $columns = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Atc codes', Atc::count())
                ->url(AtcResource::getUrl('index')),
        ];
    }
}
