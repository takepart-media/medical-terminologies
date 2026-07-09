<?php

namespace App\Filament\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Events\ImportCompleted;
use Filament\Actions\Imports\Events\ImportStarted;
use Filament\Actions\Imports\Models\Import;
use Filament\Notifications\Notification;
use Filament\Support\ChunkIterator;
use Generator;
use Illuminate\Bus\Batch;
use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;
use Iterator;
use League\Csv\Reader as CsvReader;
use League\Csv\Statement;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

/**
 * Drop-in replacement for Filament's ImportAction that dispatches the import
 * jobs while streaming the CSV, instead of loading the whole file into memory.
 *
 * The framework's action collects every chunk and its serialized job payload
 * into a single in-memory collection before dispatching the batch. For large
 * files (e.g. the ~82 MB LOINC release) that peak allocation exhausts the
 * PHP-FPM worker's memory and the request dies with a 502. Here we seed the
 * batch with the first chunk and append the rest via {@see Batch::add()}, so
 * peak memory stays at roughly one chunk regardless of file size.
 */
class StreamingImportAction extends ImportAction
{
    /**
     * Maximum accepted upload size in kilobytes (100 MB), matching the ceiling
     * enforced by nginx and PHP in production.
     */
    protected const int MAX_FILE_SIZE_KB = 100 * 1024;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileRules(['max:'.self::MAX_FILE_SIZE_KB]);

        $this->action(function (StreamingImportAction $action, array $data): void {
            /** @var TemporaryUploadedFile $csvFile */
            $csvFile = $data['file'];

            $csvStream = $this->getUploadedFileStream($csvFile);

            if (! $csvStream) {
                return;
            }

            $csvReader = CsvReader::from($csvStream);

            if (filled($csvDelimiter = $this->getCsvDelimiter($csvReader))) {
                $csvReader->setDelimiter($csvDelimiter);
            }

            $csvReader->setHeaderOffset($action->getHeaderOffset() ?? 0);
            $csvResults = (new Statement)->process($csvReader);

            $totalRows = $csvResults->count();
            $maxRows = $action->getMaxRows() ?? $totalRows;

            if ($maxRows < $totalRows) {
                $action->failureNotification(
                    Notification::make()
                        ->title(__('filament-actions::import.notifications.max_rows.title'))
                        ->body(trans_choice('filament-actions::import.notifications.max_rows.body', $maxRows, [
                            'count' => Number::format($maxRows),
                        ]))
                        ->danger(),
                );

                $action->failure();

                return;
            }

            $authGuard = $action->getAuthGuard();

            $user = auth($authGuard)->user();

            $import = app(Import::class);
            $import->user()->associate($user);
            $import->file_name = $csvFile->getClientOriginalName();
            $import->file_path = $csvFile->getRealPath();
            $import->importer = $action->getImporter();
            $import->total_rows = $totalRows;
            $import->save();

            $options = array_merge(
                $action->getOptions(),
                Arr::except($data, ['file', 'columnMap']),
            );

            // We do not want to send the loaded user relationship to the queue in job payloads,
            // in case it contains attributes that are not serializable, such as binary columns.
            $import->unsetRelation('user');

            $columnMap = $data['columnMap'];

            $importer = $import->getImporter(
                columnMap: $columnMap,
                options: $options,
            );

            $job = $action->getJob();

            $makeImportJob = fn (array $importChunk): object => app($job, [
                'import' => $import,
                'rows' => base64_encode(serialize($importChunk)),
                'columnMap' => $columnMap,
                'options' => $options,
            ]);

            $jobQueue = $importer->getJobQueue();
            $jobConnection = $importer->getJobConnection();
            $jobBatchName = $importer->getJobBatchName();

            $configureBatch = fn (array $jobs): PendingBatch => Bus::batch($jobs)
                ->allowFailures()
                ->when(
                    filled($jobQueue),
                    fn (PendingBatch $batch) => $batch->onQueue($jobQueue),
                )
                ->when(
                    filled($jobConnection),
                    fn (PendingBatch $batch) => $batch->onConnection($jobConnection),
                )
                ->when(
                    filled($jobBatchName),
                    fn (PendingBatch $batch) => $batch->name($jobBatchName),
                )
                ->finally(function () use ($authGuard, $columnMap, $import, $jobConnection, $options): void {
                    $import->touch('completed_at');

                    event(new ImportCompleted($import, $columnMap, $options));

                    if (! $import->user instanceof Authenticatable) {
                        return;
                    }

                    $import->columnMap($columnMap);
                    $import->options($options);

                    $failedRowsCount = $import->getFailedRowsCount();

                    $isSynchronous = ($jobConnection === 'sync') || (blank($jobConnection) && (config('queue.default') === 'sync'));

                    $notification = Notification::make()
                        ->title($import->importer::getCompletedNotificationTitle($import))
                        ->body($import->importer::getCompletedNotificationBody($import))
                        ->when(
                            ! $failedRowsCount,
                            fn (Notification $notification) => $notification->success(),
                        )
                        ->when(
                            $failedRowsCount && ($failedRowsCount < $import->total_rows),
                            fn (Notification $notification) => $notification->warning(),
                        )
                        ->when(
                            $failedRowsCount === $import->total_rows,
                            fn (Notification $notification) => $notification->danger(),
                        )
                        ->when(
                            $failedRowsCount,
                            fn (Notification $notification) => $notification->actions([
                                Action::make('downloadFailedRowsCsv')
                                    ->label(trans_choice('filament-actions::import.notifications.completed.actions.download_failed_rows_csv.label', $failedRowsCount, [
                                        'count' => Number::format($failedRowsCount),
                                    ]))
                                    ->color('danger')
                                    ->url(URL::signedRoute('filament.imports.failed-rows.download', ['authGuard' => $authGuard, 'import' => $import], absolute: false), shouldOpenInNewTab: true)
                                    ->markAsRead(),
                            ]),
                        )
                        ->when(
                            $isSynchronous,
                            fn (Notification $notification) => $notification->persistent(),
                        );

                    $notification = $import->importer::modifyCompletedNotification($notification, $import);

                    if ($isSynchronous) {
                        $notification->send();
                    } else {
                        $notification->sendToDatabase($import->user, isEventDispatched: true);
                    }
                });

            event(new ImportStarted($import, $columnMap, $options));

            $isSynchronous = ($jobConnection === 'sync') || (blank($jobConnection) && (config('queue.default') === 'sync'));

            if ($isSynchronous) {
                // A synchronous connection runs each job the moment it is dispatched, so a
                // progressively built batch would report itself complete before the remaining
                // chunks are added. Sync is only used for small/local imports, so we buffer the
                // jobs like the framework's default action does.
                $importJobs = [];

                foreach ($this->streamImportChunks($csvResults->getRecords(), $action->getChunkSize()) as $importChunk) {
                    $importJobs[] = $makeImportJob($importChunk);
                }

                $configureBatch($importJobs)->dispatch();
            } else {
                $this->streamBatch($configureBatch, $makeImportJob, $csvResults->getRecords(), $action->getChunkSize());
            }

            if ($isSynchronous) {
                $action->successNotification(null);
                $action->successNotificationTitle(null);

                return;
            }

            $action->successNotification(
                Notification::make()
                    ->title($action->getSuccessNotificationTitle())
                    ->body(trans_choice('filament-actions::import.notifications.started.body', $import->total_rows, [
                        'count' => Number::format($import->total_rows),
                    ]))
                    ->success(),
            );
        });
    }

    /**
     * Dispatch the batch seeded with the first chunk, then append the remaining
     * chunks one at a time so only a single chunk is ever held in memory.
     *
     * @param  Closure(array<object>): PendingBatch  $configureBatch
     * @param  Closure(array<array<string, string>>): object  $makeImportJob
     * @param  Iterator<array<string, string>>  $records
     *
     * @throws Throwable
     */
    protected function streamBatch(Closure $configureBatch, Closure $makeImportJob, Iterator $records, int $chunkSize): void
    {
        $chunks = $this->streamImportChunks($records, $chunkSize);

        $firstChunk = $chunks->valid() ? $chunks->current() : null;

        $batch = $configureBatch($firstChunk === null ? [] : [$makeImportJob($firstChunk)])->dispatch();

        if ($firstChunk === null) {
            return;
        }

        $chunks->next();

        while ($chunks->valid()) {
            $batch->add([$makeImportJob($chunks->current())]);

            $chunks->next();
        }
    }

    /**
     * Yield the CSV records in chunks without materialising the whole file.
     *
     * @param  Iterator<array<string, string>>  $records
     * @return Generator<array<array<string, string>>>
     */
    protected function streamImportChunks(Iterator $records, int $chunkSize): Generator
    {
        $generator = (new ChunkIterator($records, chunkSize: $chunkSize))->get();
        $generator->rewind();

        return $generator;
    }
}
