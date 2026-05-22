<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laravel\Scout\Searchable;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $icd10_code
 * @property string|null $related_text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Icd10 newModelQuery()
 * @method static Builder<static>|Icd10 newQuery()
 * @method static Builder<static>|Icd10 query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['icd10_code', 'related_text'])]
class Icd10 extends Model
{
    use HasTranslations;
    use Searchable;
    use SoftDeletes;

    public const string TABLE = 'icd10_codes';

    protected $table = self::TABLE;

    public array $translatable = [
        'related_text',
    ];

    public function toSearchableArray(): array
    {
        $searchables = [
            'icd10_code' => $this->icd10_code,
        ];

        foreach (config('app.supported_locales') as $locale => $localeDef) {
            $searchables["related_text_{$locale}"] = $this->getTranslation('related_text', $locale, false);
        }

        return $searchables;
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return ctype_digit((string) $value)
            ? $this->newQuery()->whereKey($value)->firstOrFail()
            : $this->newQuery()->where('icd10_code', $value)->firstOrFail();
    }
}
