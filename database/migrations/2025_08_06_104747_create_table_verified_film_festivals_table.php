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
        Schema::create('table_verified_film_festivals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verified_film_id')->constrained()->cascadeOnDelete();
            $table->foreignId('festivals_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_verified_film_festivals');
    }
};
