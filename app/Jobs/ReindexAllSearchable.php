<?php

namespace App\Jobs;

use App\Models\Atc;
use App\Models\Icd10;
use App\Models\Loinc;
use App\Models\LoincAnswer;
use App\Models\Orpha;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReindexAllSearchable implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public int $tries = 1;

    /**
     * @var array<int, class-string>
     */
    public const MODELS = [
        Loinc::class,
        LoincAnswer::class,
        Atc::class,
        Orpha::class,
        Icd10::class,
    ];

    /**
     * @param  class-string|null  $model  Re-index a single model when set; otherwise all.
     */
    public function __construct(public ?string $model = null) {}

    public function handle(): void
    {
        $targets = $this->model ? [$this->model] : self::MODELS;

        foreach ($targets as $model) {
            try {
                Artisan::call('scout:flush', ['model' => $model]);
                Artisan::call('scout:import', ['model' => $model]);
            } catch (Throwable $e) {
                Log::error('Scout reindex failed', [
                    'model' => $model,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
