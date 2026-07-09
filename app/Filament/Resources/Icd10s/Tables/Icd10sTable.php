<?php

namespace App\Filament\Resources\Icd10s\Tables;

use App\Filament\Actions\StreamingImportAction;
use App\Filament\Imports\Icd10DeprecationImporter;
use App\Filament\Imports\Icd10Importer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class Icd10sTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icd10_code')
                    ->searchable(),
                TextColumn::make('related_text')
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column): ?string => strlen($column->getState() ?? '') > $column->getCharacterLimit() ? $column->getState() : null)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                StreamingImportAction::make()
                    ->importer(Icd10Importer::class)
                    ->chunkSize(1000),
                StreamingImportAction::make('deprecate')
                    ->label('Import deprecations')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('danger')
                    ->importer(Icd10DeprecationImporter::class)
                    ->chunkSize(1000),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
