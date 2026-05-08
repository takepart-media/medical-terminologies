<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchAtcRequest;
use App\Http\Resources\AtcResource;
use App\Models\Atc;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SearchAtcController extends Controller
{
    public function __invoke(SearchAtcRequest $request): AnonymousResourceCollection
    {
        $locale = app()->getLocale();

        $results = Atc::search($request->search)
            ->options([
                'query' => [
                    'multi_match' => [
                        'query' => $request->search,
                        'fields' => ["name_{$locale}", "ddd_info_{$locale}", "note{$locale}"],
                    ],
                ],
            ])
            ->paginate($request->integer('per_page', 25) ?? 25);

        return AtcResource::collection($results);
    }
}
