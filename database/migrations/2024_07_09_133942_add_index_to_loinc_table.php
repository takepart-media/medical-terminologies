<?php

use App\Models\Loinc;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Loinc::TABLE, function (Blueprint $table) {
            $table->index('loinc_num');
        });
    }

    public function down(): void
    {
        Schema::table(Loinc::TABLE, function (Blueprint $table) {
            $table->dropIndex(['loinc_num']);
        });
    }
};
