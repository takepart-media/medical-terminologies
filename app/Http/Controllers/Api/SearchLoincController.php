<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchLoincRequest;
use App\Http\Resources\LoincResource;
use App\Models\Loinc;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SearchLoincController extends Controller
{
    public function __invoke(SearchLoincRequest $request): AnonymousResourceCollection
    {
        app()->setLocale($request->input('locale', config('app.locale')));

        $results = Loinc::search($request->search)
            ->paginate($request->integer('per_page', 25) ?? 25);

        return LoincResource::collection($results);
    }
}
