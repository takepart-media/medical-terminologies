<?php

namespace App\Filament\Resources\Atcs;

use App\Filament\Resources\Atcs\Pages\CreateAtc;
use App\Filament\Resources\Atcs\Pages\EditAtc;
use App\Filament\Resources\Atcs\Pages\ListAtcs;
use App\Filament\Resources\Atcs\Pages\ViewAtc;
use App\Filament\Resources\Atcs\Schemas\AtcForm;
use App\Filament\Resources\Atcs\Tables\AtcsTable;
use App\Models\Atc;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AtcResource extends Resource
{
    protected static ?string $model = Atc::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Medications';

    protected static ?string $recordTitleAttribute = 'Atc';

    protected static ?string $pluralModelLabel = 'Atc Codes';

    public static function form(Schema $schema): Schema
    {
        return AtcForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AtcsTable::configure($table);
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
                TextEntry::make('atc_code')->label('ATC Code'),
                TextEntry::make("name.{$locale}")
                    ->label('Name')
                    ->state(fn (Atc $record): ?string => $record->getTranslation('name', $locale, false)),
                TextEntry::make("ddd_info.{$locale}")
                    ->label('DDD Info')
                    ->state(fn (Atc $record): ?string => $record->getTranslation('ddd_info', $locale, false)),
                TextEntry::make('unit')->label('Unit'),
                TextEntry::make('route')->label('Route'),
                TextEntry::make("note.{$locale}")
                    ->label('Note')
                    ->state(fn (Atc $record): ?string => $record->getTranslation('note', $locale, false)),
            ])->collapsible();
        }

        return $schema->components($localeSections);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAtcs::route('/'),
            'create' => CreateAtc::route('/create'),
            'edit' => EditAtc::route('/{record}/edit'),
            'view' => ViewAtc::route('/{record}'),
        ];
    }
}
