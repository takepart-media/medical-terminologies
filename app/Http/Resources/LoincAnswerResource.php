<?php

namespace App\Http\Resources;

use App\Models\LoincAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LoincAnswer
 */
class LoincAnswerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'list_id' => $this->list_id,
            'string_id' => $this->string_id,
            'display_text' => $this->display_text,
            'name' => $this->name,
            'ext_code_display_name' => $this->ext_code_display_name,
        ];
    }
}
