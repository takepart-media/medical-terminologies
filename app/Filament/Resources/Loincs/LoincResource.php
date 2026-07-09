<?php

namespace App\Filament\Resources\Loincs;

use App\Filament\Actions\StreamingImportAction;
use App\Filament\Imports\LoincDeprecationImporter;
use App\Filament\Imports\LoincImporter;
use App\Filament\Resources\Loincs\Pages\CreateLoinc;
use App\Filament\Resources\Loincs\Pages\EditLoinc;
use App\Filament\Resources\Loincs\Pages\ListLoincs;
use App\Filament\Resources\Loincs\Pages\ViewLoinc;
use App\Models\Loinc;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class LoincResource extends Resource
{
    protected static ?string $model = Loinc::class;

    protected static string|null|BackedEnum $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Loinc';

    protected static ?string $modelLabel = 'LOINC';

    protected static ?string $pluralModelLabel = 'Loinc Codes';

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
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                StreamingImportAction::make()
                    ->importer(LoincImporter::class)
                    ->chunkSize(1000),
                StreamingImportAction::make('deprecate')
                    ->label('Import deprecations')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('danger')
                    ->importer(LoincDeprecationImporter::class)
                    ->chunkSize(1000),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        $localeSections = [];

        foreach (config('app.supported_locales') as $locale => $localeLabel) {
            $localeSections[] = Section::make($localeLabel)->schema([
                TextEntry::make('loinc_num')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Click on the loinc number for even more details')
                    ->label('Detailed Information')
                    ->url(fn (Loinc $record): string => url('https://loinc.org/'.$record->loinc_num))
                    ->openUrlInNewTab(),
                TextEntry::make("component.{$locale}")
                    ->label('Component')
                    ->state(fn (Loinc $record): ?string => $record->getTranslation('component', $locale, false)),
                TextEntry::make('property')->label('Property'),
                TextEntry::make('system')->label('System'),
                TextEntry::make("short_name.{$locale}")
                    ->label('Short name')
                    ->state(fn (Loinc $record): ?string => $record->getTranslation('short_name', $locale, false)),
                TextEntry::make("related_names_2.{$locale}")
                    ->label('Related Names')
                    ->separator(';')
                    ->state(fn (Loinc $record): ?string => $record->getTranslation('related_names_2', $locale, false)),
                TextEntry::make('example_units')->label('Example Units'),
                TextEntry::make('example_ucum_units')->label('Example UCUM Units'),
                TextEntry::make("display_name.{$locale}")
                    ->label('Display Name')
                    ->state(fn (Loinc $record): ?string => $record->getTranslation('display_name', $locale, false)),
            ])->collapsible();
        }

        return $schema->components($localeSections);
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
