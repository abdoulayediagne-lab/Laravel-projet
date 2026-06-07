<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpenChestRequest;
use App\Models\Character;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChestController extends Controller
{
    const NORMAL_CHEST_COST    = 50;   // pièces pour un coffre normal
    const LEGENDARY_CHEST_COST = 0;    // coffre légendaire = gagné en run difficile (gratuit à ouvrir)

    // Ouvrir un coffre
    public function open(OpenChestRequest $request)
    {
        $type   = $request->validated('type');
        $userId = Auth::id();

        // Transaction + verrou de ligne : évite qu'une double-soumission rapide
        // ne fasse passer deux fois le même solde de pièces (TOCTOU race condition).
        $result = DB::transaction(function () use ($type, $userId) {
            $user = User::lockForUpdate()->findOrFail($userId);

            if ($type === 'normal') {
                if (!$user->spendCoins(self::NORMAL_CHEST_COST)) {
                    return [
                        'success' => false,
                        'status'  => 400,
                        'payload' => [
                            'message' => 'Pas assez de pièces ! Il te faut ' . self::NORMAL_CHEST_COST . ' pièces.',
                        ],
                    ];
                }
            }

            $character = $this->rollCharacter($type);

            if (!$character) {
                if ($type === 'normal') {
                    $user->addCoins(self::NORMAL_CHEST_COST);
                }

                return [
                    'success' => false,
                    'status'  => 200,
                    'payload' => [
                        'message' => 'Tu as déjà tous les personnages disponibles ! 🎉',
                    ],
                ];
            }

            $isNew = false;
            if (!$user->hasCharacter($character)) {
                $user->characters()->attach($character->id);
                $isNew = true;
            }

            return [
                'success' => true,
                'status'  => 200,
                'payload' => [
                    'is_new'    => $isNew,
                    'character' => [
                        'id'     => $character->id,
                        'name'   => $character->name,
                        'rarity' => $character->rarity,
                        'color'  => $character->color,
                        'emoji'  => $character->emoji,
                    ],
                    'new_coins' => $user->coins,
                    'message'   => $isNew
                        ? '🎉 Nouveau personnage débloqué : ' . $character->name . ' !'
                        : 'Tu avais déjà ' . $character->name . ', mais il était content de te revoir !',
                ],
            ];
        });

        return response()->json(
            ['success' => $result['success'], ...$result['payload']],
            $result['status']
        );
    }

    // Logique gacha : tirer un personnage selon les probabilités
    private function rollCharacter(string $type): ?Character
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
