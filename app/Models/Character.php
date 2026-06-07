<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'rarity', 'probability', 'color', 'emoji', 'is_base'
    ];

    protected $casts = [
        'is_base' => 'boolean',
        'probability' => 'float',
    ];

    // Les users qui possèdent ce perso
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_characters')->withTimestamps();
    }

    // Tous les persos légendaires
    public static function legendary(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('rarity', 'legendary')->get();
    }

    // Tous les persos normaux (non base)
    public static function normal(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('rarity', 'normal')->get();
    }
}
