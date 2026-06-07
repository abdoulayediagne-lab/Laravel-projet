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
        Schema::table('scores', function (Blueprint $table) {
            // Accélère le classement général (ORDER BY score DESC)
            $table->index('score');
            // Accélère la recherche du meilleur score d'un joueur donné
            $table->index(['user_id', 'score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropIndex(['score']);
            $table->dropIndex(['user_id', 'score']);
        });
    }
};
