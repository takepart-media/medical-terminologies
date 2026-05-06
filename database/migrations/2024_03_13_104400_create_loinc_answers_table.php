<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loinc_answers', function (Blueprint $table) {
            $table->id();
            $table->string('list_id')->nullable();
            $table->string('string_id')->nullable();
            $table->text('display_text')->nullable();
            $table->string('name')->nullable();
            $table->text('ext_code_display_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loinc_answers');
    }
};
