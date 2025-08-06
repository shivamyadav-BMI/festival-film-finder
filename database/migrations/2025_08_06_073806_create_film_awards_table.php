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
        Schema::create('film_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verified_film_id')->constrained('films')->onDelete('cascade');
            $table->foreignId('festival_id')->constrained('festivals')->onDelete('cascade');
            $table->year('award_year');
            $table->string('award_name');      // e.g. "Golden Lion"
            $table->string('award_category')->nullable(); // e.g. "Best Film"
            $table->enum('result', ['Winner', 'Nominee'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film_awards');
    }
};
