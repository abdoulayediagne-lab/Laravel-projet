<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // ex: mathis, abdoulaye, karl
            $table->enum('rarity', ['base', 'normal', 'legendary'])->default('normal');
            $table->float('probability')->default(0); // % de chance d'obtenir (0 = non applicable)
            $table->string('color')->default('#6c63ff'); // couleur du perso (avatar coloré)
            $table->string('emoji')->default('🧑');      // emoji representant le perso
            $table->boolean('is_base')->default(false);  // débloqué dès le départ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
