<?php

namespace App\Filament\Imports;

use App\Models\Loinc;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class LoincDeprecationImporter extends Importer
{
    protected static ?string $model = Loinc::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('loinc_num')
                ->label('LOINC_NUM')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Loinc
    {
        return Loinc::withTrashed()
            ->where('loinc_num', $this->data['loinc_num'])
            ->first();
    }

    public function saveRecord(): void
    {
        $this->record->delete();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your loinc deprecation import has completed and '.number_format($import->successful_rows).' '.str('code')->plural($import->successful_rows).' deprecated.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
