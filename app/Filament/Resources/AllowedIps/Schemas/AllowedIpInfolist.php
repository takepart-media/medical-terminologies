<?php

namespace App\Filament\Resources\AllowedIps\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AllowedIpInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('ip_address')->label('IP / CIDR'),
                TextEntry::make('label'),
                TextEntry::make('notes')->placeholder('—'),
                IconEntry::make('is_active')->boolean()->label('Active'),
                TextEntry::make('created_at')->dateTime(),
                TextEntry::make('updated_at')->dateTime(),
            ]);
    }
}
