<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\ChestController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Page d'accueil publique
Route::get('/', [GameController::class, 'welcome'])->name('welcome');

// Leaderboard public
Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('leaderboard.index');

// Routes protégées (connecté uniquement)
Route::middleware('auth')->group(function () {
    Route::get('/game', [GameController::class, 'index'])->name('game.index');
    Route::post('/game/score', [GameController::class, 'saveScore'])->name('game.score');
    Route::post('/chest/open', [ChestController::class, 'open'])->name('chest.open');
    Route::get('/collection', [CharacterController::class, 'index'])->name('collection.index');

    // Profil joueur (stats de jeu) + édition du compte (fourni par Laravel Breeze)
    Route::get('/profile', [GameController::class, 'profile'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth (login, register, logout, fourni par Laravel Breeze)
require __DIR__.'/auth.php';
