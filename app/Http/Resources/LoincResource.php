<?php

namespace App\Http\Resources;

use App\Models\Loinc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Loinc
 */
class LoincResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'loincNum' => $this->loinc_num,
            'component' => $this->component,
            'property' => $this->property,
            'system' => $this->system,
            'shortName' => $this->short_name,
            'displayName' => $this->display_name,
            'relatedNames2' => $this->related_names_2,
            'exampleUnits' => $this->example_units,
            'exampleUcumUnits' => $this->example_ucum_units,
        ];
    }
}
