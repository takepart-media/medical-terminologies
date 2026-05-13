<?php

namespace App\Filament\Resources\Icd10s;

use App\Filament\Resources\Icd10s\Pages\CreateIcd10;
use App\Filament\Resources\Icd10s\Pages\EditIcd10;
use App\Filament\Resources\Icd10s\Pages\ListIcd10s;
use App\Filament\Resources\Icd10s\Pages\ViewIcd10;
use App\Filament\Resources\Icd10s\Schemas\Icd10Form;
use App\Filament\Resources\Icd10s\Tables\Icd10sTable;
use App\Models\Icd10;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class Icd10Resource extends Resource
{
    protected static ?string $model = Icd10::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Icd10 Code';

    protected static ?string $pluralModelLabel = 'Icd10 Codes';

    protected static string|null|UnitEnum $navigationGroup = 'Diseases / Conditions';

    public static function form(Schema $schema): Schema
    {
        return Icd10Form::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Icd10sTable::configure($table);
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

    public static function getPages(): array
    {
        return [
            'index' => ListIcd10s::route('/'),
            'create' => CreateIcd10::route('/create'),
            'edit' => EditIcd10::route('/{record}/edit'),
            'view' => ViewIcd10::route('/{record}'),
        ];
    }
}
