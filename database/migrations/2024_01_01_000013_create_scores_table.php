<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('character_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('score');
            $table->integer('coins_collected')->default(0);
            $table->enum('difficulty', ['normal', 'hard'])->default('normal');
            $table->integer('duration')->default(0); // secondes de survie
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
