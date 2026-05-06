<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class UserGrowth extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 1;

    protected int|array|null $columns = 1;

    protected function getStats(): array
    {
        $start = Carbon::today()->subDays(29);

        $countsByDay = User::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day');

        $daily = collect(range(0, 29))->map(fn (int $offset) => (int) ($countsByDay[$start->copy()->addDays($offset)->toDateString()] ?? 0));

        $newLast30 = $daily->sum();
        $newPrev30 = User::query()
            ->whereBetween('created_at', [$start->copy()->subDays(30), $start->copy()->subSecond()])
            ->count();

        $delta = $newLast30 - $newPrev30;
        $deltaIcon = match (true) {
            $delta > 0 => 'heroicon-m-arrow-trending-up',
            $delta < 0 => 'heroicon-m-arrow-trending-down',
            default => 'heroicon-m-minus',
        };
        $deltaColor = match (true) {
            $delta > 0 => 'success',
            $delta < 0 => 'danger',
            default => 'gray',
        };

        return [
            Stat::make('Total users', User::count())
                ->description($newLast30.' new in last 30 days')
                ->descriptionIcon($deltaIcon)
                ->color($deltaColor)
                ->chart($daily->all()),
        ];
    }
}
