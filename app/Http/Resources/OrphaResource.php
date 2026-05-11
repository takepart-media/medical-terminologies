<?php

namespace App\Http\Resources;

use App\Models\Orpha;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Orpha
 */
class OrphaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'orphaCode' => $this->orpha_code,
            'label' => $this->label,
            'labelType' => $this->label_type,
            'classificationLevel' => $this->classification_level,
        ];
    }
}
