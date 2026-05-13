<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loinc_codes', function (Blueprint $table) {
            $table->id();
            $table->string('loinc_num')->unique();
            $table->json('component')->nullable();
            $table->string('property')->nullable();
            $table->string('system')->nullable();
            $table->json('related_names_2')->nullable();
            $table->json('short_name')->nullable();
            $table->string('example_units')->nullable();
            $table->string('example_ucum_units')->nullable();
            $table->json('display_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loinc_codes');
    }
};
