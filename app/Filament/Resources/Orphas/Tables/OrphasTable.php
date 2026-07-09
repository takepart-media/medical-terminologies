<?php

namespace App\Filament\Resources\Orphas\Tables;

use App\Filament\Actions\StreamingImportAction;
use App\Filament\Imports\OrphaDeprecationImporter;
use App\Filament\Imports\OrphaImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class OrphasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orpha_code')
                    ->searchable(),
                TextColumn::make('label')
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column): ?string => strlen($column->getState() ?? '') > $column->getCharacterLimit() ? $column->getState() : null)
                    ->searchable(),
                TextColumn::make('label_type')
                    ->searchable(),
                TextColumn::make('classification_level')
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
                    ->importer(OrphaImporter::class)
                    ->chunkSize(1000),
                StreamingImportAction::make('deprecate')
                    ->label('Import deprecations')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('danger')
                    ->importer(OrphaDeprecationImporter::class)
                    ->chunkSize(1000),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
