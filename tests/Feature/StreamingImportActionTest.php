<?php

namespace Tests\Feature;

use App\Filament\Actions\StreamingImportAction;
use App\Filament\Imports\LoincImporter;
use App\Filament\Resources\Loincs\Pages\ListLoincs;
use App\Models\Loinc;
use App\Models\User;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class StreamingImportActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_large_import_streams_one_batched_job_per_chunk(): void
    {
        // Force an async connection so the streaming (seed + append) path is exercised
        // rather than the synchronous fallback.
        config(['queue.default' => 'database']);
        Queue::fake();

        // The Loinc import action uses a chunk size of 1000, so 2500 rows produces
        // three chunks: one seeds the batch and two are appended via Batch::add().
        $rowCount = 2500;

        Livewire::test(ListLoincs::class)
            ->callAction(TestAction::make('import')->table(), data: [
                'file' => UploadedFile::fake()->createWithContent('loincs.csv', $this->loincCsv($rowCount)),
                'columnMap' => $this->loincColumnMap(),
                'locale' => 'en',
            ]);

        $import = Import::query()->latest('id')->first();

        $this->assertNotNull($import);
        $this->assertSame($rowCount, $import->total_rows);

        Queue::assertPushed(ImportCsv::class, 3);
    }

    public function test_upload_is_capped_at_100_megabytes(): void
    {
        $action = StreamingImportAction::make()->importer(LoincImporter::class);

        // 100 MB expressed in kilobytes, as Laravel's `max` file rule expects.
        $this->assertContains('max:102400', $action->getFileValidationRules());
    }

    public function test_import_persists_all_rows_end_to_end(): void
    {
        // The test suite runs on the sync queue, so the batched jobs execute inline
        // and the rows are persisted during the request.
        $rowCount = 5;

        Livewire::test(ListLoincs::class)
            ->callAction(TestAction::make('import')->table(), data: [
                'file' => UploadedFile::fake()->createWithContent('loincs.csv', $this->loincCsv($rowCount)),
                'columnMap' => $this->loincColumnMap(),
                'locale' => 'en',
            ]);

        $this->assertSame($rowCount, Loinc::query()->count());
        $this->assertTrue(Loinc::query()->where('loinc_num', 'LNC-1')->exists());
    }

    private function loincCsv(int $rows): string
    {
        $lines = ['LOINC_NUM'];

        for ($i = 1; $i <= $rows; $i++) {
            $lines[] = 'LNC-'.$i;
        }

        return implode("\n", $lines)."\n";
    }

    /**
     * @return array<string, string|null>
     */
    private function loincColumnMap(): array
    {
        return [
            'loinc_num' => 'LOINC_NUM',
            'component' => null,
            'property' => null,
            'system' => null,
            'related_names_2' => null,
            'short_name' => null,
            'example_units' => null,
            'example_ucum_units' => null,
            'display_name' => null,
        ];
    }
}
