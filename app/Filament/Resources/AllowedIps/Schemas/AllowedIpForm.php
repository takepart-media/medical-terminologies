<?php

namespace App\Filament\Resources\AllowedIps\Schemas;

use Closure;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AllowedIpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('ip_address')
                    ->label('IP, CIDR, or hostname')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Examples: 203.0.113.5, 10.0.0.0/8, localhost, some-domain.local. Hostnames are resolved via DNS at request time (cached briefly).')
                    ->rules([
                        static fn (): Closure => static function (string $attribute, mixed $value, Closure $fail): void {
                            if (! is_string($value) || ! self::isValidEntry($value)) {
                                $fail('The :attribute must be a valid IP address, CIDR range, or hostname.');
                            }
                        },
                    ]),
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    private static function isValidEntry(string $value): bool
    {
        if (str_contains($value, '/')) {
            [$ip, $prefix] = explode('/', $value, 2);

            if (! ctype_digit($prefix)) {
                return false;
            }

            $prefix = (int) $prefix;

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
                return $prefix >= 0 && $prefix <= 32;
            }

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
                return $prefix >= 0 && $prefix <= 128;
            }

            return false;
        }

        if (filter_var($value, FILTER_VALIDATE_IP) !== false) {
            return true;
        }

        return self::isValidHostname($value);
    }

    private static function isValidHostname(string $value): bool
    {
        if ($value === '' || strlen($value) > 253) {
            return false;
        }

        return (bool) preg_match(
            '/^(?=.{1,253}$)(?:(?!-)[A-Za-z0-9-]{1,63}(?<!-)\.)*(?!-)[A-Za-z0-9-]{1,63}(?<!-)$/',
            $value,
        );
    }
}
