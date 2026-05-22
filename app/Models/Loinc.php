<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laravel\Scout\Searchable;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $loinc_num
 * @property string|null $component
 * @property string|null $property
 * @property string|null $system
 * @property string|null $related_names_2
 * @property string|null $short_name
 * @property string|null $example_units
 * @property string|null $example_ucum_units
 * @property string|null $display_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Loinc newModelQuery()
 * @method static Builder<static>|Loinc newQuery()
 * @method static Builder<static>|Loinc query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['loinc_num', 'component', 'property', 'system', 'related_names_2', 'short_name', 'example_units', 'example_ucum_units', 'display_name'])]
class Loinc extends Model
{
    use HasFactory;
    use HasTranslations;
    use Searchable;
    use SoftDeletes;

    public const string TABLE = 'loinc_codes';

    protected $table = self::TABLE;

    public array $translatable = [
        'component', 'related_names_2', 'short_name', 'display_name',
    ];

    public function toSearchableArray(): array
    {
        $searchables = [
            'loinc_num' => $this->loinc_num,
            'property' => $this->property,
            'system' => $this->system,
            'example_units' => $this->example_units,
            'example_ucum_units' => $this->example_ucum_units,
        ];

        foreach (config('app.supported_locales') as $locale => $localeDef) {
            $searchables["component_{$locale}"] = $this->getTranslation('component', $locale, false);
            $searchables["related_names_2_{$locale}"] = $this->getTranslation('related_names_2', $locale, false);
            $searchables["short_name_{$locale}"] = $this->getTranslation('short_name', $locale, false);
            $searchables["display_name_{$locale}"] = $this->getTranslation('display_name', $locale, false);
        }

        return $searchables;
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return ctype_digit((string) $value)
            ? $this->newQuery()->whereKey($value)->firstOrFail()
            : $this->newQuery()->where('loinc_num', $value)->firstOrFail();
    }
}
