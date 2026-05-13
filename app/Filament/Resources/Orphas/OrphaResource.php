<?php

namespace App\Filament\Resources\Orphas;

use App\Filament\Resources\Orphas\Pages\CreateOrpha;
use App\Filament\Resources\Orphas\Pages\EditOrpha;
use App\Filament\Resources\Orphas\Pages\ListOrphas;
use App\Filament\Resources\Orphas\Pages\ViewOrpha;
use App\Filament\Resources\Orphas\Schemas\OrphaForm;
use App\Filament\Resources\Orphas\Tables\OrphasTable;
use App\Models\Orpha;
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

class OrphaResource extends Resource
{
    protected static ?string $model = Orpha::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Orpha Code';

    protected static ?string $pluralModelLabel = 'Orpha Codes';

    protected static string|null|UnitEnum $navigationGroup = 'Diseases / Conditions';

    public static function form(Schema $schema): Schema
    {
        return OrphaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrphasTable::configure($table);
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
                TextEntry::make('orpha_code')->label('Orpha Code'),
                TextEntry::make("label.{$locale}")
                    ->label('Label')
                    ->state(fn (Orpha $record): ?string => $record->getTranslation('label', $locale, false)),
                TextEntry::make("label_type.{$locale}")
                    ->label('Label Type')
                    ->state(fn (Orpha $record): ?string => $record->getTranslation('label_type', $locale, false)),
                TextEntry::make("classification_level.{$locale}")
                    ->label('Classification Level')
                    ->state(fn (Orpha $record): ?string => $record->getTranslation('classification_level', $locale, false)),
            ])->collapsible();
        }

        return $schema->components($localeSections);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrphas::route('/'),
            'create' => CreateOrpha::route('/create'),
            'edit' => EditOrpha::route('/{record}/edit'),
            'view' => ViewOrpha::route('/{record}'),
        ];
    }
}
