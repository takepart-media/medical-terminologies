<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Translatable\HasTranslations;

class Atc extends Model
{
    use HasTranslations;
    use Searchable;

    public array $translatable = [
        'name', 'ddd_info', 'note',
    ];

    public const string TABLE = 'atc_codes';

    protected $table = self::TABLE;

    protected $fillable = [
        'atc_code',
        'name',
        'ddd_info',
        'note',
        'unit',
        'route',
    ];

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
