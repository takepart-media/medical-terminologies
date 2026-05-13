<?php

namespace App\Filament\Imports;

use App\Models\LoincAnswer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;

class LoincAnswerImporter extends Importer
{
    protected static ?string $model = LoincAnswer::class;

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
            ImportColumn::make('list_id')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('string_id')
                ->rules(['max:255']),
            ImportColumn::make('display_text')
                ->rules(['max:65535']),
            ImportColumn::make('name')
                ->rules(['max:255']),
            ImportColumn::make('ext_code_display_name')
                ->rules(['max:65535']),
        ];
    }

    public function resolveRecord(): ?LoincAnswer
    {
        app()->setLocale($this->options['locale']);

        return new LoincAnswer;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your loinc answer import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
