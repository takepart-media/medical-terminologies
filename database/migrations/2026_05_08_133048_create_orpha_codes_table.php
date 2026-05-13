<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orpha_codes', function (Blueprint $table) {
            $table->id();
            $table->string('orpha_code')->unique();
            $table->json('label')->nullable();
            $table->json('label_type')->nullable();
            $table->json('classification_level')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orpha_codes');
    }
};
