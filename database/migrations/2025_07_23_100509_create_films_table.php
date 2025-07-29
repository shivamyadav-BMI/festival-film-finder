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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('director')->index();
            $table->year('year')->index();
            $table->string('genres')->index();
            $table->string('festival_awards');
            $table->float('imdb_rating')->index();
            $table->float('rotten_tomatoes_rating')->index();
            $table->float('metacritic_rating')->index();
            $table->text('plot_summary');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
