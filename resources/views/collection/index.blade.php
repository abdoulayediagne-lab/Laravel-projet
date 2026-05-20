@extends('layouts.app')
@section('title', 'Collection')

@push('styles')
<style>
    .collection-header { margin-bottom: 2rem; }
    .collection-header h1 { font-family: 'Bangers', cursive; font-size: 2.5rem; letter-spacing: 3px; }
    .collection-header p  { color: #64748b; margin-top: 0.3rem; }

    .progress-bar-wrap { background: #1e293b; border-radius: 99px; height: 8px; margin: 1rem 0 2rem; overflow: hidden; }
    .progress-bar      { height: 100%; background: linear-gradient(90deg, #7c3aed, #06b6d4); border-radius: 99px; transition: width 0.5s; }

    .rarity-section { margin-bottom: 2.5rem; }
    .rarity-title { font-family: 'Bangers', cursive; font-size: 1.5rem; letter-spacing: 2px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }

    .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; }

    .char-card {
        background: #12122a; border: 2px solid #2a2a50;
        border-radius: 14px; padding: 1.5rem 1rem; text-align: center;
        transition: all 0.2s; position: relative; overflow: hidden;
    }
    .char-card.owned { border-color: #7c3aed; }
    .char-card.owned.legendary-card { border-color: #fbbf24; box-shadow: 0 0 20px rgba(251,191,36,0.2); }
    .char-card.locked { opacity: 0.45; filter: grayscale(0.8); }

    .char-card:hover.owned { transform: translateY(-4px); }

    .card-emoji { font-size: 2.8rem; margin-bottom: 0.5rem; }
    .card-name  { font-weight: 700; font-size: 0.9rem; color: #e2e8f0; margin-bottom: 0.4rem; }
    .card-prob  { font-size: 0.75rem; color: #64748b; margin-top: 0.3rem; }

    .lock-overlay {
        position: absolute; inset: 0; display: flex;
        align-items: center; justify-content: center;
        font-size: 1.8rem;
    }

    .chest-shop { background: #12122a; border: 1px solid #2a2a50; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; }
    .chest-shop h2 { font-family: 'Bangers', cursive; font-size: 1.8rem; letter-spacing: 2px; margin-bottom: 1rem; }
    .chest-options { display: flex; gap: 1rem; flex-wrap: wrap; }
    .chest-option {
        flex: 1; min-width: 200px; background: #0a0a14; border: 2px solid #2a2a50;
        border-radius: 12px; padding: 1.5rem; text-align: center; cursor: pointer;
        transition: all 0.2s;
    }
    .chest-option:hover { border-color: #7c3aed; transform: translateY(-2px); }
    .chest-option.legendary-chest { border-color: rgba(251,191,36,0.3); }
    .chest-option.legendary-chest:hover { border-color: #fbbf24; }
    .chest-option .chest-icon { font-size: 3rem; margin-bottom: 0.5rem; }
    .chest-option .chest-name { font-weight: 700; margin-bottom: 0.3rem; }
    .chest-option .chest-info { font-size: 0.85rem; color: #64748b; margin-bottom: 1rem; }
</style>
@endpush

@section('content')
<div class="collection-header">
    <h1>🃏 Ma Collection</h1>
    <p>{{ count($ownedIds) }} / {{ $allCharacters->count() }} personnages débloqués</p>
    <div class="progress-bar-wrap">
        <div class="progress-bar" style="width: {{ $allCharacters->count() > 0 ? round(count($ownedIds) / $allCharacters->count() * 100) : 0 }}%"></div>
    </div>
</div>

<!-- Shop coffres -->
<div class="chest-shop">
    <h2>🛍️ Ouvrir un Coffre</h2>
    <div class="chest-options">
        <div class="chest-option" onclick="buyChest('normal')">
            <div class="chest-icon">📦</div>
            <div class="chest-name">Coffre Normal</div>
            <div class="chest-info">Personnages normaux<br>Probabilités équilibrées</div>
            <button class="btn btn-accent" style="width:100%;">🪙 50 pièces</button>
        </div>
        <div class="chest-option legendary-chest">
            <div class="chest-icon">🌟</div>
            <div class="chest-name">Coffre Légendaire</div>
            <div class="chest-info">Karl, Adrien, Aurélie, Aloïs...<br>Finir une run difficile pour en gagner un</div>
            <button class="btn btn-gold" style="width:100%;" disabled>Gagner en run difficile</button>
        </div>
    </div>
</div>

<!-- Base -->
@php $base = $allCharacters->where('rarity', 'base'); @endphp
@if($base->count())
<div class="rarity-section">
    <div class="rarity-title">🧑 Personnages de Base</div>
    <div class="cards-grid">
        @foreach($base as $char)
        <div class="char-card owned">
            <div class="card-emoji">{{ $char->emoji }}</div>
            <div class="card-name">{{ $char->name }}</div>
            <span class="badge badge-base">Base</span>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Normal -->
@php $normals = $allCharacters->where('rarity', 'normal'); @endphp
@if($normals->count())
<div class="rarity-section">
    <div class="rarity-title" style="color:#06b6d4;">🔵 Personnages Normaux</div>
    <div class="cards-grid">
        @foreach($normals as $char)
        @php $owned = in_array($char->id, $ownedIds); @endphp
        <div class="char-card {{ $owned ? 'owned' : 'locked' }}">
            <div class="card-emoji">{{ $owned ? $char->emoji : '❓' }}</div>
            <div class="card-name">{{ $owned ? $char->name : '???' }}</div>
            <span class="badge badge-normal">Normal</span>
            @if($char->probability > 0)
                <div class="card-prob">{{ $char->probability }}%</div>
            @endif
            @if(!$owned)<div class="lock-overlay">🔒</div>@endif
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Légendaire -->
@php $legends = $allCharacters->where('rarity', 'legendary'); @endphp
@if($legends->count())
<div class="rarity-section">
    <div class="rarity-title" style="color:#fbbf24;">⭐ Légendaires</div>
    <div class="cards-grid">
        @foreach($legends as $char)
        @php $owned = in_array($char->id, $ownedIds); @endphp
        <div class="char-card legendary-card {{ $owned ? 'owned' : 'locked' }}">
            <div class="card-emoji">{{ $owned ? $char->emoji : '❓' }}</div>
            <div class="card-name">{{ $owned ? $char->name : '???' }}</div>
            <span class="badge badge-legendary">Légendaire</span>
            @if($char->probability > 0)
                <div class="card-prob">{{ $char->probability }}%</div>
            @endif
            @if(!$owned)<div class="lock-overlay">🔒</div>@endif
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function buyChest(type) {
    if (!confirm(type === 'normal' ? 'Ouvrir un coffre normal pour 50 pièces ?' : 'Ouvrir un coffre légendaire ?')) return;

    fetch('{{ route("chest.open") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ type })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showToast(data.message, 'error');
        }
    });
}
</script>
@endpush
