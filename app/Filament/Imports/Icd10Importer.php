<?php

namespace App\Filament\Imports;

use App\Models\Icd10;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;

class Icd10Importer extends Importer
{
    protected static ?string $model = Icd10::class;

    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('locale')
                ->label('Locale')
                ->options(config('app.supported_locales'))
                ->default(config('app.locale'))
                ->required(),
        ];
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('icd10_code')
                ->label('ICD10_CODE')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('related_text')
                ->label('RELATED_TEXT')
                ->rules(['max:65535']),
        ];
    }

    public function resolveRecord(): ?Icd10
    {
        app()->setLocale($this->options['locale']);

        $record = Icd10::withTrashed()->firstOrNew([
            'icd10_code' => $this->data['icd10_code'],
        ]);

        if ($record->trashed()) {
            $record->restore();
        }

        return $record;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your icd10 import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
