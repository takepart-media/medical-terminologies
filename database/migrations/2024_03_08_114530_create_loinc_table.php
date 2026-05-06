<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loinc', function (Blueprint $table) {
            $table->id();
            $table->string('loinc_num')->unique();
            $table->text('component')->nullable();
            $table->string('property')->nullable();
            $table->string('system')->nullable();
            $table->text('related_names_2')->nullable();
            $table->string('short_name')->nullable();
            $table->string('example_units')->nullable();
            $table->string('example_ucum_units')->nullable();
            $table->text('display_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loinc');
    }
};
