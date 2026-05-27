<?php

namespace App\Models;

use Database\Factories\AllowedIpFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

/**
 * @property int $id
 * @property string $ip_address
 * @property string $label
 * @property string|null $notes
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|AllowedIp newModelQuery()
 * @method static Builder<static>|AllowedIp newQuery()
 * @method static Builder<static>|AllowedIp query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['ip_address', 'label', 'notes', 'is_active'])]
class AllowedIp extends Model
{
    /** @use HasFactory<AllowedIpFactory> */
    use HasFactory;

    public const string CACHE_KEY = 'allowed_ips.active';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Returns the list of allowed IPs/CIDR ranges, with any hostname entries
     * expanded to their resolved IPs via DNS. Cached briefly so request-time
     * lookups stay cheap while still picking up DNS changes without an
     * admin re-save.
     *
     * @return array<int, string>
     */
    public static function activeList(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            Config::integer('whitelist.cache_ttl'),
            fn (): array => self::buildResolvedList(),
        );
    }

    /**
     * @return array<int, string>
     */
    protected static function buildResolvedList(): array
    {
        $entries = self::query()
            ->where('is_active', true)
            ->pluck('ip_address')
            ->all();

        $resolved = [];

        foreach ($entries as $entry) {
            if (self::looksLikeIpOrCidr($entry)) {
                $resolved[] = $entry;

                continue;
            }

            $ips = @gethostbynamel($entry);

            if ($ips !== false) {
                foreach ($ips as $ip) {
                    $resolved[] = $ip;
                }
            }
        }

        return array_values(array_unique($resolved));
    }

    protected static function looksLikeIpOrCidr(string $value): bool
    {
        if (str_contains($value, '/')) {
            [$ip] = explode('/', $value, 2);

            return filter_var($ip, FILTER_VALIDATE_IP) !== false;
        }

        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    protected static function booted(): void
    {
        $flush = static fn (): bool => Cache::forget(self::CACHE_KEY);

        static::saved($flush);
        static::deleted($flush);
    }
}
