<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoincAnswerResource;
use App\Models\LoincAnswer;

class ShowLoincAnswerController extends Controller
{
    public function __invoke(LoincAnswer $loincAnswer): LoincAnswerResource
    {
        return new LoincAnswerResource($loincAnswer);
    }
}
