<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    // Page d'accueil
    public function welcome()
    {
        return view('welcome');
    }

    // Page de jeu - choisir perso, map, difficulté
    public function index()
    {
        $user = Auth::user();
        $userCharacters = $user->characters()->get();

        // Ajouter les persos de base si pas encore ajoutés
        $baseCharacters = Character::where('is_base', true)->get();
        foreach ($baseCharacters as $base) {
            if (!$user->hasCharacter($base)) {
                $user->characters()->attach($base->id);
            }
        }

        $userCharacters = $user->characters()->get();

        return view('game.index', compact('user', 'userCharacters'));
    }

    // Sauvegarder le score après une run
    public function saveScore(Request $request)
    {
        $request->validate([
            'score'           => 'required|integer|min:0',
            'coins_collected' => 'required|integer|min:0',
            'difficulty'      => 'required|in:normal,hard',
            'duration'        => 'required|integer|min:0',
            'character_id'    => 'nullable|exists:characters,id',
        ]);

        $user = Auth::user();

        // Sauvegarder le score
        Score::create([
            'user_id'         => $user->id,
            'character_id'    => $request->character_id,
            'score'           => $request->score,
            'coins_collected' => $request->coins_collected,
            'difficulty'      => $request->difficulty,
            'duration'        => $request->duration,
        ]);

        // Ajouter les pièces au wallet du joueur
        $user->addCoins($request->coins_collected);

        // Donner un coffre si la run est terminée
        $chestType = $request->difficulty === 'hard' ? 'legendary' : 'normal';

        return response()->json([
            'success'    => true,
            'new_coins'  => $user->fresh()->coins,
            'chest_type' => $chestType,
            'message'    => 'Score sauvegardé ! Tu as gagné un coffre ' . $chestType . ' !',
        ]);
    }

    // Leaderboard
    public function leaderboard()
    {
        $topScores = Score::with(['user', 'character'])
            ->orderBy('score', 'desc')
            ->take(20)
            ->get();

        $userBest = null;
        if (Auth::check()) {
            $userBest = Score::where('user_id', Auth::id())
                ->orderBy('score', 'desc')
                ->first();
        }

        return view('leaderboard.index', compact('topScores', 'userBest'));
    }

    // Profil du joueur
    public function profile()
    {
        $user = Auth::user();
        $scores        = $user->scores()->with('character')->orderBy('score', 'desc')->take(10)->get();
        $totalRuns     = $user->scores()->count();
        $bestScore     = $user->bestScore();
        $totalCoins    = $user->scores()->sum('coins_collected');
        $totalDuration = $user->scores()->sum('duration');
        $bestCharacter = Score::with('character')
            ->where('user_id', $user->id)
            ->whereNotNull('character_id')
            ->orderByDesc('score')
            ->first()
            ?->character;

        return view('profile.index', compact(
            'user', 'scores', 'totalRuns', 'bestScore',
            'totalCoins', 'totalDuration', 'bestCharacter'
        ));
    }
}
