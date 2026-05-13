<?php

namespace App\Filament\Imports;

use App\Models\Orpha;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;

class OrphaImporter extends Importer
{
    protected static ?string $model = Orpha::class;

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
            ImportColumn::make('orpha_code')
                ->label('ORPHA_CODE')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('label')
                ->label('LABEL')
                ->rules(['max:65535']),
            ImportColumn::make('label_type')
                ->label('LABEL_TYPE')
                ->rules(['max:255']),
            ImportColumn::make('classification_level')
                ->label('CLASSIFICATION_LEVEL')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?Orpha
    {
        app()->setLocale($this->options['locale']);

        $record = Orpha::withTrashed()->firstOrNew([
            'orpha_code' => $this->data['orpha_code'],
        ]);

        if ($record->trashed()) {
            $record->restore();
        }

        return $record;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your orpha import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
