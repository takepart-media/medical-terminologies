<?php

namespace App\Filament\Resources\AllowedIps;

use App\Filament\Resources\AllowedIps\Pages\CreateAllowedIp;
use App\Filament\Resources\AllowedIps\Pages\EditAllowedIp;
use App\Filament\Resources\AllowedIps\Pages\ListAllowedIps;
use App\Filament\Resources\AllowedIps\Pages\ViewAllowedIp;
use App\Filament\Resources\AllowedIps\Schemas\AllowedIpForm;
use App\Filament\Resources\AllowedIps\Schemas\AllowedIpInfolist;
use App\Filament\Resources\AllowedIps\Tables\AllowedIpsTable;
use App\Models\AllowedIp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AllowedIpResource extends Resource
{
    protected static ?string $model = AllowedIp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?string $pluralModelLabel = 'Allowed IPs';

    public static function form(Schema $schema): Schema
    {
        return AllowedIpForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AllowedIpInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AllowedIpsTable::configure($table);
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
            'index' => ListAllowedIps::route('/'),
            'create' => CreateAllowedIp::route('/create'),
            'view' => ViewAllowedIp::route('/{record}'),
            'edit' => EditAllowedIp::route('/{record}/edit'),
        ];
    }
}
