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
            'listId' => $this->list_id,
            'stringId' => $this->string_id,
            'displayText' => $this->display_text,
            'name' => $this->name,
            'extCodeDisplayName' => $this->ext_code_display_name,
        ];
    }
}
