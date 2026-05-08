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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AtcResource extends Resource
{
    protected static ?string $model = Atc::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Atc';

    public static function form(Schema $schema): Schema
    {
        return AtcForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AtcsTable::configure($table);
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
            'index' => ListAtcs::route('/'),
            'create' => CreateAtc::route('/create'),
            'edit' => EditAtc::route('/{record}/edit'),
            'view' => ViewAtc::route('/{record}'),
        ];
    }
}
