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
            'id' => $this->id,
            'loinc_num' => $this->loinc_num,
            'component' => $this->component,
            'property' => $this->property,
            'system' => $this->system,
            'short_name' => $this->short_name,
            'display_name' => $this->display_name,
            'related_names_2' => $this->related_names_2,
            'example_units' => $this->example_units,
            'example_ucum_units' => $this->example_ucum_units,
        ];
    }
}
