@extends('layouts.app')
@section('title', 'Classement')

@push('styles')
<style>
    .lb-header { margin-bottom: 2rem; }
    .lb-header h1 { font-family: 'Bangers', cursive; font-size: 2.5rem; letter-spacing: 3px; }
    .lb-header p  { color: #64748b; margin-top: 0.3rem; }

    .lb-table { width: 100%; border-collapse: collapse; }
    .lb-table th {
        text-align: left; padding: 0.8rem 1rem; font-size: 0.8rem;
        text-transform: uppercase; letter-spacing: 1px; color: #64748b;
        border-bottom: 1px solid #2a2a50;
    }
    .lb-table td { padding: 1rem; border-bottom: 1px solid #1e293b; }
    .lb-table tr:hover td { background: rgba(124,58,237,0.05); }

    .rank { font-family: 'Bangers', cursive; font-size: 1.4rem; letter-spacing: 1px; }
    .rank-1 { color: #fbbf24; }
    .rank-2 { color: #94a3b8; }
    .rank-3 { color: #cd7c2e; }

    .player-name { font-weight: 700; }
    .char-info { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: #94a3b8; }

    .score-val { font-family: 'Bangers', cursive; font-size: 1.6rem; color: #e2e8f0; letter-spacing: 1px; }

    .my-score-banner {
        background: rgba(124,58,237,0.1); border: 1px solid #7c3aed;
        border-radius: 12px; padding: 1.2rem 1.5rem; margin-bottom: 2rem;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
    }
    .my-score-banner .label { color: #94a3b8; font-size: 0.9rem; }
    .my-score-banner .val   { font-family: 'Bangers', cursive; font-size: 1.8rem; color: #a78bfa; letter-spacing: 1px; }
</style>
@endpush

@section('content')
<div class="lb-header">
    <h1>🏆 Classement</h1>
    <p>Les meilleurs runners de la promo</p>
</div>

@auth
    @if($userBest)
    <div class="my-score-banner">
        <div>
            <div class="label">Mon meilleur score</div>
            <div class="val">{{ $userBest->score }}</div>
        </div>
        <div>
            <div class="label">Pièces récoltées</div>
            <div class="val">🪙 {{ $userBest->coins_collected }}</div>
        </div>
        <div>
            <div class="label">Difficulté</div>
            <div class="val">{{ $userBest->difficulty === 'hard' ? '🔴 Difficile' : '🟦 Normal' }}</div>
        </div>
        <a href="{{ route('game.index') }}" class="btn btn-primary">🎮 Battre mon score</a>
    </div>
    @else
    <div class="my-score-banner">
        <span style="color:#64748b;">Tu n'as pas encore joué !</span>
        <a href="{{ route('game.index') }}" class="btn btn-primary">🎮 Jouer maintenant</a>
    </div>
    @endif
@endauth

<div class="card">
    @if($topScores->count() > 0)
    <table class="lb-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Joueur</th>
                <th>Personnage</th>
                <th>Score</th>
                <th>🪙 Pièces</th>
                <th>Difficulté</th>
                <th>Durée</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topScores as $i => $entry)
            <tr>
                <td>
                    <span class="rank {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : '')) }}">
                        {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) }}
                    </span>
                </td>
                <td><span class="player-name">{{ $entry->user->name }}</span></td>
                <td>
                    @if($entry->character)
                    <div class="char-info">
                        <span>{{ $entry->character->emoji }}</span>
                        <span>{{ $entry->character->name }}</span>
                    </div>
                    @else
                    <span style="color:#334155;">—</span>
                    @endif
                </td>
                <td><span class="score-val">{{ number_format($entry->score) }}</span></td>
                <td style="color:#fbbf24; font-weight:700;">{{ $entry->coins_collected }}</td>
                <td>
                    <span class="badge {{ $entry->difficulty === 'hard' ? 'badge-legendary' : 'badge-normal' }}">
                        {{ $entry->difficulty === 'hard' ? '🔴 Difficile' : '🟦 Normal' }}
                    </span>
                </td>
                <td style="color:#64748b;">{{ $entry->duration }}s</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center; padding:3rem; color:#64748b;">
        <div style="font-size:3rem; margin-bottom:1rem;">🏃</div>
        <p>Personne n'a encore joué. Sois le premier !</p>
        @auth<a href="{{ route('game.index') }}" class="btn btn-primary" style="margin-top:1rem;">🎮 Jouer</a>@endauth
    </div>
    @endif
</div>
@endsection
