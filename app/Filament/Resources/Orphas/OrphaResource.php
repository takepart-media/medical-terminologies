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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrphaResource extends Resource
{
    protected static ?string $model = Orpha::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Orpha Code';

    protected static ?string $pluralModelLabel = 'Orpha Codes';

    protected static string|null|UnitEnum $navigationGroup = 'ORPHA';

    public static function form(Schema $schema): Schema
    {
        return OrphaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrphasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
