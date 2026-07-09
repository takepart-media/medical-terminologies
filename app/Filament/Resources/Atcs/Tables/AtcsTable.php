<?php

namespace App\Filament\Resources\Atcs\Tables;

use App\Filament\Actions\StreamingImportAction;
use App\Filament\Imports\AtcDeprecationImporter;
use App\Filament\Imports\AtcImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AtcsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('atc_code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('unit')
                    ->searchable(),
                TextColumn::make('route')
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
                    ->importer(AtcImporter::class)
                    ->chunkSize(1000),
                StreamingImportAction::make('deprecate')
                    ->label('Import deprecations')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('danger')
                    ->importer(AtcDeprecationImporter::class)
                    ->chunkSize(1000),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
