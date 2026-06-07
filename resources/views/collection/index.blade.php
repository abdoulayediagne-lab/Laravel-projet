@extends('layouts.app')
@section('title', 'Collection')

@push('styles')
<style>
    .collection-header { margin-bottom: 2rem; }
    .collection-header h1 { font-family: 'Bangers', cursive; font-size: 2.5rem; letter-spacing: 3px; color: var(--text); }
    .collection-header p  { color: var(--muted); margin-top: 0.3rem; }

    .progress-bar-wrap { background: var(--border); border-radius: 99px; height: 8px; margin: 1rem 0 2rem; overflow: hidden; }
    .progress-bar      { height: 100%; background: linear-gradient(90deg, #2563eb, #38bdf8); border-radius: 99px; transition: width 0.5s; }

    .rarity-section { margin-bottom: 2.5rem; }
    .rarity-title { font-family: 'Bangers', cursive; font-size: 1.5rem; letter-spacing: 2px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text); }

    .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; }

    .char-card {
        background: var(--surface); border: 2px solid var(--border);
        border-radius: 14px; padding: 1.5rem 1rem; text-align: center;
        transition: all 0.2s; position: relative; overflow: hidden;
    }
    .char-card.owned { border-color: var(--primary); }
    .char-card.owned.legendary-card { border-color: var(--accent); box-shadow: 0 6px 20px rgba(245,158,11,0.15); }
    .char-card.locked { opacity: 0.5; filter: grayscale(0.7); }

    .char-card:hover.owned { transform: translateY(-4px); }

    .card-emoji { font-size: 2.8rem; margin-bottom: 0.5rem; }
    .card-name  { font-weight: 700; font-size: 0.9rem; color: var(--text); margin-bottom: 0.4rem; }
    .card-prob  { font-size: 0.75rem; color: var(--muted); margin-top: 0.3rem; }

    .lock-overlay {
        position: absolute; inset: 0; display: flex;
        align-items: center; justify-content: center;
        font-size: 1.8rem;
    }

    .chest-shop { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 2rem; margin-top: 2.5rem; }
    .chest-shop h2 { font-family: 'Bangers', cursive; font-size: 1.8rem; letter-spacing: 2px; margin-bottom: 1rem; color: var(--text); }
    .chest-options { display: flex; gap: 1rem; flex-wrap: wrap; }
    .chest-option {
        flex: 1; min-width: 200px; background: var(--bg); border: 2px solid var(--border);
        border-radius: 12px; padding: 1.5rem; text-align: center; cursor: pointer;
        transition: all 0.2s;
    }
    .chest-option:hover { border-color: var(--primary); transform: translateY(-2px); }
    .chest-option.legendary-chest { border-color: rgba(245,158,11,0.35); }
    .chest-option.legendary-chest:hover { border-color: var(--accent); }
    .chest-option .chest-icon { font-size: 3rem; margin-bottom: 0.5rem; }
    .chest-option .chest-name { font-weight: 700; margin-bottom: 0.3rem; color: var(--text); }
    .chest-option .chest-info { font-size: 0.85rem; color: var(--muted); margin-bottom: 1rem; }
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
    <div class="rarity-title" style="color:#2563eb;">🔵 Personnages Normaux</div>
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
    <div class="rarity-title" style="color:#d97f06;">⭐ Légendaires</div>
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

<!-- Shop coffres (en bas, comme demandé) -->
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
