@extends('layouts.app')
@section('title', 'Profil')

@push('styles')
<style>
    .profile-grid { display: grid; grid-template-columns: 300px 1fr; gap: 1.5rem; }
    @media(max-width:768px) { .profile-grid { grid-template-columns: 1fr; } }

    .profile-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 2rem; text-align: center; }
    .profile-avatar {
        width: 90px; height: 90px; border-radius: 50%; margin: 0 auto 1rem;
        background: linear-gradient(135deg, #2563eb, #38bdf8);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; font-family: 'Bangers', cursive; color: white;
    }
    .profile-name  { font-size: 1.4rem; font-weight: 800; margin-bottom: 0.3rem; color: var(--text); }
    .profile-email { color: var(--muted); font-size: 0.85rem; margin-bottom: 1.5rem; }

    .stat-row { display: flex; justify-content: space-between; padding: 0.7rem 0; border-bottom: 1px solid var(--border); }
    .stat-row:last-child { border: none; }
    .stat-label { color: var(--muted); font-size: 0.9rem; }
    .stat-val   { font-weight: 700; color: var(--text); }

    .runs-table { width: 100%; border-collapse: collapse; }
    .runs-table th { text-align: left; padding: 0.7rem 1rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); border-bottom: 1px solid var(--border); }
    .runs-table td { padding: 0.9rem 1rem; border-bottom: 1px solid var(--border); color: var(--text); }
    .runs-table tr:hover td { background: rgba(37,99,235,0.04); }
</style>
@endpush

@section('content')
<div style="margin-bottom:1.5rem;">
    <h1 style="font-family:'Bangers',cursive; font-size:2.5rem; letter-spacing:3px; color:var(--text);">👤 Mon Profil</h1>
</div>

<div class="profile-grid">
    <!-- Carte profil -->
    <div>
        <div class="profile-card">
            <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>

            <div class="stat-row">
                <span class="stat-label">🪙 Pièces</span>
                <span class="stat-val" style="color:#b8790f;">{{ $user->coins }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">🏃 Runs totales</span>
                <span class="stat-val">{{ $totalRuns }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">🏆 Meilleur score</span>
                <span class="stat-val" style="color:var(--primary);">{{ number_format($bestScore) }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">🃏 Persos débloqués</span>
                <span class="stat-val">{{ $user->characters->count() }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">🪙 Total pièces gagnées</span>
                <span class="stat-val" style="color:#b8790f;">{{ number_format($totalCoins) }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">⏱️ Temps de jeu total</span>
                <span class="stat-val">
                    @php
                        $h = intdiv($totalDuration, 3600);
                        $m = intdiv($totalDuration % 3600, 60);
                        $s = $totalDuration % 60;
                        $parts = [];
                        if ($h > 0) $parts[] = $h . 'h';
                        if ($m > 0) $parts[] = $m . 'm';
                        $parts[] = $s . 's';
                    @endphp
                    {{ implode(' ', $parts) }}
                </span>
            </div>
            <div class="stat-row">
                <span class="stat-label">⭐ Meilleur personnage</span>
                <span class="stat-val">{{ $bestCharacter ? $bestCharacter->emoji . ' ' . $bestCharacter->name : '-' }}</span>
            </div>

            <div style="margin-top:1.5rem; display:flex; flex-direction:column; gap:0.7rem;">
                <a href="{{ route('game.index') }}" class="btn btn-primary">🎮 Jouer</a>
                <a href="{{ route('collection.index') }}" class="btn btn-outline">🃏 Ma collection</a>
            </div>
        </div>
    </div>

    <!-- Historique -->
    <div class="card">
        <h2 style="font-family:'Bangers',cursive; font-size:1.6rem; letter-spacing:2px; margin-bottom:1.2rem;">📋 Mes Dernières Runs</h2>

        @if($scores->count() > 0)
        <table class="runs-table">
            <thead>
                <tr>
                    <th>Score</th>
                    <th>Perso</th>
                    <th>Difficulté</th>
                    <th>🪙</th>
                    <th>Durée</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scores as $s)
                <tr>
                    <td style="font-family:'Bangers',cursive; font-size:1.4rem; color:var(--primary);">{{ number_format($s->score) }}</td>
                    <td>
                        @if($s->character)
                            {{ $s->character->emoji }} {{ $s->character->name }}
                        @else
                            <span style="color:var(--border);">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $s->difficulty === 'hard' ? 'badge-legendary' : 'badge-normal' }}">
                            {{ $s->difficulty === 'hard' ? '🔴 Hard' : '🟦 Normal' }}
                        </span>
                    </td>
                    <td style="color:#b8790f; font-weight:700;">{{ $s->coins_collected }}</td>
                    <td style="color:var(--muted);">{{ $s->duration }}s</td>
                    <td style="color:var(--muted); font-size:0.85rem;">{{ $s->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align:center; padding:2rem; color:var(--muted);">
            <div style="font-size:2.5rem; margin-bottom:0.8rem;">🏃</div>
            <p>Aucune run pour l'instant.</p>
            <a href="{{ route('game.index') }}" class="btn btn-primary" style="margin-top:1rem;">Jouer maintenant</a>
        </div>
        @endif
    </div>
</div>
@endsection
