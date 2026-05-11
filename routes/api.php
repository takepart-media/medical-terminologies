<?php

use App\Http\Controllers\Api\SearchAtcController;
use App\Http\Controllers\Api\SearchLoincAnswerController;
use App\Http\Controllers\Api\SearchLoincController;
use App\Http\Controllers\Api\SearchOrphaController;
use Illuminate\Support\Facades\Route;

Route::prefix('terminologies')->group(function (): void {
    Route::get('loincs/search', SearchLoincController::class);
    Route::get('loinc-answers/search', SearchLoincAnswerController::class);
    Route::get('atcs/search', SearchAtcController::class);
    Route::get('orphas/search', SearchOrphaController::class);
});
