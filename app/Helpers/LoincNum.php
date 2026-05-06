<?php

namespace App\Helpers;

use App\Models\Loinc;

class LoincNum
{
    public static function list(string $search): array
    {
        $response = [];

        if (strlen($search) < 3) {
            return $response;
        }

        $loincs = Loinc::where('component', 'like', "%{$search}%")
            ->orWhere('short_name', 'like', "%{$search}%")
            ->orWhere('example_units', 'like', "%{$search}%")
            ->orWhere('loinc_num', 'like', "%{$search}%")
            ->select([
                'loinc.loinc_num',
                'loinc.component',
                'loinc.example_units',
                'loinc.short_name',
            ])
            ->limit(30)
            ->get();

        foreach ($loincs as $loinc) {
            $longName = '';
            if ($loinc->component) {
                $longName .= $loinc->component;
            } else {
                $longName .= 'N/A';
            }
            $longName .= ' | Unit: ';
            if ($loinc->example_units) {
                $longName .= $loinc->example_units;
            } else {
                $longName .= 'N/A';
            }
            $longName .= ' | ';
            $longName .= $loinc->loinc_num;

            $response[$loinc->loinc_num] = $longName;
        }

        return $response;
    }
}
