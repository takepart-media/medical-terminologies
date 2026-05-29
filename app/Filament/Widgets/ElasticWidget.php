<?php

namespace App\Filament\Widgets;

use App\Jobs\ReindexAllSearchable;
use Elastic\Client\ClientBuilderInterface;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Throwable;

class ElasticWidget extends StatsOverviewWidget implements HasActions
{
    use InteractsWithActions;

    protected static ?int $sort = 1;

    protected int|array|null $columns = 3;

    protected ?string $pollingInterval = '3s';

    protected string $view = 'filament.widgets.elastic-widget';

    protected function getStats(): array
    {
        return [
            $this->buildReindexStat(),
            $this->buildRunningOperationsStat(),
            $this->buildStatusStat(),
        ];
    }

    public function reindexAction(): Action
    {
        return Action::make('reindex')
            ->label('Flush & re-import')
            ->icon('heroicon-m-arrow-path')
            ->color('primary')
            ->modalHeading('Re-index Scout search')
            ->modalDescription('This deletes and rebuilds the search index for the chosen target.')
            ->modalSubmitActionLabel('Flush & re-import')
            ->schema([
                Select::make('model')
                    ->label('Target')
                    ->options($this->modelOptions())
                    ->default('all')
                    ->required()
                    ->native(false),
            ])
            ->action(function (array $data): void {
                $model = $data['model'] === 'all' ? null : $data['model'];

                ReindexAllSearchable::dispatch($model);

                Notification::make()
                    ->title('Re-indexing started')
                    ->body($model
                        ? class_basename($model).' is being flushed and re-imported in the background.'
                        : 'All Scout models are being flushed and re-imported in the background.')
                    ->success()
                    ->send();
            });
    }

    private function buildStatusStat(): Stat
    {
        [$status, $clusterName] = $this->probeCluster();

        $color = match ($status) {
            'green' => 'success',
            'yellow' => 'success',
            default => 'danger',
        };

        return Stat::make('Elastic status', ucfirst($status))
            ->description($clusterName ?? 'Cluster unreachable')
            ->descriptionIcon('heroicon-m-signal')
            ->color($color);
    }

    private function buildReindexStat(): Stat
    {
        $button = new HtmlString(Blade::render(<<<'BLADE'
            <x-filament::button
                wire:click="mountAction('reindex')"
                icon="heroicon-m-arrow-path"
                color="primary"
                size="sm"
            >
                Flush & re-import
            </x-filament::button>
        BLADE
        ));

        return Stat::make('Search index', 'Flush and re-import')
            ->description($button);
    }

    /**
     * @return array<string, string>
     */
    private function modelOptions(): array
    {
        $options = ['all' => 'All models'];

        foreach (ReindexAllSearchable::MODELS as $model) {
            $options[$model] = class_basename($model);
        }

        return $options;
    }

    private function buildRunningOperationsStat(): Stat
    {
        $actions = $this->probeRunningOperations();

        if ($actions === null) {
            return Stat::make('Running operations', 'Unavailable')
                ->description('Could not reach cluster')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger');
        }

        if ($actions === []) {
            return Stat::make('Running operations', '0')
                ->description('Idle — no active index work')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success');
        }

        $summary = collect($actions)
            ->countBy()
            ->map(fn (int $count, string $action) => $count > 1 ? "$action ×$count" : $action)
            ->values()
            ->take(3)
            ->implode(', ');

        return Stat::make('Running operations', count($actions))
            ->description($summary)
            ->descriptionIcon('heroicon-m-bolt')
            ->color('warning');
    }

    /**
     * @return array{0: string, 1: ?string}
     */
    private function probeCluster(): array
    {
        try {
            $health = app(ClientBuilderInterface::class)
                ->default()
                ->cluster()
                ->health()
                ->asArray();

            return [
                $health['status'] ?? 'unreachable',
                $health['cluster_name'] ?? null,
            ];
        } catch (Throwable) {
            return ['unreachable', null];
        }
    }

    /**
     * @return array<int, string>|null Action strings of running tasks, or null when the cluster is unreachable.
     */
    private function probeRunningOperations(): ?array
    {
        try {
            $response = app(ClientBuilderInterface::class)
                ->default()
                ->tasks()
                ->list(['detailed' => true])
                ->asArray();
        } catch (Throwable) {
            return null;
        }

        $actions = [];

        foreach ($response['nodes'] ?? [] as $node) {
            foreach ($node['tasks'] ?? [] as $task) {
                $action = $task['action'] ?? '';

                if ($this->isNoiseAction($action)) {
                    continue;
                }

                $actions[] = $this->normalizeAction($action);
            }
        }

        return $actions;
    }

    private function isNoiseAction(string $action): bool
    {
        return str_starts_with($action, 'cluster:monitor/')
            || str_starts_with($action, 'internal:')
            || str_starts_with($action, 'cluster:admin/tasks/list')
            || str_starts_with($action, 'health-node') ||
            str_starts_with($action, 'geoip-downloader');
    }

    private function normalizeAction(string $action): string
    {
        $bracket = strpos($action, '[');

        return $bracket === false ? $action : substr($action, 0, $bracket);
    }
}
