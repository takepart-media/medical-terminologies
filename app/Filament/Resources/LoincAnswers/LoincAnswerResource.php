<?php

namespace App\Filament\Resources\LoincAnswers;

use App\Filament\Actions\StreamingImportAction;
use App\Filament\Imports\LoincAnswerDeprecationImporter;
use App\Filament\Imports\LoincAnswerImporter;
use App\Filament\Resources\LoincAnswers\Pages\CreateLoincAnswer;
use App\Filament\Resources\LoincAnswers\Pages\EditLoincAnswer;
use App\Filament\Resources\LoincAnswers\Pages\ListLoincAnswers;
use App\Filament\Resources\LoincAnswers\Pages\ViewLoincAnswer;
use App\Models\LoincAnswer;
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

class LoincAnswerResource extends Resource
{
    protected static ?string $model = LoincAnswer::class;

    protected static string|null|BackedEnum $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Loinc';

    protected static ?string $modelLabel = 'LOINC Answer';

    protected static ?string $pluralModelLabel = 'Loinc Answers';

    protected static ?int $navigationSort = 92;

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
                TextColumn::make('list_id')->searchable(),
                TextColumn::make('string_id')->searchable(),
                TextColumn::make('display_text')->searchable(),
                TextColumn::make('name')->searchable(),
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
            ->headerActions([
                StreamingImportAction::make()
                    ->importer(LoincAnswerImporter::class)
                    ->chunkSize(1000),
                StreamingImportAction::make('deprecate')
                    ->label('Import deprecations')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('danger')
                    ->importer(LoincAnswerDeprecationImporter::class)
                    ->chunkSize(1000),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
                TextEntry::make('list_id')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Click on the loinc number for even more details')
                    ->label('List ID Detailed Information')
                    ->url(fn (LoincAnswer $record): string => url('https://loinc.org/'.$record->list_id))
                    ->openUrlInNewTab(),
                TextEntry::make('string_id')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Click on the loinc number for even more details')
                    ->label('String ID Detailes Information')
                    ->url(fn (LoincAnswer $record): string => url('https://loinc.org/'.$record->string_id))
                    ->openUrlInNewTab(),
                TextEntry::make("display_text.{$locale}")
                    ->label('Display Text')
                    ->state(fn (LoincAnswer $record): ?string => $record->getTranslation('display_text', $locale, false)),
                TextEntry::make("name.{$locale}")
                    ->label('Name')
                    ->state(fn (LoincAnswer $record): ?string => $record->getTranslation('name', $locale, false)),
                TextEntry::make("ext_code_display_name.{$locale}")
                    ->label('Ext Code Display Name')
                    ->state(fn (LoincAnswer $record): ?string => $record->getTranslation('ext_code_display_name', $locale, false)),
            ])->collapsible();
        }

        return $schema->components($localeSections);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoincAnswers::route('/'),
            'create' => CreateLoincAnswer::route('/create'),
            'edit' => EditLoincAnswer::route('/{record}/edit'),
            'view' => ViewLoincAnswer::route('/{record}'),
        ];
    }
}
