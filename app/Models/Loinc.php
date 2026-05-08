<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
class Loinc extends Model
{
    use HasFactory;

    public const string TABLE = 'loinc_codes';

    protected $table = self::TABLE;

    protected $fillable = [
        'loinc_num',
        'component',
        'property',
        'system',
        'related_names_2',
        'short_name',
        'example_units',
        'example_ucum_units',
        'display_name',
    ];
}
