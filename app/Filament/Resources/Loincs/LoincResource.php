<?php

namespace App\Filament\Resources\Loincs;

use App\Filament\Imports\LoincImporter;
use App\Filament\Resources\Loincs\Pages\CreateLoinc;
use App\Filament\Resources\Loincs\Pages\EditLoinc;
use App\Filament\Resources\Loincs\Pages\ListLoincs;
use App\Filament\Resources\Loincs\Pages\ViewLoinc;
use App\Models\Loinc;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoincResource extends Resource
{
    protected static ?string $model = Loinc::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Terminologies';

    protected static ?string $modelLabel = 'LOINC';

    protected static ?string $pluralModelLabel = 'LOINC';

    protected static ?int $navigationSort = 91;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loinc_num')->searchable()->sortable(),
                TextColumn::make('component')->searchable()->sortable(),
                TextColumn::make('example_units')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(LoincImporter::class)
                    ->chunkSize(1000),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextEntry::make('loinc_num')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Click on the loinc number for even more details')
                        ->label('Detailed Information')
                        ->url(fn (Loinc $record): string => url('https://loinc.org/'.$record->loinc_num))
                        ->openUrlInNewTab(),
                    TextEntry::make('component')->label('Component'),
                    TextEntry::make('property')->label('Property'),
                    TextEntry::make('system')->label('System'),
                    TextEntry::make('short_name')->label('Short name'),
                    TextEntry::make('related_names_2')->label('Related Names')->separator(';'),
                    TextEntry::make('display_name')->label('Display Name'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoincs::route('/'),
            'create' => CreateLoinc::route('/create'),
            'edit' => EditLoinc::route('/{record}/edit'),
            'view' => ViewLoinc::route('/{record}'),
        ];
    }
}
