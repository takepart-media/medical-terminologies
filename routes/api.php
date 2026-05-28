<?php

use App\Http\Controllers\Api\SearchAtcController;
use App\Http\Controllers\Api\SearchIcd10Controller;
use App\Http\Controllers\Api\SearchLoincAnswerController;
use App\Http\Controllers\Api\SearchLoincController;
use App\Http\Controllers\Api\SearchOrphaController;
use App\Http\Controllers\Api\ShowAtcController;
use App\Http\Controllers\Api\ShowIcd10Controller;
use App\Http\Controllers\Api\ShowLoincAnswerController;
use App\Http\Controllers\Api\ShowLoincController;
use App\Http\Controllers\Api\ShowLoincVersionController;
use App\Http\Controllers\Api\ShowOrphaController;
use App\Http\Middleware\SetLocaleFromHeader;
use App\Http\Middleware\WhitelistIpAddresses;
use Illuminate\Support\Facades\Route;

Route::prefix('terminologies')
    ->middleware([
        WhitelistIpAddresses::class,
        SetLocaleFromHeader::class,
    ])
    ->group(function (): void {
        Route::get('loincs/version', ShowLoincVersionController::class);

        Route::get('loincs/search', SearchLoincController::class);
        Route::get('loinc-answers/search', SearchLoincAnswerController::class);
        Route::get('atcs/search', SearchAtcController::class);
        Route::get('orphas/search', SearchOrphaController::class);
        Route::get('icd10s/search', SearchIcd10Controller::class);

        Route::get('loincs/{loinc}', ShowLoincController::class);
        Route::get('loinc-answers/{loincAnswer}', ShowLoincAnswerController::class);
        Route::get('atcs/{atc}', ShowAtcController::class);
        Route::get('orphas/{orpha}', ShowOrphaController::class);
        Route::get('icd10s/{icd10}', ShowIcd10Controller::class);
    });
