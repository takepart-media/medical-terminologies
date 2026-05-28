<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ShowLoincVersionController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $version = DB::table('loinc_version')->value('version');

        return response()->json([
            'data' => [
                'version' => $version,
            ],
        ]);
    }
}
