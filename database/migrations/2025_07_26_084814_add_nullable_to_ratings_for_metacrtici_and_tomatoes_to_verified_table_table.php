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
            $table->float('rotten_tomatoes_rating')->nullable()->change();
            $table->float('metacritic_rating')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verified_films', function (Blueprint $table) {
            $table->float('rotten_tomatoes_rating')->nullable(false)->change();
            $table->float('metacritic_rating')->nullable(false)->change();
        });
    }
};
