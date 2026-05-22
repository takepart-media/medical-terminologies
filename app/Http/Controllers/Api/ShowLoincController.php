<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoincResource;
use App\Models\Loinc;

class ShowLoincController extends Controller
{
    public function __invoke(Loinc $loinc): LoincResource
    {
        return new LoincResource($loinc);
    }
}
