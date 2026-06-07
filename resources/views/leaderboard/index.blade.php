@extends('layouts.app')
@section('title', 'Classement')

@push('styles')
<style>
    .lb-header { margin-bottom: 2rem; }
    .lb-header h1 { font-family: 'Bangers', cursive; font-size: 2.5rem; letter-spacing: 3px; color: var(--text); }
    .lb-header p  { color: var(--muted); margin-top: 0.3rem; }

    .lb-table { width: 100%; border-collapse: collapse; }
    .lb-table th {
        text-align: left; padding: 0.8rem 1rem; font-size: 0.8rem;
        text-transform: uppercase; letter-spacing: 1px; color: var(--muted);
        border-bottom: 1px solid var(--border);
    }
    .lb-table td { padding: 1rem; border-bottom: 1px solid var(--border); color: var(--text); }
    .lb-table tr:hover td { background: rgba(37,99,235,0.04); }

    .rank { font-family: 'Bangers', cursive; font-size: 1.4rem; letter-spacing: 1px; }
    .rank-1 { color: #d97f06; }
    .rank-2 { color: #94a3b8; }
    .rank-3 { color: #b8762f; }

    .player-name { font-weight: 700; }
    .char-info { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: var(--muted); }

    .score-val { font-family: 'Bangers', cursive; font-size: 1.6rem; color: var(--primary); letter-spacing: 1px; }

    .my-score-banner {
        background: rgba(37,99,235,0.06); border: 1px solid rgba(37,99,235,0.3);
        border-radius: 12px; padding: 1.2rem 1.5rem; margin-bottom: 2rem;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
    }
    .my-score-banner .label { color: var(--muted); font-size: 0.9rem; }
    .my-score-banner .val   { font-family: 'Bangers', cursive; font-size: 1.8rem; color: var(--primary); letter-spacing: 1px; }
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
        <span style="color:var(--muted);">Tu n'as pas encore joué !</span>
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
                    <span style="color:var(--border);">-</span>
                    @endif
                </td>
                <td><span class="score-val">{{ number_format($entry->score) }}</span></td>
                <td style="color:#b8790f; font-weight:700;">{{ $entry->coins_collected }}</td>
                <td>
                    <span class="badge {{ $entry->difficulty === 'hard' ? 'badge-legendary' : 'badge-normal' }}">
                        {{ $entry->difficulty === 'hard' ? '🔴 Difficile' : '🟦 Normal' }}
                    </span>
                </td>
                <td style="color:var(--muted);">{{ $entry->duration }}s</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center; padding:3rem; color:var(--muted);">
        <div style="font-size:3rem; margin-bottom:1rem;">🏃</div>
        <p>Personne n'a encore joué. Sois le premier !</p>
        @auth<a href="{{ route('game.index') }}" class="btn btn-primary" style="margin-top:1rem;">🎮 Jouer</a>@endauth
    </div>
    @endif
</div>
@endsection
