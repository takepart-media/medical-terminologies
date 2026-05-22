<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrphaResource;
use App\Models\Orpha;

class ShowOrphaController extends Controller
{
    public function __invoke(Orpha $orpha): OrphaResource
    {
        return new OrphaResource($orpha);
    }
}
