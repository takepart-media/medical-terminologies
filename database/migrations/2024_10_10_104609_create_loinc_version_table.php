<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loinc_version', function (Blueprint $table) {
            $table->string('version');
        });

        DB::table('loinc_version')->insert([
            'version' => '2.77',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loinc_version');
    }
};
