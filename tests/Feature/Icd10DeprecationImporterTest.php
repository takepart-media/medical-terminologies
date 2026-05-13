<?php

namespace Tests\Feature;

use App\Filament\Imports\Icd10DeprecationImporter;
use App\Filament\Imports\Icd10Importer;
use App\Models\Icd10;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Icd10DeprecationImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_deprecation_importer_soft_deletes_existing_code(): void
    {
        $code = Icd10::create(['icd10_code' => 'A00.0']);

        $importer = new Icd10DeprecationImporter(new Import, [], []);
        $importer(['icd10_code' => 'A00.0']);

        $this->assertSoftDeleted($code);
        $this->assertNull(Icd10::where('icd10_code', 'A00.0')->first());
        $this->assertNotNull(Icd10::withTrashed()->where('icd10_code', 'A00.0')->first());
    }

    public function test_deprecation_importer_returns_null_for_missing_code(): void
    {
        $importer = new Icd10DeprecationImporter(new Import, [], []);
        $importer(['icd10_code' => 'NOT_REAL']);

        $this->assertSame(0, Icd10::withTrashed()->count());
    }

    public function test_normal_importer_restores_soft_deleted_code(): void
    {
        $code = Icd10::create(['icd10_code' => 'A00.0']);
        $code->delete();

        $this->assertSoftDeleted($code);

        $importer = new Icd10Importer(new Import, [], ['locale' => 'en']);
        $importer(['icd10_code' => 'A00.0', 'related_text' => 'Cholera']);

        $restored = Icd10::where('icd10_code', 'A00.0')->first();
        $this->assertNotNull($restored);
        $this->assertNull($restored->deleted_at);
    }
}
