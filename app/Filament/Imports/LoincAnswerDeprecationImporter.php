<?php

namespace App\Filament\Imports;

use App\Models\LoincAnswer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class LoincAnswerDeprecationImporter extends Importer
{
    protected static ?string $model = LoincAnswer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('list_id')
                ->label('LIST_ID')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?LoincAnswer
    {
        return LoincAnswer::withTrashed()
            ->where('list_id', $this->data['list_id'])
            ->first();
    }

    public function saveRecord(): void
    {
        LoincAnswer::where('list_id', $this->data['list_id'])->delete();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your loinc answer deprecation import has completed and '.number_format($import->successful_rows).' '.str('list')->plural($import->successful_rows).' deprecated.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
