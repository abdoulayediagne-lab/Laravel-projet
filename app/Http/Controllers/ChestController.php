<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChestController extends Controller
{
    const NORMAL_CHEST_COST    = 50;   // pièces pour un coffre normal
    const LEGENDARY_CHEST_COST = 0;    // coffre légendaire = gagné en run difficile (gratuit à ouvrir)

    // Ouvrir un coffre
    public function open(Request $request)
    {
        $request->validate([
            'type' => 'required|in:normal,legendary',
        ]);

        $user = Auth::user();
        $type = $request->type;

        // Coffre normal : coûte des pièces
        if ($type === 'normal') {
            if (!$user->spendCoins(self::NORMAL_CHEST_COST)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pas assez de pièces ! Il te faut ' . self::NORMAL_CHEST_COST . ' pièces.',
                ], 400);
            }
        }

        // Tirer un personnage selon la rareté du coffre
        $character = $this->rollCharacter($user, $type);

        if (!$character) {
            // Rembourser si pas de nouveau perso possible
            if ($type === 'normal') {
                $user->addCoins(self::NORMAL_CHEST_COST);
            }
            return response()->json([
                'success' => false,
                'message' => 'Tu as déjà tous les personnages disponibles ! 🎉',
            ]);
        }

        // Attribuer le perso au user (s'il ne l'a pas déjà)
        $isNew = false;
        if (!$user->hasCharacter($character)) {
            $user->characters()->attach($character->id);
            $isNew = true;
        }

        return response()->json([
            'success'    => true,
            'is_new'     => $isNew,
            'character'  => [
                'id'     => $character->id,
                'name'   => $character->name,
                'rarity' => $character->rarity,
                'color'  => $character->color,
                'emoji'  => $character->emoji,
            ],
            'new_coins'  => $user->fresh()->coins,
            'message'    => $isNew
                ? '🎉 Nouveau personnage débloqué : ' . $character->name . ' !'
                : 'Tu avais déjà ' . $character->name . ', mais il était content de te revoir !',
        ]);
    }

    // Logique gacha : tirer un personnage selon les probabilités
    private function rollCharacter($user, string $type): ?Character
    {
        if ($type === 'legendary') {
            $pool = Character::where('rarity', 'legendary')->get();
        } else {
            $pool = Character::where('rarity', 'normal')->get();
        }

        if ($pool->isEmpty()) return null;

        // Calculer la somme des probabilités
        $totalProb = $pool->sum('probability');

        if ($totalProb <= 0) {
            // Probabilités égales si non définies
            return $pool->random();
        }

        // Tirage pondéré
        $rand = mt_rand(0, (int)($totalProb * 10000)) / 10000;
        $cumul = 0;

        foreach ($pool as $character) {
            $cumul += $character->probability;
            if ($rand <= $cumul) {
                return $character;
            }
        }

        return $pool->last();
    }
}
