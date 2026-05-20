<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\ChestController;
use App\Http\Controllers\CharacterController;
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
    Route::get('/profile', [GameController::class, 'profile'])->name('profile.index');
});

// Auth (login, register, logout — fourni par Laravel Breeze)
require __DIR__.'/auth.php';
