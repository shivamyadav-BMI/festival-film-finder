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
        Schema::table('verified_films', function (Blueprint $table) {
              $table->renameColumn('genres', 'genre_names');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verified_films', function (Blueprint $table) {
              $table->renameColumn('genre_names', 'genres');
        });
    }
};
