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
 * @property string $atc_code
 * @property string|null $name
 * @property string|null $ddd_info
 * @property string|null $note
 * @property string|null $unit
 * @property string|null $route
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Atc newModelQuery()
 * @method static Builder<static>|Atc newQuery()
 * @method static Builder<static>|Atc query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['atc_code', 'name', 'ddd_info', 'note', 'unit', 'route'])]
class Atc extends Model
{
    use HasTranslations;
    use Searchable;
    use SoftDeletes;

    public array $translatable = [
        'name', 'ddd_info', 'note',
    ];

    public const string TABLE = 'atc_codes';

    protected $table = self::TABLE;

    public function toSearchableArray(): array
    {
        $searchables = [
            'atc_code' => $this->atc_code,
        ];

        foreach (config('app.supported_locales') as $locale => $localeDef) {
            $searchables["name_{$locale}"] = $this->getTranslation('name', $locale, false);
            $searchables["ddd_info_{$locale}"] = $this->getTranslation('ddd_info', $locale, false);
            $searchables["note_{$locale}"] = $this->getTranslation('note', $locale, false);
        }

        return $searchables;
    }
}
