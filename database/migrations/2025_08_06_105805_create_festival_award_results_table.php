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
        Schema::create('festival_award_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_award_id')->constrained()->onDelete('cascade');
            $table->foreignId('verified_film_id')->constrained()->onDelete('cascade');
            $table->foreignId('festival_edition_id')->constrained()->onDelete('cascade');
            $table->enum('result', ['Winner', 'Nominee']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festival_award_results');
    }
};
