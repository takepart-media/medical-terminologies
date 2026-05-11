<?php

namespace App\Http\Resources;

use App\Models\Atc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Atc
 */
class AtcResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'atcCode' => $this->atc_code,
            'name' => $this->name,
            'dddInfo' => $this->ddd_info,
            'note' => $this->note,
            'unit' => $this->unit,
            'route' => $this->route,
        ];
    }
}
