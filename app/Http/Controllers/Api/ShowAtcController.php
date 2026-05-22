<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AtcResource;
use App\Models\Atc;

class ShowAtcController extends Controller
{
    public function __invoke(Atc $atc): AtcResource
    {
        return new AtcResource($atc);
    }
}
