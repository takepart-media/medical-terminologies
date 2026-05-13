<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laravel\Scout\Searchable;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string|null $list_id
 * @property string|null $string_id
 * @property string|null $display_text
 * @property string|null $name
 * @property string|null $ext_code_display_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|LoincAnswer newModelQuery()
 * @method static Builder<static>|LoincAnswer newQuery()
 * @method static Builder<static>|LoincAnswer query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['list_id', 'string_id', 'display_text', 'name', 'ext_code_display_name'])]
class LoincAnswer extends Model
{
    use HasFactory;
    use HasTranslations;
    use Searchable;

    public const string TABLE = 'loinc_answers';

    protected $table = self::TABLE;

    public array $translatable = [
        'display_text', 'name', 'ext_code_display_name',
    ];

    public function toSearchableArray(): array
    {
        $searchables = [
            'list_id' => $this->list_id,
            'string_id' => $this->string_id,
        ];

        foreach (config('app.supported_locales') as $locale => $localeDef) {
            $searchables["display_text_{$locale}"] = $this->getTranslation('display_text', $locale, false);
            $searchables["name_{$locale}"] = $this->getTranslation('name', $locale, false);
            $searchables["ext_code_display_name_{$locale}"] = $this->getTranslation('ext_code_display_name', $locale, false);
        }

        return $searchables;
    }
}
