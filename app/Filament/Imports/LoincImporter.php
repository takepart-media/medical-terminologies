<?php

namespace App\Filament\Imports;

use App\Models\Loinc;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class LoincImporter extends Importer
{
    protected static ?string $model = Loinc::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('loinc_num')
                ->label('LOINC_NUM')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('component')
                ->label('COMPONENT')
                ->rules(['max:65535']),
            ImportColumn::make('property')
                ->label('PROPERTY')
                ->rules(['max:255']),
            ImportColumn::make('system')
                ->label('SYSTEM')
                ->rules(['max:255']),
            ImportColumn::make('related_names_2')
                ->label('RELATEDNAMES2')
                ->rules(['max:65535']),
            ImportColumn::make('short_name')
                ->label('SHORTNAME')
                ->rules(['max:255']),
            ImportColumn::make('example_units')
                ->label('EXAMPLE_UNITS')
                ->rules(['max:255']),
            ImportColumn::make('example_ucum_units')
                ->label('EXAMPLE_UCUM_UNITS')
                ->rules(['max:255']),
            ImportColumn::make('display_name')
                ->label('DisplayName')
                ->rules(['max:65535']),
        ];
    }

    public function resolveRecord(): ?Loinc
    {
        return Loinc::firstOrNew([
            'loinc_num' => $this->data['loinc_num'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your loinc import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
