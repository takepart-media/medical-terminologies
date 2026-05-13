<?php

namespace App\Filament\Imports;

use App\Models\Icd10;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class Icd10DeprecationImporter extends Importer
{
    protected static ?string $model = Icd10::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('icd10_code')
                ->label('ICD10_CODE')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Icd10
    {
        return Icd10::withTrashed()
            ->where('icd10_code', $this->data['icd10_code'])
            ->first();
    }

    public function saveRecord(): void
    {
        $this->record->delete();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your icd10 deprecation import has completed and '.number_format($import->successful_rows).' '.str('code')->plural($import->successful_rows).' deprecated.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
