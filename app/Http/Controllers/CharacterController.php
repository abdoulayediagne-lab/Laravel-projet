<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Support\Facades\Auth;

class CharacterController extends Controller
{
    // Page collection de cartes
    public function index()
    {
        $user = Auth::user();

        // Tous les persos du jeu
        $allCharacters = Character::orderBy('rarity')->orderBy('name')->get();

        // Les ids des persos que le user possède
        $ownedIds = $user->characters()->pluck('character_id')->toArray();

        return view('collection.index', compact('user', 'allCharacters', 'ownedIds'));
    }
}
