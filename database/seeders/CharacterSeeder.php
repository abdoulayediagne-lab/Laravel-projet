<?php

namespace Database\Seeders;

use App\Models\Character;
use Illuminate\Database\Seeder;

class CharacterSeeder extends Seeder
{
    public function run(): void
    {
        // ---- PERSOS DE BASE (débloqués dès le départ) ----
        Character::firstOrCreate(['slug' => 'mathis'], [
            'name'        => 'Mathis',
            'rarity'      => 'base',
            'probability' => 0,
            'color'       => '#7c3aed',
            'emoji'       => '🧑',
            'is_base'     => true,
        ]);

        Character::firstOrCreate(['slug' => 'abdoulaye'], [
            'name'        => 'Abdoulaye',
            'rarity'      => 'base',
            'probability' => 0,
            'color'       => '#0ea5e9',
            'emoji'       => '👦',
            'is_base'     => true,
        ]);

        // ---- PERSOS NORMAUX (coffres normaux) ----
        // Probabilités sur 100 — chaque élève de la promo
        $normalStudents = [
            ['name' => 'Lucas',    'slug' => 'lucas',    'emoji' => '🙋', 'color' => '#10b981', 'probability' => 5],
            ['name' => 'Emma',     'slug' => 'emma',     'emoji' => '👩', 'color' => '#f43f5e', 'probability' => 5],
            ['name' => 'Noah',     'slug' => 'noah',     'emoji' => '🧔', 'color' => '#f97316', 'probability' => 5],
            ['name' => 'Léa',      'slug' => 'lea',      'emoji' => '👱‍♀️','color' => '#eab308', 'probability' => 5],
            ['name' => 'Tom',      'slug' => 'tom',      'emoji' => '🤓', 'color' => '#06b6d4', 'probability' => 5],
            ['name' => 'Jade',     'slug' => 'jade',     'emoji' => '💁‍♀️','color' => '#8b5cf6', 'probability' => 5],
            ['name' => 'Hugo',     'slug' => 'hugo',     'emoji' => '😎', 'color' => '#64748b', 'probability' => 5],
            // Ajoute ici tous les élèves de ta promo !
        ];

        foreach ($normalStudents as $s) {
            Character::firstOrCreate(['slug' => $s['slug']], [
                'name'        => $s['name'],
                'rarity'      => 'normal',
                'probability' => $s['probability'],
                'color'       => $s['color'],
                'emoji'       => $s['emoji'],
                'is_base'     => false,
            ]);
        }

        // ---- LÉGENDAIRES (coffres légendaires uniquement) ----
        $legendaries = [
            [
                'name'        => 'Karl',
                'slug'        => 'karl',
                'emoji'       => '👑',
                'color'       => '#fbbf24',
                'probability' => 1,       // 1% — rare
            ],
            [
                'name'        => 'Aurélie',
                'slug'        => 'aurelie',
                'emoji'       => '🌟',
                'color'       => '#f472b6',
                'probability' => 4,       // 4%
            ],
            [
                'name'        => 'Aloïs',
                'slug'        => 'alois',
                'emoji'       => '⚡',
                'color'       => '#a3e635',
                'probability' => 4,       // 4%
            ],
            [
                'name'        => 'Adrien',
                'slug'        => 'adrien',
                'emoji'       => '💎',
                'color'       => '#67e8f9',
                'probability' => 0.01,    // 0.01% — ultra rare
            ],
        ];

        foreach ($legendaries as $l) {
            Character::firstOrCreate(['slug' => $l['slug']], [
                'name'        => $l['name'],
                'rarity'      => 'legendary',
                'probability' => $l['probability'],
                'color'       => $l['color'],
                'emoji'       => $l['emoji'],
                'is_base'     => false,
            ]);
        }

        $this->command->info('✅ ' . Character::count() . ' personnages créés !');
    }
}
