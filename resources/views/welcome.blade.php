@extends('layouts.app')
@section('title', 'Accueil')

@push('styles')
<style>
    .hero {
        text-align: center;
        padding: 5rem 1rem 4rem;
    }
    .hero-title {
        font-family: 'Bangers', cursive;
        font-size: clamp(4rem, 10vw, 7rem);
        letter-spacing: 4px;
        line-height: 1;
        color: var(--text);
        margin-bottom: 1rem;
    }
    .hero-title span { color: var(--primary); }
    .hero-sub {
        color: var(--muted);
        font-size: 1.2rem;
        margin-bottom: 2.5rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    .hero-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
    .hero-btns .btn { padding: 0.9rem 2.5rem; font-size: 1.1rem; }
    .characters-preview { display: flex; justify-content: center; gap: 1rem; margin-top: 3rem; flex-wrap: wrap; }
    .char-bubble {
        width: 70px; height: 70px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; border: 3px solid var(--border); background: var(--surface);
        transition: transform 0.2s;
    }
    .char-bubble:hover { transform: scale(1.15); }
    .char-bubble.legendary { border-color: var(--accent); box-shadow: 0 0 15px rgba(245,158,11,0.25); }
</style>
@endpush

@section('content')
<div class="hero">
    <h1 class="hero-title">🏃 STREET <span>RUN</span></h1>
    <p class="hero-sub">Le jeu infini de notre promo. Cours, saute, esquive : débloque tous les persos de la classe !</p>
    <div class="hero-btns">
        @auth
            <a href="{{ route('game.index') }}" class="btn btn-primary">🎮 Jouer maintenant</a>
            <a href="{{ route('collection.index') }}" class="btn btn-outline">🃏 Ma collection</a>
        @else
            <a href="{{ route('register') }}" class="btn btn-primary">🚀 Commencer à jouer</a>
            <a href="{{ route('login') }}" class="btn btn-outline">Se connecter</a>
        @endauth
    </div>
    <div class="characters-preview">
        <div class="char-bubble" title="Mathis">🧑</div>
        <div class="char-bubble" title="Abdoulaye">👦</div>
        <div class="char-bubble legendary" title="Légendaire">❓</div>
        <div class="char-bubble legendary" title="Légendaire">❓</div>
        <div class="char-bubble" title="Normal">🕵️</div>
        <div class="char-bubble legendary" title="Ultra Rare">⭐</div>
    </div>
</div>
@endsection
