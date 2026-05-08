<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchLoincAnswerRequest;
use App\Http\Resources\LoincAnswerResource;
use App\Models\LoincAnswer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SearchLoincAnswerController extends Controller
{
    public function __invoke(SearchLoincAnswerRequest $request): AnonymousResourceCollection
    {
        $results = LoincAnswer::search($request->search)
            ->paginate($request->integer('per_page', 25) ?? 25);

        return LoincAnswerResource::collection($results);
    }
}
