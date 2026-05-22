<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchOrphaRequest;
use App\Http\Resources\OrphaResource;
use App\Models\Orpha;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SearchOrphaController extends Controller
{
    public function __invoke(SearchOrphaRequest $request): AnonymousResourceCollection
    {
        $locale = app()->getLocale();

        $results = Orpha::search($request->search)
            ->options([
                'query' => [
                    'multi_match' => [
                        'query' => $request->search,
                        'fields' => ["label_{$locale}", "label_type_{$locale}", "classification_level_{$locale}"],
                    ],
                ],
            ])
            ->paginate($request->integer('per_page', 25) ?? 25);

        return OrphaResource::collection($results);
    }
}
