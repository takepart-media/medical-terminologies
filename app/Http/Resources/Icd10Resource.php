<?php

namespace App\Http\Resources;

use App\Models\Icd10;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Icd10
 */
class Icd10Resource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'icd10Code' => $this->icd10_code,
            'relatedText' => $this->related_text,
        ];
    }
}
