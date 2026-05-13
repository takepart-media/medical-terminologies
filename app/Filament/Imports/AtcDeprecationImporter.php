<?php

namespace App\Filament\Imports;

use App\Models\Atc;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AtcDeprecationImporter extends Importer
{
    protected static ?string $model = Atc::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('atc_code')
                ->label('ATC_CODE')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Atc
    {
        return Atc::withTrashed()
            ->where('atc_code', $this->data['atc_code'])
            ->first();
    }

    public function saveRecord(): void
    {
        $this->record->delete();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your atc deprecation import has completed and '.number_format($import->successful_rows).' '.str('code')->plural($import->successful_rows).' deprecated.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
