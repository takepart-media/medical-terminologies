<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Atc extends Model
{
    use HasTranslations;

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
}
