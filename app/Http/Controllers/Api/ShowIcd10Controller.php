<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Icd10Resource;
use App\Models\Icd10;

class ShowIcd10Controller extends Controller
{
    public function __invoke(Icd10 $icd10): Icd10Resource
    {
        return new Icd10Resource($icd10);
    }
}
