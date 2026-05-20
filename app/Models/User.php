<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'coins'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'coins' => 'integer',
        ];
    }

    // Persos débloqués par ce user
    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class, 'user_characters')->withTimestamps();
    }

    // Scores du user
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    // Meilleur score du user
    public function bestScore(): int
    {
        return $this->scores()->max('score') ?? 0;
    }

    // Vérifie si le user possède un perso
    public function hasCharacter(Character $character): bool
    {
        return $this->characters()->where('character_id', $character->id)->exists();
    }

    // Ajoute des pièces
    public function addCoins(int $amount): void
    {
        $this->increment('coins', $amount);
    }

    // Dépense des pièces
    public function spendCoins(int $amount): bool
    {
        if ($this->coins < $amount) return false;
        $this->decrement('coins', $amount);
        return true;
    }
}
