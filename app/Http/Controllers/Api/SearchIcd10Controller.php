<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchIcd10Request;
use App\Http\Resources\Icd10Resource;
use App\Models\Icd10;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SearchIcd10Controller extends Controller
{
    public function __invoke(SearchIcd10Request $request): AnonymousResourceCollection
    {
        app()->setLocale($request->input('locale', config('app.locale')));
        $locale = app()->getLocale();

        $results = Icd10::search($request->search)
            ->options([
                'query' => [
                    'multi_match' => [
                        'query' => $request->search,
                        'fields' => ["related_text_{$locale}"],
                    ],
                ],
            ])
            ->paginate($request->integer('per_page', 25) ?? 25);

        return Icd10Resource::collection($results);
    }
}
