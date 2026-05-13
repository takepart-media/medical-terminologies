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
 * @property string $orpha_code
 * @property string|null $label
 * @property string|null $label_type
 * @property string|null $classification_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Orpha newModelQuery()
 * @method static Builder<static>|Orpha newQuery()
 * @method static Builder<static>|Orpha query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['orpha_code', 'label', 'label_type', 'classification_level'])]
class Orpha extends Model
{
    use HasTranslations;
    use Searchable;
    use SoftDeletes;

    public const string TABLE = 'orpha_codes';

    protected $table = self::TABLE;

    public array $translatable = [
        'label', 'label_type', 'classification_level',
    ];

    public function toSearchableArray(): array
    {
        $searchables = [
            'orpha_code' => $this->orpha_code,
        ];

        foreach (config('app.supported_locales') as $locale => $localeDef) {
            $searchables["label_{$locale}"] = $this->getTranslation('label', $locale, false);
            $searchables["label_type_{$locale}"] = $this->getTranslation('label_type', $locale, false);
            $searchables["classification_level_{$locale}"] = $this->getTranslation('classification_level', $locale, false);
        }

        return $searchables;
    }
}
