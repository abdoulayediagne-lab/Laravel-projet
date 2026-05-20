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
        background: linear-gradient(135deg, #7c3aed, #06b6d4, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
    }
    .hero-sub {
        color: #94a3b8;
        font-size: 1.2rem;
        margin-bottom: 2.5rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    .hero-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
    .hero-btns .btn { padding: 0.9rem 2.5rem; font-size: 1.1rem; }
    .features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 4rem;
    }
    .feature-card {
        background: #12122a;
        border: 1px solid #2a2a50;
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        transition: transform 0.2s, border-color 0.2s;
    }
    .feature-card:hover { transform: translateY(-4px); border-color: #7c3aed; }
    .feature-icon { font-size: 3rem; margin-bottom: 1rem; }
    .feature-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem; }
    .feature-desc { color: #64748b; font-size: 0.9rem; line-height: 1.6; }
    .characters-preview { display: flex; justify-content: center; gap: 1rem; margin-top: 3rem; flex-wrap: wrap; }
    .char-bubble {
        width: 70px; height: 70px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; border: 3px solid #2a2a50; transition: transform 0.2s;
    }
    .char-bubble:hover { transform: scale(1.2); }
    .char-bubble.legendary { border-color: #fbbf24; box-shadow: 0 0 15px rgba(251,191,36,0.3); }
</style>
@endpush

@section('content')
<div class="hero">
    <h1 class="hero-title">🏃 TEMPLE RUN</h1>
    <p class="hero-sub">Le jeu infini de notre promo. Cours, saute, esquive — débloque tous les persos de la classe !</p>
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
        <div class="char-bubble" style="background:#1e1b4b;" title="Mathis">🧑</div>
        <div class="char-bubble" style="background:#1e3a5f;" title="Abdoulaye">👦</div>
        <div class="char-bubble legendary" style="background:#1a1a0e;" title="Légendaire">❓</div>
        <div class="char-bubble legendary" style="background:#1a1a0e;" title="Légendaire">❓</div>
        <div class="char-bubble" style="background:#1a2e1a;" title="Normal">🕵️</div>
        <div class="char-bubble legendary" style="background:#1a1a0e;" title="Ultra Rare">⭐</div>
    </div>
</div>

<div class="features">
    <div class="feature-card">
        <div class="feature-icon">♾️</div>
        <div class="feature-title">Runner Infini</div>
        <div class="feature-desc">Cours à l'infini, la vitesse augmente. Saute et esquive pour survivre le plus longtemps possible.</div>
    </div>
    <div class="feature-card">
        <div class="feature-icon">📦</div>
        <div class="feature-title">Système de Coffres</div>
        <div class="feature-desc">Chaque run te récompense. Coffres normaux avec tes pièces, coffres légendaires en mode difficile.</div>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🃏</div>
        <div class="feature-title">Collection de Cartes</div>
        <div class="feature-desc">Débloque tous les persos de la promo. Karl à 1%, Adrien à 0,01%... bonne chance 😈</div>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🏆</div>
        <div class="feature-title">Classement</div>
        <div class="feature-desc">Qui a le meilleur score ? Affronte toute la promo et prouve que tu es le meilleur runner.</div>
    </div>
</div>
@endsection
