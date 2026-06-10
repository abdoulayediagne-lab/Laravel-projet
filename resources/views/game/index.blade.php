@extends('layouts.app')
@section('title', 'Jouer')

@push('styles')
<style>
/* ===== BASE ===== */
.game-wrapper { display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }

/* ===== PREGAME ===== */
.pregame {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 18px;
    width: 100%; max-width: 880px;
    overflow: hidden;
    position: relative;
}

.pg-hero {
    padding: 3.5rem 2.5rem 2.5rem;
    text-align: center;
}
.pg-title {
    font-family: 'Bangers', cursive;
    font-size: 4rem; line-height: 1;
    color: var(--text); letter-spacing: 8px;
    margin: 0; text-transform: uppercase;
}
.pg-title span { color: var(--primary); }
.pg-sub {
    font-size: 0.85rem; color: var(--muted);
    margin-top: 0.7rem; font-weight: 600;
}

#start-btn {
    display: flex; align-items: center; justify-content: center; gap: 0.7rem;
    width: 100%; max-width: 320px; margin: 1.8rem auto 0;
    padding: 1.1rem; border-radius: 12px;
    font-size: 1.25rem; border: none;
    background: var(--primary);
    color: #fff; font-weight: 800;
    cursor: pointer; font-family: 'Bangers', cursive;
    letter-spacing: 4px; text-transform: uppercase;
    transition: all 0.15s;
    box-shadow: 0 8px 24px rgba(37,99,235,0.25);
}
#start-btn:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 10px 28px rgba(37,99,235,0.32); }

/* Options (compactes, secondaires) */
.pg-options {
    border-top: 1px solid var(--border);
    background: var(--bg);
    padding: 1.5rem 2.5rem 2rem;
    display: flex; flex-direction: column; gap: 1.3rem;
}
.pg-opt-row { display: flex; align-items: center; gap: 1.2rem; flex-wrap: wrap; }
.pg-label {
    font-size: 0.62rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 2px;
    color: var(--muted); white-space: nowrap; min-width: 80px;
}

/* Characters */
.char-grid { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.char-card {
    width: 64px; cursor: pointer;
    border: 1.5px solid var(--border); border-radius: 10px;
    padding: 0.55rem 0.4rem;
    text-align: center;
    transition: all 0.12s;
    background: var(--surface);
}
.char-card:hover { border-color: var(--primary); }
.char-card.selected { border-color: var(--primary); background: rgba(37,99,235,0.06); }
.char-emoji { font-size: 1.6rem; line-height: 1.1; }
.char-name { font-size: 0.55rem; font-weight: 700; letter-spacing: 0.5px; color: var(--muted); margin-top: 0.25rem; }

/* Mode cards */
.mode-grid { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.mode-card {
    padding: 0.6rem 1.1rem;
    border: 1.5px solid var(--border); border-radius: 10px;
    cursor: pointer; transition: all 0.12s;
    background: var(--surface);
    text-align: left;
}
.mode-card.active-normal { border-color: var(--primary); background: rgba(37,99,235,0.06); }
.mode-card.active-hard   { border-color: var(--danger);  background: rgba(239,68,68,0.06); }
.mode-title {
    font-weight: 800; font-size: 0.8rem; letter-spacing: 1px;
    color: var(--text); line-height: 1.2;
}
.mode-sub {
    font-size: 0.66rem; color: var(--muted);
    margin-top: 0.15rem;
}

/* Keys */
.keys-row { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
.key-btn {
    padding: 0.4rem 0.9rem; border-radius: 8px;
    border: 1.5px solid var(--border);
    background: var(--surface); color: var(--muted);
    cursor: pointer; font-weight: 700;
    font-size: 0.7rem; letter-spacing: 0.5px;
    transition: all 0.12s;
    font-family: 'Inter', sans-serif;
}
.key-btn.active { border-color: var(--primary); color: var(--primary); background: rgba(37,99,235,0.06); }
.keys-hint { font-size: 0.7rem; color: var(--muted); }

/* Canvas */
#game-container { display: none; flex-direction: column; align-items: center; width: 100%; max-width: 880px; }
#game-canvas-wrapper { overflow: hidden; border: 1px solid var(--border); border-radius: 14px; width: 100%; }
#game-canvas-wrapper canvas { display: block; width: 100% !important; }

/* Game over */
@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
#gameover-panel {
    display: none;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 18px;
    width: 100%; max-width: 880px;
    overflow: hidden; position: relative;
    animation: fadeSlideIn 0.3s ease;
}
.go-inner { padding: 2.5rem 2.5rem 0; text-align: center; }
.go-eyebrow {
    font-size: 0.65rem; font-weight: 800;
    letter-spacing: 4px; text-transform: uppercase;
    color: var(--muted); margin-bottom: 0.4rem;
}
.go-title {
    font-family: 'Bangers', cursive;
    font-size: 3.2rem; letter-spacing: 4px;
    color: var(--text); line-height: 1; margin: 0;
}
.go-stats {
    display: flex; align-items: stretch;
    border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
    margin-top: 2rem;
}
.go-stat {
    flex: 1; padding: 1.3rem 1.5rem; text-align: center;
    border-right: 1px solid var(--border);
}
.go-stat:last-child { border-right: none; }
.go-stat-val {
    font-family: 'Bangers', cursive;
    font-size: 2.3rem; letter-spacing: 2px;
    color: var(--primary); line-height: 1;
}
.go-stat-key {
    font-size: 0.6rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 3px;
    color: var(--muted); margin-top: 0.4rem;
}
.go-actions { display: flex; gap: 0.6rem; padding: 1.5rem 2rem; flex-wrap: wrap; justify-content: center; }

/* Chest modal */
#chest-modal {
    display: none; position: fixed; inset: 0;
    background: rgba(51,49,44,0.45);
    z-index: 1000; align-items: center; justify-content: center;
}
#chest-modal.show { display: flex; }
@keyframes popIn {
    from { transform: scale(0.9) translateY(8px); opacity: 0; }
    to   { transform: scale(1) translateY(0); opacity: 1; }
}
.chest-box {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    padding: 2.5rem; text-align: center;
    max-width: 380px; width: 90%;
    animation: popIn 0.2s ease;
}
.chest-tag {
    font-size: 0.6rem; font-weight: 800;
    letter-spacing: 4px; text-transform: uppercase;
    color: var(--accent); margin-bottom: 1.2rem;
}
.chest-icon { font-size: 3.5rem; margin-bottom: 0.8rem; }
.chest-title {
    font-family: 'Bangers', cursive;
    font-size: 1.6rem; letter-spacing: 3px;
    color: var(--text); margin-bottom: 0.4rem;
}
.chest-desc {
    font-size: 0.78rem; color: var(--muted);
    margin-bottom: 2rem;
}
.chest-result { display: none; }
.result-icon { font-size: 3.5rem; margin-bottom: 0.8rem; }
.result-name {
    font-family: 'Bangers', cursive;
    font-size: 1.6rem; letter-spacing: 3px;
    color: var(--text); margin-bottom: 0.5rem;
}
.result-rarity { margin-bottom: 0.75rem; }
.result-msg {
    font-size: 0.78rem; color: var(--muted);
    margin-bottom: 1.5rem;
}
.btn-full { width: 100%; justify-content: center; padding: 0.9rem 1rem; font-size: 0.95rem; }
</style>
@endpush

@section('content')
<div class="game-wrapper">

    <!-- PRÉ-JEU -->
    <div class="pregame" id="pregame-panel">

        <div class="pg-hero">
            <h1 class="pg-title">STREET <span>RUN</span></h1>
            <p class="pg-sub">Cours, saute, esquive — et ne te fais pas rattraper.</p>
            <button id="start-btn" onclick="startGame()">
                <span>JOUER</span>
                <svg viewBox="0 0 24 24" fill="currentColor" style="width:1em;height:1em;flex-shrink:0"><path d="M5 3l14 9-14 9V3z"/></svg>
            </button>
        </div>

        <div class="pg-options">
            <div class="pg-opt-row">
                <span class="pg-label">Personnage</span>
                <div class="char-grid" id="char-grid">
                    @foreach($userCharacters as $char)
                    <div class="char-card {{ $loop->first ? 'selected' : '' }}"
                         data-id="{{ $char->id }}" onclick="selectChar(this)">
                        <div class="char-emoji">{{ $char->emoji }}</div>
                        <div class="char-name">{{ $char->name }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pg-opt-row">
                <span class="pg-label">Mode</span>
                <div class="mode-grid">
                    <div class="mode-card active-normal" data-diff="normal" onclick="selectDiff(this)">
                        <div class="mode-title">Normal</div>
                        <div class="mode-sub">Coffre standard après la run</div>
                    </div>
                    <div class="mode-card" data-diff="hard" onclick="selectDiff(this)">
                        <div class="mode-title">Difficile</div>
                        <div class="mode-sub">Coffre légendaire si tu survis</div>
                    </div>
                </div>
            </div>

            <div class="pg-opt-row">
                <span class="pg-label">Touches</span>
                <div class="keys-row">
                    <button class="key-btn active" data-keys="arrows" onclick="selectKeys(this)">↑ ← → Flèches</button>
                    <button class="key-btn" data-keys="zqsd" onclick="selectKeys(this)">Z Q D</button>
                    <span class="keys-hint" id="keys-hint">↑ Sauter &nbsp;·&nbsp; ← → Changer de voie</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ZONE JEU -->
    <div id="game-container">
        <div id="game-canvas-wrapper"></div>
    </div>

    <!-- GAME OVER -->
    <div id="gameover-panel">
        <div class="go-inner">
            <div class="go-eyebrow">Partie terminée</div>
            <h2 class="go-title">Rattrapé !</h2>
        </div>
        <div class="go-stats">
            <div class="go-stat">
                <div class="go-stat-val" id="go-score">0</div>
                <div class="go-stat-key">Score</div>
            </div>
            <div class="go-stat">
                <div class="go-stat-val" id="go-coins">0</div>
                <div class="go-stat-key">Pièces</div>
            </div>
            <div class="go-stat">
                <div class="go-stat-val"><span id="go-duration">0</span>s</div>
                <div class="go-stat-key">Survie</div>
            </div>
        </div>
        <div class="go-actions">
            <button class="btn btn-primary" onclick="openChest()">Ouvrir le loot</button>
            <button class="btn btn-outline" onclick="restartGame()">Rejouer</button>
            <a href="{{ route('leaderboard.index') }}" class="btn btn-outline">Classement</a>
        </div>
    </div>
</div>

<!-- MODAL COFFRE -->
<div id="chest-modal">
    <div class="chest-box">
        <div id="chest-waiting">
            <div class="chest-tag">Loot Drop</div>
            <div class="chest-icon" id="chest-emoji">📦</div>
            <div class="chest-title" id="chest-title">COFFRE NORMAL</div>
            <div class="chest-desc" id="chest-desc">Ouvre pour débloquer un runner !</div>
            <button class="btn btn-gold btn-full" onclick="openChestRequest()">Ouvrir</button>
        </div>
        <div id="chest-result" class="chest-result">
            <div class="result-icon" id="result-emoji">🧑</div>
            <div class="result-name" id="result-name"></div>
            <div class="result-rarity" id="result-rarity"></div>
            <p id="result-msg" class="result-msg"></p>
            <button class="btn btn-primary btn-full" onclick="closeChest()">GG !</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/game.js'])
<script>
let selectedCharId = {{ $userCharacters->first()?->id ?? 'null' }};
let selectedDiff   = 'normal';
let selectedKeys   = 'arrows';
let phaserGame     = null;
let lastScore = 0, lastCoins = 0, lastDuration = 0, chestType = 'normal';

// Statistiques par personnage (vitesse, saut, couleur)
@php
$statsMap = [
    'mathis'    => ['speed_mult' => 0.92, 'jump_mult' => 1.12],
    'abdoulaye' => ['speed_mult' => 1.00, 'jump_mult' => 1.00],
    'lucas'     => ['speed_mult' => 1.12, 'jump_mult' => 0.90],
    'emma'      => ['speed_mult' => 0.88, 'jump_mult' => 1.18],
    'noah'      => ['speed_mult' => 1.18, 'jump_mult' => 0.85],
    'lea'       => ['speed_mult' => 0.94, 'jump_mult' => 1.08],
    'tom'       => ['speed_mult' => 1.06, 'jump_mult' => 1.02],
    'jade'      => ['speed_mult' => 0.98, 'jump_mult' => 1.12],
    'hugo'      => ['speed_mult' => 1.22, 'jump_mult' => 0.82],
    'karl'      => ['speed_mult' => 1.22, 'jump_mult' => 1.18],
    'aurelie'   => ['speed_mult' => 1.04, 'jump_mult' => 1.06],
    'alois'     => ['speed_mult' => 1.12, 'jump_mult' => 1.12],
    'adrien'    => ['speed_mult' => 1.35, 'jump_mult' => 1.25],
];
$charStatsById = [];
foreach ($userCharacters as $c) {
    $base = $statsMap[$c->slug] ?? ['speed_mult' => 1.0, 'jump_mult' => 1.0];
    $charStatsById[$c->id] = array_merge($base, ['color' => $c->color, 'name' => $c->name]);
}
@endphp
const charStatsById = {!! json_encode($charStatsById) !!};
function getCharStats() {
    return charStatsById[selectedCharId] || { speed_mult: 1.0, jump_mult: 1.0, color: '#1d4ed8', name: '?' };
}

function selectChar(el) {
    document.querySelectorAll('.char-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    selectedCharId = el.dataset.id;
}
function selectDiff(el) {
    document.querySelectorAll('.mode-card').forEach(b => b.classList.remove('active-normal','active-hard'));
    selectedDiff = el.dataset.diff;
    el.classList.add(selectedDiff === 'hard' ? 'active-hard' : 'active-normal');
    chestType = selectedDiff === 'hard' ? 'legendary' : 'normal';
}
function selectKeys(el) {
    document.querySelectorAll('.key-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    selectedKeys = el.dataset.keys;
    document.getElementById('keys-hint').textContent = selectedKeys === 'zqsd'
        ? 'Z Sauter · Q D Voie · S Baisser'
        : '↑ Sauter · ← → Voie · ↓ Baisser';
}

function _buildGame() {
    return new Phaser.Game({
        type: Phaser.AUTO, width: 880, height: 420,
        parent: 'game-canvas-wrapper',
        backgroundColor: '#9fd3ee',
        physics: { default: 'arcade', arcade: { debug: false } },
        scene: _streetSceneClass()
    });
}
function startGame() {
    document.getElementById('pregame-panel').style.display  = 'none';
    document.getElementById('gameover-panel').style.display = 'none';
    document.getElementById('game-container').style.display = 'flex';
    if (phaserGame) phaserGame.destroy(true);
    phaserGame = _buildGame();
}
function restartGame() {
    document.getElementById('gameover-panel').style.display = 'none';
    document.getElementById('game-container').style.display = 'flex';
    if (phaserGame) phaserGame.destroy(true);
    phaserGame = _buildGame();
}

// ============================================================
//  SCÈNE PRINCIPALE
//  (encapsulée dans une fonction : Phaser est chargé via un module
//  Vite différé, donc Phaser.Scene n'existe pas encore au moment où
//  ce <script> classique s'exécute. On ne construit la classe qu'au
//  lancement du jeu, quand window.Phaser est garanti disponible.)
// ============================================================
function _streetSceneClass() { return class StreetScene extends Phaser.Scene {
    constructor() { super('StreetScene'); }

    create() {
        const W = this.scale.width, H = this.scale.height;
        this.W = W; this.H = H;

        // Perspective
        this.vp       = { x: W/2, y: H * 0.20 };
        this.pathNear = { left: W*0.07, right: W*0.93, y: H - 18 };
        this.pathFar  = { left: W/2-30, right: W/2+30, y: this.vp.y };

        // État
        this.lane               = 1;
        this.jumping            = false;
        this.jumpT              = 0;
        this.gameOver           = false;
        this.score              = 0;
        this.coins              = 0;
        this.elapsed            = 0;
        this.speed              = selectedDiff === 'hard' ? 0.68 : 0.48;
        this.objects            = [];
        this.laneChangeCooldown = 0;
        this.flashColor         = null;
        this.flashAlpha         = 0;
        // Le monstre n'existe pas au début : il surgit seulement après ta
        // première erreur, puis se rapproche un peu plus à chaque faute.
        this.monsterActive      = false;
        this.monsterDist        = 1.0;
        this.hitCooldown        = 0;
        this.spawnTimer         = 0;
        // Nouvelles mécaniques
        this.ducking            = false;
        this.duckT              = 0;
        this.leanDir            = 0;   // -1 gauche, 0 neutre, 1 droite
        this.leanT              = 0;   // timer lean (ms)
        // Biomes : forêt → ville → campagne
        this.biome              = 'forest';
        this._biomes            = ['forest','city','countryside'];
        this._biomeIdx          = 0;
        this._biomeTimer        = 0;
        this._biomeDuration     = 38000; // ms par biome
        this._biomeFlash        = 0;     // transition flash alpha

        // Countdown
        this.countdown       = 3;
        this.countdownActive = true;
        this.countTxt = this.add.text(W/2, H/2, '3', {
            font: 'bold 110px Bangers', color: '#fbbf24',
            stroke: '#1e293b', strokeThickness: 8
        }).setOrigin(0.5).setDepth(10);
        this.time.addEvent({ delay: 1000, repeat: 2, callback: () => {
            this.countdown--;
            if (this.countdown > 0) {
                this.countTxt.setText('' + this.countdown);
                this.cameras.main.shake(80, 0.005);
            } else {
                this.countTxt.setText('GO!');
                this.countTxt.setColor('#22c55e');
                this.time.delayedCall(500, () => {
                    this.countTxt.setAlpha(0);
                    this.countdownActive = false;
                });
            }
        }});

        // Graphics layers
        this.bgGfx     = this.add.graphics().setDepth(0);
        this.pathGfx   = this.add.graphics().setDepth(1);
        this.objGfx    = this.add.graphics().setDepth(2);
        this.monsterGfx= this.add.graphics().setDepth(2.5);
        this.playerGfx = this.add.graphics().setDepth(3);
        this.hudGfx    = this.add.graphics().setDepth(4);

        // HUD texts (couleurs sombres + halo clair : lisibles sur le ciel clair)
        this.scoreTxt = this.add.text(14, 12, 'SCORE: 0', {
            font: 'bold 20px Bangers', color: '#1d4ed8', letterSpacing: 2,
            stroke: '#ffffff', strokeThickness: 3,
        }).setDepth(5);
        this.coinTxt  = this.add.text(14, 38, '🪙 0',
            { font: '14px Inter', color: '#92702f' }).setDepth(5);
        this.diffTxt  = this.add.text(W-14, 12,
            selectedDiff === 'hard' ? 'HARD' : 'NORMAL', {
            font: 'bold 14px Bangers', letterSpacing: 3,
            color: selectedDiff==='hard' ? '#dc2626' : '#1d4ed8',
            stroke: '#ffffff', strokeThickness: 3,
        }).setOrigin(1,0).setDepth(5);
        // Nom + stats du perso en haut à droite
        const _cs = getCharStats();
        const _spd = Math.round((_cs.speed_mult - 1) * 100);
        const _jmp = Math.round((_cs.jump_mult  - 1) * 100);
        this.statTxt = this.add.text(W-14, 30,
            `${_cs.name || ''}  ⚡${_spd >= 0 ? '+' : ''}${_spd}%  ↑${_jmp >= 0 ? '+' : ''}${_jmp}%`,
            { font: '10px Inter', color: '#475569', stroke: '#ffffff', strokeThickness: 2 }
        ).setOrigin(1,0).setDepth(5);
        this.hintTxt  = this.add.text(W/2, H-11,
            selectedKeys === 'zqsd' ? 'Z=Sauter · Q/D=Voie · S=Baisser' : '↑=Sauter · ←/→=Voie · ↓=Baisser',
            { font: '10px Inter', color: '#5b5448' }).setOrigin(0.5).setDepth(5);

        // Touches
        const kb = this.input.keyboard;
        this.keys = {
            left:  kb.addKey('LEFT'),  right: kb.addKey('RIGHT'),
            up:    kb.addKey('UP'),    space: kb.addKey('SPACE'),
            down:  kb.addKey('DOWN'),
            q: kb.addKey('Q'), d: kb.addKey('D'), z: kb.addKey('Z'), s: kb.addKey('S'),
        };
        this.keys.left.on('down',  () => this.moveLane(-1));
        this.keys.right.on('down', () => this.moveLane(1));
        this.keys.q.on('down',     () => this.moveLane(-1));
        this.keys.d.on('down',     () => this.moveLane(1));

        // Pièces initiales
        for (let i = 0; i < 3; i++)
            this.objects.push({ type:'coin', lane: Phaser.Math.Between(0,2), depth: 0.12 + i*0.28 });
    }

    screenPos(lane, depth, jumpOff = 0) {
        const t   = depth * depth;
        const lx  = this.pathFar.left  + (this.pathNear.left  - this.pathFar.left)  * t;
        const rx  = this.pathFar.right + (this.pathNear.right - this.pathFar.right) * t;
        const y   = this.pathFar.y     + (this.pathNear.y     - this.pathFar.y)     * t;
        const lW  = (rx - lx) / 3;
        const cx  = lx + lW * lane + lW / 2;
        const sc  = 0.05 + t * 0.95;
        return { x: cx, y: y - jumpOff * lW * 0.7, scale: sc };
    }

    moveLane(dir) {
        if (this.gameOver || this.laneChangeCooldown > 0) return;
        const next = this.lane + dir;
        if (next >= 0 && next <= 2) {
            this.lane = next;
            this.laneChangeCooldown = 150;
            this.leanDir = dir;
            this.leanT   = 280; // durée du lean en ms
        }
    }

    flashScreen(col, alpha) { this.flashColor = col; this.flashAlpha = alpha; }

    update(time, delta) {
        if (this.gameOver) return;
        if (this.countdownActive) {
            this.drawBg(); this.drawPath(); this.drawMonster(); this.drawPlayer(0); return;
        }

        this.elapsed += delta;
        this.score    = Math.floor(this.elapsed / 80 + this.coins * 5);
        this.scoreTxt.setText('SCORE: ' + this.score);

        if (this.laneChangeCooldown > 0) this.laneChangeCooldown -= delta;
        if (this.hitCooldown > 0)        this.hitCooldown -= delta;

        const cs = getCharStats();
        const baseSpeed = (selectedDiff === 'hard' ? 0.68 : 0.48) * cs.speed_mult;
        this.speed = baseSpeed + (this.elapsed / 1000) * 0.007;

        // Cycle de biomes
        this._biomeTimer += delta;
        if (this._biomeFlash > 0) this._biomeFlash -= delta / 400;
        if (this._biomeTimer >= this._biomeDuration) {
            this._biomeTimer = 0;
            this._biomeIdx   = (this._biomeIdx + 1) % this._biomes.length;
            this.biome       = this._biomes[this._biomeIdx];
            this._biomeFlash = 1.0;
            this._bld = null; this._trees = null; this._clouds = null; // reset décors
        }

        // Lean (glissade visuelle au virage)
        if (this.leanT > 0) { this.leanT -= delta; if (this.leanT <= 0) { this.leanT = 0; this.leanDir = 0; } }

        // Saut
        const jumpKey = this.keys.up.isDown || this.keys.space.isDown ||
                        (selectedKeys === 'zqsd' && this.keys.z.isDown);
        if (jumpKey && !this.jumping && !this.ducking) { this.jumping = true; this.jumpT = 0; }
        if (this.jumping) {
            this.jumpT += delta / (520 * cs.jump_mult);
            if (this.jumpT >= 1) { this.jumping = false; this.jumpT = 0; }
        }
        const jumpOff = this.jumping ? Math.sin(this.jumpT * Math.PI) : 0;

        // Baisser (duck)
        const duckKey = this.keys.down.isDown || (selectedKeys === 'zqsd' && this.keys.s.isDown);
        if (duckKey && !this.jumping) {
            this.ducking = true; this.duckT = 400; // maintenu tant que touche enfoncée
        } else if (this.ducking) {
            this.duckT -= delta;
            if (this.duckT <= 0 || !duckKey) { this.ducking = false; this.duckT = 0; }
        }

        // Avancer objets
        const dt = delta / 1000;
        for (const o of this.objects) o.depth += this.speed * dt;

        // Collisions
        for (const o of this.objects) {
            if (o.resolved) continue;

            if (o.type === 'coin') {
                if (o.depth >= 0.90 && o.depth <= 1.01 && o.lane === this.lane) {
                    o.resolved = true; this.coins++;
                    this.coinTxt.setText('🪙 ' + this.coins);
                    this.flashScreen(0xfacc15, 0.10);
                }
            } else if (o.type === 'jump') {
                // Fenêtre large : sauter à n'importe quel moment pendant l'approche évite l'obstacle
                if (o.depth >= 0.78 && o.lane === this.lane) {
                    if (this.jumping) {
                        o.resolved = true;
                        if (this.monsterActive) this.monsterDist = Math.min(1.0, this.monsterDist + 0.012);
                    } else if (o.depth > 1.0 && this.hitCooldown <= 0) {
                        o.resolved = true;
                        this._hit();
                        if (this.monsterDist <= 0) { this.triggerGameOver(); return; }
                    }
                }
            } else if (o.type === 'lane') {
                if (o.depth >= 0.90 && o.depth <= 1.01 && o.lane === this.lane && this.hitCooldown <= 0) {
                    o.resolved = true;
                    this._hit();
                    if (this.monsterDist <= 0) { this.triggerGameOver(); return; }
                }
            } else if (o.type === 'duck') {
                // Obstacle bas : il faut se baisser pour passer dessous
                if (o.depth >= 0.82 && o.lane === this.lane) {
                    if (this.ducking) {
                        o.resolved = true;
                        if (this.monsterActive) this.monsterDist = Math.min(1.0, this.monsterDist + 0.010);
                    } else if (o.depth > 1.0 && this.hitCooldown <= 0) {
                        o.resolved = true;
                        this._hit();
                        if (this.monsterDist <= 0) { this.triggerGameOver(); return; }
                    }
                }
            } else if (o.type === 'hole') {
                // Trou dans la piste : il faut sauter pour passer
                if (o.depth >= 0.78 && o.lane === this.lane) {
                    if (this.jumping) {
                        o.resolved = true;
                        if (this.monsterActive) this.monsterDist = Math.min(1.0, this.monsterDist + 0.010);
                    } else if (o.depth > 1.0 && this.hitCooldown <= 0) {
                        o.resolved = true;
                        this._hit();
                        if (this.monsterDist <= 0) { this.triggerGameOver(); return; }
                    }
                }
            }
        }

        this.objects = this.objects.filter(o => o.depth <= 1.06);

        // Spawn
        this.spawnTimer += delta;
        const delay = Math.max(550, 1350 - this.elapsed * 0.035);
        if (this.spawnTimer >= delay) { this.spawnTimer = 0; this.spawnObject(); }

        // Le monstre ne pourchasse qu'une fois réveillé par une de tes erreurs
        // (cf. _hit) — tant que tu joues proprement, la voie est libre.
        if (this.monsterActive && this.monsterDist < 0.32)
            this.flashScreen(0xef4444, 0.04 + (0.32 - this.monsterDist) * 0.16);

        // Draw
        this.drawBg();
        this.drawPath();
        this.drawMonster();
        this.drawObjects(jumpOff);
        this.drawPlayer(jumpOff);
        this._drawHUD();
    }

    _hit() {
        if (!this.monsterActive) {
            // Première erreur : le monstre surgit au loin et se met en chasse.
            this.monsterActive = true;
            this.monsterDist   = 0.62;
        } else {
            // Chaque nouvelle erreur le rapproche un peu plus de toi.
            this.monsterDist -= 0.30;
        }
        this.hitCooldown = 850;
        this.flashScreen(0xef4444, 0.30);
        this.cameras.main.shake(200, 0.011);
    }

    spawnObject() {
        const r = Math.random();
        // Les trous n'apparaissent qu'après 45 s, les obstacles duck après 20 s
        const canHole = this.elapsed > 45000;
        const canDuck = this.elapsed > 20000;

        if (r < 0.37) {
            const base = Phaser.Math.Between(0,2);
            const cnt  = Phaser.Math.Between(1,3);
            for (let i = 0; i < cnt; i++)
                this.objects.push({ type:'coin', lane: Math.min(2, base+i), depth: 0.01 + i*0.045 });
        } else if (r < 0.58) {
            this.objects.push({ type:'jump', lane: Phaser.Math.Between(0,2), depth: 0.01 });
        } else if (r < 0.72 || !canDuck) {
            const free    = Phaser.Math.Between(0,2);
            const blocked = [0,1,2].filter(l => l !== free);
            const cnt     = Math.random() < 0.45 ? 1 : 2;
            blocked.slice(0, cnt).forEach(l =>
                this.objects.push({ type:'lane', lane: l, depth: 0.01 }));
        } else if (r < 0.87 || !canHole) {
            // Obstacle duck : barre basse, il faut se baisser
            this.objects.push({ type:'duck', lane: Phaser.Math.Between(0,2), depth: 0.01 });
        } else {
            // Trou dans la piste : il faut sauter
            this.objects.push({ type:'hole', lane: Phaser.Math.Between(0,2), depth: 0.01 });
        }
    }

    triggerGameOver() {
        if (this.gameOver) return;
        this.gameOver = true;
        lastScore = this.score; lastCoins = this.coins;
        lastDuration = Math.floor(this.elapsed / 1000);
        this.flashScreen(0xef4444, 0.50);
        this.cameras.main.shake(380, 0.018);
        fetch('{{ route("game.score") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ score: lastScore, coins_collected: lastCoins, difficulty: selectedDiff, duration: lastDuration, character_id: selectedCharId })
        }).then(r => r.json()).then(d => { if (d.success) chestType = d.chest_type; });
        this.time.delayedCall(850, () => {
            document.getElementById('game-container').style.display  = 'none';
            document.getElementById('gameover-panel').style.display  = 'block';
            document.getElementById('go-score').textContent    = lastScore;
            document.getElementById('go-coins').textContent    = lastCoins;
            document.getElementById('go-duration').textContent = lastDuration;
        });
    }

    // ── DESSIN ──────────────────────────────────────────────

    drawBg() {
        if      (this.biome === 'forest')      this._drawForestBg();
        else if (this.biome === 'city')        this._drawCityBg();
        else                                   this._drawCountrysideBg();
        // Flash de transition entre biomes
        if (this._biomeFlash > 0) {
            const g = this.bgGfx;
            g.fillStyle(0xffffff, Math.min(0.55, this._biomeFlash * 0.55));
            g.fillRect(0, 0, this.W, this.H);
        }
    }

    _drawForestBg() {
        const g = this.bgGfx; g.clear();
        const fy = this.vp.y;
        // Ciel filtré à travers les arbres (vert sombre → vert clair)
        g.fillGradientStyle(0x1a3320, 0x1a3320, 0x4a7c42, 0x4a7c42, 1);
        g.fillRect(0, 0, this.W, fy + 25);
        // Sol (terre battue, mousses)
        g.fillStyle(0x3d2b1a, 1);
        g.fillRect(0, fy + 25, this.W, this.H - fy - 25);
        g.fillStyle(0x2d5a27, 0.5);
        g.fillRect(0, fy + 25, this.W, 10);
        // Rayons de lumière filtrée
        [0.22, 0.55, 0.78].forEach((rx, i) => {
            g.fillStyle(0xc8f0a0, 0.06 + i*0.02);
            g.fillTriangle(this.W*rx, 0, this.W*rx-18, fy+30, this.W*rx+18, fy+30);
        });
        // Grands arbres sur les côtés (troncs épais + canopée)
        if (!this._trees) this._trees = [
            {x:14,  h:150, tw:22, c:0x2d1a0a, fc:0x1e4d18},
            {x:55,  h:110, tw:16, c:0x3d2510, fc:0x2a6620},
            {x:this.W-24, h:155, tw:24, c:0x2d1a0a, fc:0x1e4d18},
            {x:this.W-60, h:115, tw:18, c:0x3d2510, fc:0x2a6620},
            {x:this.W*0.18, h:80, tw:12, c:0x4a2e12, fc:0x336b1c},
            {x:this.W*0.82, h:85, tw:13, c:0x4a2e12, fc:0x336b1c},
        ];
        this._trees.forEach(t => {
            const ty = fy - t.h + 28;
            // Ombre du tronc
            g.fillStyle(0x0a0a0a, 0.18);
            g.fillRect(t.x+4, ty+t.h*0.3, t.tw-2, t.h*0.7);
            // Tronc
            g.fillStyle(t.c, 1);
            g.fillRect(t.x-t.tw/2, ty, t.tw, t.h);
            // Mousse sur le tronc
            g.fillStyle(0x3d7a2a, 0.4);
            g.fillRect(t.x-t.tw/2, ty+t.h*0.6, t.tw*0.6, t.h*0.4);
            // Canopée (plusieurs couches)
            g.fillStyle(t.fc, 1);
            g.fillCircle(t.x, ty-10, t.tw*2.8);
            g.fillStyle(0x2a7025, 0.8);
            g.fillCircle(t.x+t.tw*0.6, ty-5, t.tw*2.0);
            g.fillStyle(0x4a9040, 0.6);
            g.fillCircle(t.x-t.tw*0.4, ty-18, t.tw*1.8);
        });
        // Fougères / sous-bois au sol
        [this.pathNear.left-8, this.pathNear.right+8].forEach((fx, si) => {
            const dir = si === 0 ? -1 : 1;
            for (let i = 0; i < 4; i++) {
                g.fillStyle(0x2d7020, 0.7);
                g.fillTriangle(fx, fy+20, fx+dir*(10+i*8), fy+2, fx+dir*(18+i*8), fy+22);
            }
        });
        // Lucioles (points lumineux animés)
        for (let i = 0; i < 6; i++) {
            const lx = this.W*(0.05 + i*0.16 + Math.sin(this.elapsed*0.001+i)*0.04);
            const ly = fy*(0.4 + Math.sin(this.elapsed*0.0015+i*1.3)*0.3);
            const la = 0.3 + Math.sin(this.elapsed*0.004+i)*0.3;
            g.fillStyle(0xd4ff80, la); g.fillCircle(lx, ly, 2.5);
        }
    }

    _drawCityBg() {
        const g = this.bgGfx; g.clear();
        const fy = this.vp.y;
        // Ciel nuit/aube urbain
        g.fillGradientStyle(0x9fd3ee, 0x9fd3ee, 0xeaf6fb, 0xeaf6fb, 1);
        g.fillRect(0, 0, this.W, fy + 25);
        const sunX = this.W * 0.80, sunY = fy * 0.32;
        g.fillStyle(0xfff3c4, 0.45); g.fillCircle(sunX, sunY, 42);
        g.fillStyle(0xfffaeb, 0.95); g.fillCircle(sunX, sunY, 22);
        if (!this._clouds) this._clouds = [
            {x:this.W*0.16,y:fy*0.32,s:1.00},{x:this.W*0.46,y:fy*0.58,s:0.70},{x:this.W*0.66,y:fy*0.24,s:0.85}
        ];
        this._clouds.forEach(c => {
            g.fillStyle(0xffffff, 0.65);
            g.fillEllipse(c.x,c.y,72*c.s,22*c.s); g.fillEllipse(c.x+28*c.s,c.y-9*c.s,46*c.s,20*c.s);
            g.fillEllipse(c.x-26*c.s,c.y-5*c.s,42*c.s,17*c.s);
        });
        g.fillStyle(0xe7e1d6, 1); g.fillRect(0, fy+25, this.W, this.H-fy-25);
        if (!this._bld) this._bld = [
            {x:8,  w:52,h:105,c:0xc7d4e3,wins:[[12,18],[12,40],[12,62],[32,18],[32,40]]},
            {x:66, w:33,h:72, c:0xe9ddc9,wins:[[8,14],[8,34],[20,14],[20,34]]},
            {x:106,w:58,h:128,c:0xd6e3d2,wins:[[10,18],[10,44],[10,70],[30,18],[30,44],[30,70],[46,18],[46,44]]},
            {x:178,w:28,h:55, c:0xc7d4e3,wins:[[6,12],[6,32],[16,12]]},
            {x:this.W-72, w:48,h:98, c:0xe9ddc9,wins:[[8,18],[8,44],[28,18],[28,44]]},
            {x:this.W-128,w:38,h:78, c:0xd6e3d2,wins:[[7,12],[7,32],[22,12],[22,32]]},
            {x:this.W-190,w:54,h:118,c:0xc7d4e3,wins:[[8,18],[8,48],[8,78],[28,18],[28,48],[28,78],[44,18],[44,48]]},
            {x:this.W-252,w:33,h:62, c:0xe9ddc9,wins:[[7,12],[7,32],[20,12]]},
        ];
        this._bld.forEach(b => {
            const by = fy - b.h + 22;
            g.fillStyle(b.c,1); g.fillRect(b.x,by,b.w,b.h);
            g.lineStyle(1,0xffffff,0.55); g.strokeRect(b.x,by,b.w,b.h);
            b.wins.forEach(([wx,wy],i) => { if(i%3===1)return; g.fillStyle(0xfff3d6,0.8); g.fillRect(b.x+wx,by+wy,7,5); });
        });
        [this.pathNear.left-15, this.pathNear.right+15].forEach(lx => {
            const dir = lx < this.W/2 ? 1 : -1;
            g.fillStyle(0x9aa6b2,1); g.fillRect(lx-2,fy-25,4,50); g.fillRect(lx,fy-24,dir*14,2);
            g.fillStyle(0xfff3c4,0.9); g.fillCircle(lx+dir*14,fy-23,3.5);
        });
    }

    _drawCountrysideBg() {
        const g = this.bgGfx; g.clear();
        const fy = this.vp.y;
        // Ciel dégagé (bleu clair)
        g.fillGradientStyle(0x87ceeb, 0x87ceeb, 0xd0ecf8, 0xd0ecf8, 1);
        g.fillRect(0, 0, this.W, fy + 25);
        // Soleil haut
        const sx = this.W*0.72, sy = fy*0.22;
        g.fillStyle(0xfffde0, 0.5); g.fillCircle(sx, sy, 38);
        g.fillStyle(0xfff8c0, 1);   g.fillCircle(sx, sy, 20);
        // Nuages duveteux
        if (!this._clouds) this._clouds = [
            {x:this.W*0.12,y:fy*0.28,s:1.1},{x:this.W*0.40,y:fy*0.45,s:0.80},{x:this.W*0.68,y:fy*0.20,s:0.95},{x:this.W*0.88,y:fy*0.55,s:0.65}
        ];
        this._clouds.forEach(c => {
            g.fillStyle(0xffffff,0.80); g.fillEllipse(c.x,c.y,80*c.s,26*c.s);
            g.fillEllipse(c.x+32*c.s,c.y-10*c.s,50*c.s,24*c.s); g.fillEllipse(c.x-28*c.s,c.y-6*c.s,46*c.s,20*c.s);
        });
        // Collines en arrière-plan
        g.fillStyle(0x6db85a, 0.7);
        g.fillEllipse(this.W*0.15, fy+10, this.W*0.5, 60);
        g.fillStyle(0x7acc68, 0.6);
        g.fillEllipse(this.W*0.75, fy+8, this.W*0.45, 50);
        g.fillStyle(0x8cd678, 0.5);
        g.fillEllipse(this.W*0.50, fy+15, this.W*0.38, 44);
        // Sol herbe
        g.fillStyle(0x5da832, 1); g.fillRect(0, fy+25, this.W, this.H-fy-25);
        g.fillStyle(0x74c43c, 0.5); g.fillRect(0, fy+25, this.W, 12);
        // Fleurs
        if (!this._trees) this._trees = [
            {x:this.pathNear.left-22,col:0xff6b8a},{x:this.pathNear.left-40,col:0xffd93d},
            {x:this.pathNear.right+22,col:0xff8c42},{x:this.pathNear.right+40,col:0xa8e6cf},
        ];
        this._trees.forEach(f => {
            g.fillStyle(0x3d8c1a,1); g.fillRect(f.x-1,fy+18,2,10);
            g.fillStyle(f.col,1);    g.fillCircle(f.x,fy+16,5);
        });
        // Poteaux en bois des clôtures (décoration latérale)
        for (let i = 0; i < 6; i++) {
            const px1 = this.pathNear.left - 8 - i*18;
            const px2 = this.pathNear.right + 8 + i*18;
            if (px1 > 0) { g.fillStyle(0xa0784a,1); g.fillRect(px1-2,fy+10,4,20); }
            if (px2 < this.W) { g.fillStyle(0xa0784a,1); g.fillRect(px2-2,fy+10,4,20); }
        }
    }

    drawPath() {
        const g = this.pathGfx; g.clear();
        const { left:fl, right:fr, y:fy } = this.pathFar;
        const { left:nl, right:nr, y:ny } = this.pathNear;
        const dashOff = (this.elapsed * this.speed * 0.00042) % (1/9);

        // ── Surface unique, ZÉRO séparation de voie ──────────
        if (this.biome === 'forest') {
            // Sol latéral (mousse)
            g.fillStyle(0x2d5a1a, 1);
            g.fillPoints([{x:0,y:fy},{x:fl,y:fy},{x:nl,y:ny},{x:0,y:ny}], true);
            g.fillPoints([{x:fr,y:fy},{x:this.W,y:fy},{x:this.W,y:ny},{x:nr,y:ny}], true);
            // Chemin unique : terre battue
            g.fillStyle(0x6b4a28, 1);
            g.fillPoints([{x:fl,y:fy},{x:fr,y:fy},{x:nr,y:ny},{x:nl,y:ny}], true);
            // Nuances de texture (côtés plus sombres)
            g.fillStyle(0x4e3318, 0.35);
            g.fillPoints([{x:fl,y:fy},{x:fl+(fr-fl)*0.18,y:fy},{x:nl+(nr-nl)*0.18,y:ny},{x:nl,y:ny}], true);
            g.fillPoints([{x:fl+(fr-fl)*0.82,y:fy},{x:fr,y:fy},{x:nr,y:ny},{x:nl+(nr-nl)*0.82,y:ny}], true);
            // Pierres irrégulières sur le chemin (décoration, pas séparation)
            for (let d=0;d<6;d++) {
                const t=(d/5); const mid=(fl+fr)/2;
                const px=mid+(d%3-1)*((nr-nl)*0.28), py=fy+(ny-fy)*t;
                g.fillStyle(0x8a6840,0.3); g.fillCircle(px,py,(2+d%2)*t*t*6);
            }
            // Bord organique (pas de ligne droite)
            g.lineStyle(4, 0x2a5018, 0.5);
            g.beginPath(); g.moveTo(fl,fy); g.lineTo(nl,ny); g.strokePath();
            g.beginPath(); g.moveTo(fr,fy); g.lineTo(nr,ny); g.strokePath();

        } else if (this.biome === 'city') {
            // Trottoirs
            g.fillStyle(0xd9d2c5,1);
            g.fillPoints([{x:0,y:fy},{x:fl,y:fy},{x:nl,y:ny},{x:0,y:ny}],true);
            g.fillPoints([{x:fr,y:fy},{x:this.W,y:fy},{x:this.W,y:ny},{x:nr,y:ny}],true);
            // Asphalte unifié, pas de lignes centrales
            g.fillStyle(0xa8a29e,1);
            g.fillPoints([{x:fl,y:fy},{x:fr,y:fy},{x:nr,y:ny},{x:nl,y:ny}],true);
            // Légère variation de teinte (centre légèrement plus clair = usure)
            g.fillStyle(0xb8b2a8,0.4);
            g.fillPoints([{x:fl+(fr-fl)*0.2,y:fy},{x:fl+(fr-fl)*0.8,y:fy},{x:nl+(nr-nl)*0.8,y:ny},{x:nl+(nr-nl)*0.2,y:ny}],true);
            // Petites fissures d'asphalte (décoration)
            g.lineStyle(1,0x8a847e,0.35);
            for(let d=0;d<4;d++){
                const t=0.2+d*0.18; const cx=(fl+fr)/2+(d%2*2-1)*((fr-fl)*0.12);
                const cy=fy+(ny-fy)*t;
                g.beginPath(); g.moveTo(cx-8*t,cy); g.lineTo(cx+6*t,cy+4*t); g.strokePath();
            }
            // Kerb (trottoir)
            g.fillStyle(0xb0a898,1);
            g.fillRect(0,fy-3,nl+1,3); g.fillRect(nr-1,fy-3,this.W-nr+1,3);

        } else { // countryside
            // Herbe latérale
            g.fillStyle(0x5da832,1);
            g.fillPoints([{x:0,y:fy},{x:fl,y:fy},{x:nl,y:ny},{x:0,y:ny}],true);
            g.fillPoints([{x:fr,y:fy},{x:this.W,y:fy},{x:this.W,y:ny},{x:nr,y:ny}],true);
            // Chemin unique : terre battue beige
            g.fillStyle(0xc4a060,1);
            g.fillPoints([{x:fl,y:fy},{x:fr,y:fy},{x:nr,y:ny},{x:nl,y:ny}],true);
            g.fillStyle(0xa88448,0.3);
            g.fillPoints([{x:fl,y:fy},{x:fl+(fr-fl)*0.22,y:fy},{x:nl+(nr-nl)*0.22,y:ny},{x:nl,y:ny}],true);
            g.fillPoints([{x:fl+(fr-fl)*0.78,y:fy},{x:fr,y:fy},{x:nr,y:ny},{x:nl+(nr-nl)*0.78,y:ny}],true);
            // Herbe qui dépasse sur les bords du chemin
            g.lineStyle(3,0x68b830,0.6);
            g.beginPath(); g.moveTo(fl,fy); g.lineTo(nl,ny); g.strokePath();
            g.beginPath(); g.moveTo(fr,fy); g.lineTo(nr,ny); g.strokePath();
        }
    }

    drawMonster() {
        const g = this.monsterGfx; g.clear();
        // Tant qu'aucune erreur n'a été commise, le monstre n'existe pas
        // encore : la voie reste calme et dégagée.
        if (!this.monsterActive) return;

        // Il nous pourchasse DANS NOTRE DOS — hors-champ, là où la caméra ne
        // regarde jamais. Impossible (et étrange) de le poser sur la route
        // devant nous : on le montre comme une ombre menaçante qui surgit du
        // bord inférieur de l'écran, juste derrière la caméra, et qui grandit
        // jusqu'à nous engloutir. Le joueur (dessiné par-dessus) reste net.
        const dist = this.monsterDist;
        const p = Phaser.Math.Clamp((0.62 - dist) / 0.62, 0, 1); // 0 = vient de surgir, 1 = nous attrape
        if (p <= 0.02) return;

        const cx = this.W / 2, baseY = this.H + 6;
        const w = 100 + p * 300;
        const h = 30 + p * 220;

        // Halo rouge inquiétant
        g.fillStyle(0xef4444, 0.05 + p * 0.15);
        g.fillEllipse(cx, baseY, w * 1.5, h * 1.3);

        // Masse sombre qui grimpe depuis le bas de l'écran
        g.fillStyle(0x252a36, 0.55 + p * 0.35);
        g.fillEllipse(cx, baseY, w, h);
        g.fillStyle(0x1a1e27, 0.55 + p * 0.35);
        g.fillEllipse(cx, baseY + h * 0.12, w * 0.6, h * 0.8);

        // Yeux rouges luisants — n'apparaissent qu'à mi-approche
        if (p > 0.30) {
            const eyeA = Math.min(1, (p - 0.30) / 0.4);
            const eyePulse = 0.6 + Math.sin(this.elapsed * 0.012) * 0.4;
            const eyeY = baseY - h * 0.62, eyeR = 4 + p * 6;
            g.fillStyle(0xef4444, eyeA * eyePulse);
            g.fillCircle(cx - w*0.11, eyeY, eyeR);
            g.fillCircle(cx + w*0.11, eyeY, eyeR);
            g.fillStyle(0xffffff, eyeA * 0.5);
            g.fillCircle(cx - w*0.11 - eyeR*0.3, eyeY - eyeR*0.3, eyeR*0.32);
            g.fillCircle(cx + w*0.11 - eyeR*0.3, eyeY - eyeR*0.3, eyeR*0.32);
        }

        // Griffes qui s'agrippent sur les bords de l'écran quand il est tout proche
        if (p > 0.62) {
            const cA = (p - 0.62) / 0.38;
            g.fillStyle(0x1a1e27, cA * 0.85);
            [-1, 1].forEach(side => {
                const clawX = cx + side * w * 0.46;
                for (let i = -1; i <= 1; i++) {
                    g.fillTriangle(
                        clawX + i*9 - 4, baseY - h*0.40,
                        clawX + i*9,     baseY - h*0.62,
                        clawX + i*9 + 4, baseY - h*0.40
                    );
                }
            });
        }
    }

    drawObjects(jumpOff) {
        const g = this.objGfx; g.clear();
        const sorted = [...this.objects].sort((a,b) => a.depth-b.depth);
        const bm = this.biome;

        for (const o of sorted) {
            if (o.depth <= 0 || o.depth > 1.02) continue;
            const pos = this.screenPos(o.lane, o.depth);
            const { x, y, scale: sc } = pos;
            const t2 = o.depth * o.depth;
            const lx2 = this.pathFar.left  + (this.pathNear.left  - this.pathFar.left)  * t2;
            const rx2 = this.pathFar.right + (this.pathNear.right - this.pathFar.right) * t2;
            const lW  = (rx2 - lx2) / 3;

            // ── PIÈCE ──────────────────────────────────────────
            if (o.type === 'coin') {
                const r = 9*sc;
                const coinCol = bm==='forest' ? 0xa8d050 : (bm==='countryside' ? 0xf4d03f : 0xfacc15);
                g.fillStyle(coinCol, 1); g.fillCircle(x, y, r);
                g.fillStyle(0xfefce8, 0.8); g.fillCircle(x-r*0.28, y-r*0.28, r*0.4);
                g.lineStyle(1.5*sc, 0xca9a0a, 1); g.strokeCircle(x, y, r);

            // ── OBSTACLE SAUT (traverse tout le chemin) ────────
            } else if (o.type === 'jump') {
                // L'obstacle saut est une CRÊTE qui traverse tout le chemin d'un bord à l'autre
                const oh = 16*sc, oy = y - oh*0.5;
                const jx = lx2 - 6*sc, jw = (rx2 - lx2) + 12*sc; // bord à bord + débord
                if (bm === 'forest') {
                    // Réseau de grosses racines traversant tout le chemin
                    g.fillStyle(0x4a2a0a, 1);
                    g.fillRect(jx, oy+oh*0.2, jw, oh*0.5);
                    // Racines individuelles (3 bosses)
                    for (let n=0;n<5;n++) {
                        const rx3 = jx + jw*(0.1+n*0.18);
                        const rh2 = (6+n%3*3)*sc;
                        g.fillStyle(0x6b3d14,1); g.fillEllipse(rx3, oy+oh*0.35, 16*sc, rh2*2);
                    }
                    // Mousse sur les racines
                    g.fillStyle(0x3a7020,0.5); g.fillRect(jx,oy+oh*0.15,jw,oh*0.2);
                    // Extension hors-chemin (racines sortent des arbres)
                    g.fillStyle(0x3a2008,0.6);
                    g.fillRect(jx-12*sc,oy+oh*0.3,14*sc,oh*0.35);
                    g.fillRect(jx+jw-2*sc,oy+oh*0.3,14*sc,oh*0.35);
                } else if (bm === 'countryside') {
                    // Talus de terre traversant le chemin
                    g.fillStyle(0x8a6840,1); g.fillRect(jx,oy,jw,oh);
                    g.fillStyle(0x5a8a28,0.7); g.fillRect(jx,oy,jw,oh*0.35);
                    g.fillStyle(0x6a5030,1);
                    for(let n=0;n<4;n++) g.fillEllipse(jx+jw*(0.12+n*0.22),oy+oh*0.55,(10+n%2*5)*sc,oh*0.6);
                    g.lineStyle(2*sc,0x4a7a18,0.8);
                    g.beginPath(); g.moveTo(jx,oy); g.lineTo(jx+jw,oy); g.strokePath();
                } else {
                    // Dos-d'âne asphalte + marquage jaune
                    g.fillStyle(0x6b7280,1); g.fillRect(jx,oy,jw,oh);
                    for(let s=0;s<Math.ceil(jw/(18*sc));s++){
                        g.fillStyle(0xfacc15,0.9); g.fillRect(jx+s*18*sc,oy,10*sc,oh);
                    }
                    g.lineStyle(2*sc,0xdc2626,1);
                    g.beginPath(); g.moveTo(jx,oy); g.lineTo(jx+jw,oy); g.strokePath();
                    g.beginPath(); g.moveTo(jx,oy+oh); g.lineTo(jx+jw,oy+oh); g.strokePath();
                }
                if (o.depth > 0.42) {
                    const a=Math.min(1,(o.depth-0.42)*2.5), p=Math.abs(Math.sin(this.elapsed*0.015))*0.4+0.6;
                    const ac = bm==='forest'?0xa8d050:(bm==='countryside'?0xf4d03f:0xfacc15);
                    g.fillStyle(ac,a*p); g.fillTriangle(x,oy-20*sc,x-11*sc,oy-7*sc,x+11*sc,oy-7*sc);
                    g.fillStyle(ac,a*p*0.5); g.fillTriangle(x,oy-32*sc,x-8*sc,oy-20*sc,x+8*sc,oy-20*sc);
                }

            // ── OBSTACLE VOIE (style Temple Run – vient du bord) ──
            } else if (o.type === 'lane') {
                // lane 0 = obstacle qui vient du BORD GAUCHE du chemin
                // lane 1 = obstacle central (sol / milieu)
                // lane 2 = obstacle qui vient du BORD DROIT du chemin
                const pathW = rx2 - lx2;
                const bh = 70*sc;

                if (bm === 'forest') {
                    if (o.lane === 0) {
                        // Grosse branche / tronc depuis le bord gauche → pénètre dans le chemin
                        const reach = pathW * 0.52;          // jusqu'où la branche pénètre
                        const trunkW = 18*sc, trunkH = 90*sc;
                        // Tronc ancré hors-chemin gauche
                        g.fillStyle(0x4a2c0e,1); g.fillRect(lx2-trunkW*0.7, y-trunkH, trunkW, trunkH);
                        // Branche horizontale qui s'étend vers le centre
                        g.fillStyle(0x4a2c0e,1);
                        g.fillRect(lx2-trunkW*0.4, y-bh, reach, 14*sc);
                        // Écorce
                        g.fillStyle(0x3a2008,0.5); g.fillRect(lx2,y-bh+2*sc,reach*0.7,5*sc);
                        // Racines au sol
                        g.fillStyle(0x5c3a18,1);
                        g.fillTriangle(lx2, y, lx2+trunkW*0.5, y-bh*0.35, lx2+trunkW, y);
                        // Feuilles à l'extrémité
                        g.fillStyle(0x2d7020,0.8);
                        g.fillEllipse(lx2+reach+4*sc, y-bh+7*sc, 28*sc, 20*sc);
                        g.fillStyle(0x3d8a28,0.65); g.fillEllipse(lx2+reach-4*sc, y-bh-4*sc, 20*sc,16*sc);
                    } else if (o.lane === 2) {
                        // Miroir : branche depuis le bord droit
                        const reach = pathW * 0.52;
                        const trunkW = 18*sc, trunkH = 90*sc;
                        g.fillStyle(0x4a2c0e,1); g.fillRect(rx2-trunkW*0.3, y-trunkH, trunkW, trunkH);
                        g.fillStyle(0x4a2c0e,1);
                        g.fillRect(rx2-reach, y-bh, reach+trunkW*0.4, 14*sc);
                        g.fillStyle(0x3a2008,0.5); g.fillRect(rx2-reach*0.7,y-bh+2*sc,reach*0.7,5*sc);
                        g.fillStyle(0x5c3a18,1);
                        g.fillTriangle(rx2,y, rx2-trunkW*0.5,y-bh*0.35, rx2-trunkW,y);
                        g.fillStyle(0x2d7020,0.8); g.fillEllipse(rx2-reach-4*sc,y-bh+7*sc,28*sc,20*sc);
                        g.fillStyle(0x3d8a28,0.65); g.fillEllipse(rx2-reach+4*sc,y-bh-4*sc,20*sc,16*sc);
                    } else {
                        // Lane 1 : gros tronc central (sort du sol)
                        const tw = 22*sc, by2 = y - bh;
                        g.fillStyle(0x4a2c0e,1); g.fillRect(x-tw*0.5,by2,tw,bh);
                        g.fillStyle(0x3a2008,0.5); g.fillRect(x-tw*0.35,by2+bh*0.1,tw*0.18,bh*0.7);
                        g.fillStyle(0x3d7a20,0.55); g.fillRect(x-tw*0.5,by2+bh*0.55,tw,bh*0.14);
                        g.fillStyle(0x5c3a18,1);
                        g.fillTriangle(x-tw*0.8,y, x-tw*0.1,by2+bh*0.75, x+tw*0.2,y);
                        g.fillTriangle(x+tw*0.8,y, x+tw*0.1,by2+bh*0.75, x-tw*0.2,y);
                    }

                } else if (bm === 'city') {
                    if (o.lane === 0) {
                        // Mur / pilier depuis le bord gauche
                        const reach = pathW * 0.48;
                        g.fillStyle(0x6b7280,1); g.fillRect(0,y-bh,lx2+reach,bh);
                        g.fillStyle(0x9ca3af,0.4); g.fillRect(0,y-bh,lx2+reach,bh*0.22);
                        g.fillStyle(0xf97316,1); g.fillRect(lx2+reach-4*sc,y-bh,6*sc,bh);
                        // Rayures danger
                        for(let s=0;s<4;s++){
                            g.fillStyle(0xfacc15,0.8); g.fillRect(lx2+reach-4*sc,y-bh+s*bh/4,6*sc,bh*0.12);
                        }
                        g.fillStyle(0x374151,1); g.fillRect(0,y-bh,lx2,bh);
                    } else if (o.lane === 2) {
                        // Mur depuis le bord droit
                        const reach = pathW * 0.48;
                        g.fillStyle(0x6b7280,1); g.fillRect(rx2-reach,y-bh,this.W-(rx2-reach),bh);
                        g.fillStyle(0x9ca3af,0.4); g.fillRect(rx2-reach,y-bh,this.W-(rx2-reach),bh*0.22);
                        g.fillStyle(0xf97316,1); g.fillRect(rx2-reach-2*sc,y-bh,6*sc,bh);
                        for(let s=0;s<4;s++){
                            g.fillStyle(0xfacc15,0.8); g.fillRect(rx2-reach-2*sc,y-bh+s*bh/4,6*sc,bh*0.12);
                        }
                        g.fillStyle(0x374151,1); g.fillRect(rx2,y-bh,this.W-rx2,bh);
                    } else {
                        // Barrière centrale (béton + rayures)
                        const bw2 = pathW*0.36, bx2 = x-bw2*0.5, by2 = y-bh;
                        g.fillStyle(0x6b7280,1); g.fillRect(bx2,by2,bw2,bh);
                        g.fillStyle(0xfca5a5,0.3); g.fillRect(bx2+3,by2+3,bw2-6,bh*0.2);
                        g.fillStyle(0xf9fafb,0.9); g.fillRect(bx2,by2+bh*0.38,bw2,bh*0.14);
                        g.fillStyle(0xfacc15,0.85); g.fillRect(bx2,by2+bh*0.64,bw2,bh*0.10);
                        g.lineStyle(1.5*sc,0x374151,1); g.strokeRect(bx2,by2,bw2,bh);
                        g.fillStyle(0x1f2937,1); g.fillRect(bx2+bw2*0.3,y-5*sc,bw2*0.4,8*sc);
                    }

                } else { // countryside
                    if (o.lane === 0) {
                        // Buisson dense / talus depuis la gauche
                        const reach = pathW * 0.50;
                        g.fillStyle(0x4a7c1a,1); g.fillRect(0,y-bh,lx2+reach,bh);
                        g.fillStyle(0x5da832,0.7); g.fillRect(0,y-bh,lx2+reach,bh*0.3);
                        // Branches dépassant
                        for(let b=0;b<3;b++){
                            g.fillStyle(0x3d6618,0.8);
                            g.fillEllipse(lx2+reach*(0.3+b*0.22),y-bh*(0.5+b*0.1),22*sc,14*sc);
                        }
                        g.fillStyle(0x5da832,1); g.fillEllipse(lx2+reach+4*sc,y-bh*0.55,30*sc,22*sc);
                        g.fillStyle(0x2d5a1a,1); g.fillRect(0,y-bh,lx2,bh);
                    } else if (o.lane === 2) {
                        // Buisson depuis la droite
                        const reach = pathW * 0.50;
                        g.fillStyle(0x4a7c1a,1); g.fillRect(rx2-reach,y-bh,this.W-(rx2-reach),bh);
                        g.fillStyle(0x5da832,0.7); g.fillRect(rx2-reach,y-bh,this.W-(rx2-reach),bh*0.3);
                        for(let b=0;b<3;b++){
                            g.fillStyle(0x3d6618,0.8);
                            g.fillEllipse(rx2-reach*(0.3+b*0.22),y-bh*(0.5+b*0.1),22*sc,14*sc);
                        }
                        g.fillStyle(0x5da832,1); g.fillEllipse(rx2-reach-4*sc,y-bh*0.55,30*sc,22*sc);
                        g.fillStyle(0x2d5a1a,1); g.fillRect(rx2,y-bh,this.W-rx2,bh);
                    } else {
                        // Rocher central
                        const rw = pathW*0.28, rh = bh*0.65;
                        g.fillStyle(0x8a7a6a,1); g.fillEllipse(x,y-rh*0.5,rw,rh);
                        g.fillStyle(0xb0a090,0.5); g.fillEllipse(x-rw*0.1,y-rh*0.7,rw*0.45,rh*0.35);
                        g.lineStyle(1.5*sc,0x6a5a4a,0.7); g.strokeEllipse(x,y-rh*0.5,rw,rh);
                    }
                }

            // ── OBSTACLE BAISSE (DUCK – traverse tout le chemin, ancré aux bords) ──
            } else if (o.type === 'duck') {
                // Barre qui va D'UN ARBRE/MUR À L'AUTRE, couvre tout le chemin
                const barY = y - 34*sc;
                const barH = 9*sc;
                const dx = lx2 - 8*sc, dw = (rx2 - lx2) + 16*sc; // bord à bord

                if (bm === 'forest') {
                    // Troncs porteurs aux deux bords (font partie des arbres du décor)
                    const trunkW = 14*sc;
                    g.fillStyle(0x4a2c0e,1);
                    g.fillRect(lx2-trunkW*0.5, y-100*sc, trunkW, 100*sc); // gauche
                    g.fillRect(rx2-trunkW*0.5, y-100*sc, trunkW, 100*sc); // droite
                    // Grosse branche traversant tout le chemin
                    g.fillStyle(0x5a3510,1);
                    g.fillRect(dx, barY-barH*0.4, dw, barH*1.5);
                    // Nœuds et texture
                    g.fillStyle(0x3e2208,0.55); g.fillCircle(dx+dw*0.3,barY,5*sc); g.fillCircle(dx+dw*0.65,barY+barH*0.3,4*sc);
                    // Lianes et feuilles pendantes tout le long
                    g.fillStyle(0x2d7020,0.85);
                    for(let li=0;li<8;li++) g.fillEllipse(dx+dw*(0.04+li*0.12),barY+barH+9*sc,10*sc,13*sc);
                    g.lineStyle(1.5*sc,0x1e5010,0.6);
                    for(let li=0;li<5;li++){
                        const lx3=dx+dw*(0.1+li*0.18);
                        g.beginPath(); g.moveTo(lx3,barY+barH); g.lineTo(lx3+3*sc,barY+barH+16*sc); g.strokePath();
                    }
                } else if (bm === 'countryside') {
                    // Poteaux + barre de clôture traversant le chemin
                    const postW = 7*sc;
                    g.fillStyle(0x8a6030,1);
                    g.fillRect(lx2-postW*0.5,y-60*sc,postW,60*sc);
                    g.fillRect(rx2-postW*0.5,y-60*sc,postW,60*sc);
                    // Deux barres de bois
                    g.fillStyle(0xb8885a,1); g.fillRect(dx,barY-barH*0.5,dw,barH*1.3);
                    g.fillStyle(0xc89060,1); g.fillRect(dx,barY+barH*1.1,dw,barH*1.0);
                    g.fillStyle(0xd0a070,0.5); g.fillRect(dx,barY-barH*0.5,dw,barH*0.4);
                    // Fil barbelé
                    g.lineStyle(1.5*sc,0x888888,0.8);
                    g.beginPath(); g.moveTo(dx,barY+barH*2.5); g.lineTo(dx+dw,barY+barH*2.5); g.strokePath();
                } else {
                    // Tube / pipeline traversant la ruelle d'un mur à l'autre
                    g.fillStyle(0x374151,0.85);
                    g.fillRect(0,barY-barH*0.5,lx2,barH*1.5); // mur gauche
                    g.fillRect(rx2,barY-barH*0.5,this.W-rx2,barH*1.5); // mur droit
                    // Tuyau néon
                    g.fillStyle(0x22d3ee,0.92); g.fillRect(dx,barY,dw,barH);
                    g.fillStyle(0xcffafe,0.5); g.fillRect(dx,barY,dw,barH*0.4);
                    g.fillStyle(0x22d3ee,0.15); g.fillRect(dx-4,barY-5,dw+8,barH+10);
                    // Boulons aux extrémités
                    g.fillStyle(0x6b7280,1);
                    g.fillCircle(lx2+4*sc,barY+barH*0.5,5*sc); g.fillCircle(rx2-4*sc,barY+barH*0.5,5*sc);
                }
                if (o.depth > 0.45) {
                    const a=Math.min(1,(o.depth-0.45)*2.5), p=Math.abs(Math.sin(this.elapsed*0.018))*0.4+0.6;
                    const dc = bm==='forest'?0xa8d050:(bm==='countryside'?0xe8c44a:0x22d3ee);
                    g.fillStyle(dc,a*p);
                    g.fillTriangle(x, barY+barH+18*sc, x-10*sc, barY+barH+6*sc, x+10*sc, barY+barH+6*sc);
                }

            // ── TROU ──────────────────────────────────────────
            } else if (o.type === 'hole') {
                const hx = lx2+lW*o.lane+lW*0.05, hw=lW*0.90, hh=20*sc, hy=y-hh*0.5;
                const holeCol = bm==='forest'?0x1a3318:(bm==='countryside'?0x5a3a18:0x0a0a0f);
                const edgeCol = bm==='forest'?0x3a7a20:(bm==='countryside'?0x8a6040:0xf97316);
                g.fillStyle(holeCol,0.95); g.fillRect(hx,hy,hw,hh);
                g.fillStyle(edgeCol,0.7); g.fillRect(hx,hy,hw,2*sc); g.fillRect(hx,hy+hh-2*sc,hw,2*sc);
                g.fillStyle(edgeCol,0.3); g.fillRect(hx,hy,2*sc,hh); g.fillRect(hx+hw-2*sc,hy,2*sc,hh);
                g.fillStyle(edgeCol,0.15); g.fillRect(hx+4,hy+4,hw-8,hh-8);
                if (o.depth > 0.42) {
                    const a=Math.min(1,(o.depth-0.42)*2.5), p=Math.abs(Math.sin(this.elapsed*0.015))*0.4+0.6;
                    g.fillStyle(edgeCol,a*p);
                    g.fillTriangle(x,hy-18*sc,x-11*sc,hy-5*sc,x+11*sc,hy-5*sc);
                }
            }
        }
    }

    drawPlayer(jumpOff) {
        const g = this.playerGfx; g.clear();
        const pos = this.screenPos(this.lane, 0.99, jumpOff);
        const sc  = pos.scale * 0.92;
        const x = pos.x, y = pos.y;
        const ducking = this.ducking;
        const bounce  = (this.jumping || ducking) ? 0 : Math.sin(this.elapsed*0.014)*1.5;
        const leanOff = this.leanDir * Math.min(1, this.leanT / 280) * 6 * sc;

        // Couleur perso
        const cs = getCharStats();
        const hexStr = (cs.color || '#1d4ed8').replace('#','');
        const colMain  = parseInt(hexStr, 16);
        const r2 = Math.min(255, ((colMain>>16)&0xff)+80);
        const g2 = Math.min(255, ((colMain>>8) &0xff)+80);
        const b2 = Math.min(255, ((colMain)    &0xff)+80);
        const colLight = (r2<<16)|(g2<<8)|b2;
        const skinCol  = 0xd4a574;

        // Animation de course
        const runCycle = this.elapsed * 0.020;
        const legSwing = ducking ? 0 : (this.jumping ? 0 : Math.sin(runCycle) * 10 * sc);
        const armSwing = ducking ? 0 : (this.jumping ? 8*sc : Math.sin(runCycle + Math.PI) * 10 * sc);

        // ── Ombre ──
        g.fillStyle(0x000000, ducking ? 0.28 : 0.18);
        g.fillEllipse(x+leanOff*0.4, this.pathNear.y-6, ducking?46*sc:38*sc, ducking?10*sc:7*sc);

        if (ducking) {
            // ══ ACCROUPI ══
            // Ailes repliées
            g.fillStyle(colMain, 0.7);
            g.fillTriangle(x+leanOff-10*sc, y-22*sc, x+leanOff-26*sc, y-12*sc, x+leanOff-9*sc, y-10*sc);
            g.fillTriangle(x+leanOff+10*sc, y-22*sc, x+leanOff+26*sc, y-12*sc, x+leanOff+9*sc, y-10*sc);
            // Cuisses accroupies (horizontales)
            g.fillStyle(0x1a1a2e, 1);
            g.fillRect(x-18*sc+leanOff, y-14*sc, 14*sc, 9*sc);
            g.fillRect(x+4*sc+leanOff,  y-14*sc, 14*sc, 9*sc);
            // Tibias (vers le bas)
            g.fillRect(x-14*sc+leanOff, y-6*sc, 10*sc, 8*sc);
            g.fillRect(x+4*sc+leanOff,  y-6*sc, 10*sc, 8*sc);
            // Sandales/bandelettes
            g.fillStyle(colMain, 0.9);
            g.fillRect(x-16*sc+leanOff, y-1*sc, 12*sc, 4*sc);
            g.fillRect(x+4*sc+leanOff,  y-1*sc, 12*sc, 4*sc);
            // Corps penché en avant
            g.fillStyle(0x111827, 1);
            g.fillRoundedRect(x-13*sc+leanOff, y-30*sc, 26*sc, 17*sc, 4*sc);
            // Ceinture
            g.fillStyle(colMain, 0.85);
            g.fillRect(x-13*sc+leanOff, y-17*sc, 26*sc, 3*sc);
            // Bras vers l'avant
            g.fillStyle(skinCol, 1);
            g.fillRect(x-20*sc+leanOff, y-28*sc, 8*sc, 14*sc);
            g.fillRect(x+12*sc+leanOff, y-28*sc, 8*sc, 14*sc);
            g.fillStyle(0x111827, 0.8);
            g.fillRect(x-19*sc+leanOff, y-28*sc, 6*sc, 9*sc);
            g.fillRect(x+13*sc+leanOff, y-28*sc, 6*sc, 9*sc);
            // Cou court
            g.fillStyle(skinCol, 1);
            g.fillRect(x-4*sc+leanOff, y-36*sc, 8*sc, 7*sc);
            // Tête (ovale, penchée)
            g.fillStyle(0x1a1a2e, 1);
            g.fillEllipse(x+leanOff, y-44*sc, 22*sc, 20*sc);
            // Masque
            g.fillStyle(colMain, 0.92);
            g.fillRect(x-10*sc+leanOff, y-42*sc, 20*sc, 8*sc);
            // Yeux
            g.fillStyle(0xffffff, 1);
            g.fillRect(x-9*sc+leanOff, y-49*sc, 7*sc, 4*sc);
            g.fillRect(x+2*sc+leanOff,  y-49*sc, 7*sc, 4*sc);
            g.fillStyle(0x111111, 1);
            g.fillRect(x-7*sc+leanOff, y-48*sc, 4*sc, 2.5*sc);
            g.fillRect(x+3*sc+leanOff,  y-48*sc, 4*sc, 2.5*sc);
            // Bandeau
            g.fillStyle(colMain, 1);
            g.fillRect(x-11*sc+leanOff, y-52*sc, 22*sc, 4*sc);

        } else {
            // ══ DEBOUT / SAUT ══
            const jl = leanOff;

            // ── Trainées de vitesse ──
            if (this.speed > 0.78) {
                const sA = Math.min(0.4, (this.speed-0.78)*1.0);
                for (let i=0;i<4;i++) {
                    const ly = y-64*sc+(i*15*sc);
                    const lLen = (16+i*5)*sc*(this.speed-0.78)*2.2;
                    g.fillStyle(colMain, sA*(0.45+i*0.07));
                    g.fillRect(x-22*sc+jl-lLen, ly, lLen, 2*sc);
                    g.fillRect(x+22*sc+jl,       ly, lLen*0.55, 2*sc);
                }
            }

            // ── Ailes (dans le dos, niveau épaules) ──
            const wFlap = this.jumping
                ? Math.sin(this.elapsed*0.028)*7*sc
                : Math.sin(this.elapsed*0.009)*3*sc;
            const wAnchorY = y-55*sc+bounce;
            const wSpan = 46*sc, wH = 34*sc;
            // Plume principale gauche
            g.fillStyle(colMain, 0.90);
            g.fillTriangle(x+jl-10*sc, wAnchorY, x+jl-wSpan, wAnchorY-wH+wFlap, x+jl-9*sc, wAnchorY+wH*0.5);
            // Plume secondaire
            g.fillStyle(colMain, 0.60);
            g.fillTriangle(x+jl-8*sc, wAnchorY+3*sc, x+jl-wSpan*0.68, wAnchorY-wH*0.45+wFlap*0.7, x+jl-8*sc, wAnchorY+wH*0.72);
            // Reflet clair
            g.fillStyle(colLight, 0.35);
            g.fillTriangle(x+jl-7*sc, wAnchorY+6*sc, x+jl-wSpan*0.38, wAnchorY-wH*0.18+wFlap*0.4, x+jl-7*sc, wAnchorY+wH*0.90);
            // Plume principale droite
            g.fillStyle(colMain, 0.90);
            g.fillTriangle(x+jl+10*sc, wAnchorY, x+jl+wSpan, wAnchorY-wH+wFlap, x+jl+9*sc, wAnchorY+wH*0.5);
            g.fillStyle(colMain, 0.60);
            g.fillTriangle(x+jl+8*sc, wAnchorY+3*sc, x+jl+wSpan*0.68, wAnchorY-wH*0.45+wFlap*0.7, x+jl+8*sc, wAnchorY+wH*0.72);
            g.fillStyle(colLight, 0.35);
            g.fillTriangle(x+jl+7*sc, wAnchorY+6*sc, x+jl+wSpan*0.38, wAnchorY-wH*0.18+wFlap*0.4, x+jl+7*sc, wAnchorY+wH*0.90);

            // ── Jambes ──
            // Cuisse gauche
            g.fillStyle(0x1a1a2e, 1);
            g.fillRect(x-12*sc+jl, y-36*sc+bounce, 10*sc, 20*sc+legSwing);
            // Cuisse droite
            g.fillRect(x+2*sc+jl,  y-36*sc+bounce, 10*sc, 20*sc-legSwing);
            // Tibia gauche (suit la cuisse)
            g.fillStyle(0x252540, 1);
            g.fillRect(x-11*sc+jl, y-16*sc+bounce+legSwing*0.5, 8*sc, 18*sc);
            // Tibia droit
            g.fillRect(x+3*sc+jl,  y-16*sc+bounce-legSwing*0.5, 8*sc, 18*sc);
            // Sandales/bandelettes
            g.fillStyle(colMain, 0.9);
            g.fillRect(x-13*sc+jl, y+1*sc+bounce, 12*sc, 5*sc);
            g.fillRect(x+1*sc+jl,  y+1*sc+bounce, 12*sc, 5*sc);

            // ── Bras arrière (derrière le corps) ──
            g.fillStyle(skinCol, 0.7);
            const backArmX = x + (armSwing > 0 ? 14 : -14)*sc + jl;
            const backArmY = y-52*sc+bounce + (armSwing > 0 ? armSwing*0.6 : -armSwing*0.6);
            g.fillRect(backArmX-3*sc, backArmY, 7*sc, 20*sc);
            // Manche du bras arrière
            g.fillStyle(0x111827, 0.8);
            g.fillRect(backArmX-3*sc, backArmY, 6*sc, 14*sc);

            // ── Torse ──
            g.fillStyle(0x111827, 1);
            g.fillRoundedRect(x-14*sc+jl, y-55*sc+bounce, 28*sc, 22*sc, 3*sc);
            // Motif de tunique (tissu diagonal)
            g.fillStyle(0x1e2840, 0.8);
            g.fillRect(x-14*sc+jl, y-55*sc+bounce, 14*sc, 22*sc); // moitié gauche légèrement plus sombre
            // Ceinture
            g.fillStyle(colMain, 0.88);
            g.fillRect(x-16*sc+jl, y-36*sc+bounce, 32*sc, 4*sc);
            // Obi/nœud de ceinture (côté droit)
            g.fillStyle(colLight, 0.6);
            g.fillRect(x+8*sc+jl, y-38*sc+bounce, 8*sc, 8*sc);
            // Décorations poitrine (kunai croisés)
            g.lineStyle(1.2*sc, colLight, 0.45);
            g.beginPath(); g.moveTo(x-8*sc+jl,y-52*sc+bounce); g.lineTo(x+2*sc+jl,y-40*sc+bounce); g.strokePath();
            g.beginPath(); g.moveTo(x+8*sc+jl,y-52*sc+bounce); g.lineTo(x-2*sc+jl,y-40*sc+bounce); g.strokePath();

            // ── Bras avant ──
            g.fillStyle(skinCol, 1);
            const frontArmX = x + (armSwing > 0 ? -14 : 14)*sc + jl;
            const frontArmY = y-52*sc+bounce + (armSwing > 0 ? -armSwing*0.6 : armSwing*0.6);
            g.fillRect(frontArmX-3*sc, frontArmY, 7*sc, 18*sc);
            // Manche
            g.fillStyle(0x111827, 0.85);
            g.fillRect(frontArmX-3*sc, frontArmY, 6*sc, 12*sc);
            // Poing
            g.fillStyle(skinCol, 1);
            g.fillCircle(frontArmX+0.5*sc, frontArmY+18*sc, 4*sc);

            // ── Cou ──
            g.fillStyle(skinCol, 1);
            g.fillRect(x-4*sc+jl, y-60*sc+bounce, 8*sc, 7*sc);

            // ── Tête (ovale légèrement allongé) ──
            g.fillStyle(0x1a1a2e, 1);
            g.fillEllipse(x+jl, y-72*sc+bounce, 24*sc, 26*sc);
            // Peau visible autour des yeux
            g.fillStyle(skinCol, 1);
            g.fillRect(x-10*sc+jl, y-80*sc+bounce, 20*sc, 10*sc);
            // Masque ninja (bas du visage, couleur perso)
            g.fillStyle(colMain, 0.95);
            g.fillRect(x-11*sc+jl, y-69*sc+bounce, 22*sc, 10*sc);
            // Nez (légère bosse)
            g.fillStyle(skinCol, 0.8);
            g.fillCircle(x+jl, y-68*sc+bounce, 2.5*sc);
            // Yeux (blancs + iris + pupilles)
            g.fillStyle(0xffffff, 1);
            g.fillEllipse(x-6*sc+jl, y-78*sc+bounce, 8*sc, 6*sc);
            g.fillEllipse(x+6*sc+jl, y-78*sc+bounce, 8*sc, 6*sc);
            g.fillStyle(0x2a4a8a, 1);  // iris bleu foncé
            g.fillCircle(x-6*sc+jl, y-78*sc+bounce, 3*sc);
            g.fillCircle(x+6*sc+jl, y-78*sc+bounce, 3*sc);
            g.fillStyle(0x111111, 1);  // pupilles
            g.fillCircle(x-6*sc+jl, y-78*sc+bounce, 1.8*sc);
            g.fillCircle(x+6*sc+jl, y-78*sc+bounce, 1.8*sc);
            g.fillStyle(0xffffff, 0.8);  // reflet
            g.fillCircle(x-5*sc+jl, y-79*sc+bounce, 0.9*sc);
            g.fillCircle(x+7*sc+jl, y-79*sc+bounce, 0.9*sc);
            // Sourcils expresifs
            g.lineStyle(2*sc, 0x1a1a2e, 1);
            g.beginPath(); g.moveTo(x-10*sc+jl,y-82*sc+bounce); g.lineTo(x-2*sc+jl,y-82*sc+bounce); g.strokePath();
            g.beginPath(); g.moveTo(x+2*sc+jl, y-82*sc+bounce); g.lineTo(x+10*sc+jl,y-82*sc+bounce); g.strokePath();
            // Bandeau de tête (couleur perso)
            g.fillStyle(colMain, 1);
            g.fillRect(x-12*sc+jl, y-85*sc+bounce, 24*sc, 5*sc);
            // Symbole sur le bandeau (cercle)
            g.fillStyle(0xffffff, 0.6);
            g.fillCircle(x+jl, y-82.5*sc+bounce, 3*sc);
            // Nœud du bandeau (flotte derrière)
            const kF = Math.sin(this.elapsed*0.011)*2.5*sc;
            g.fillStyle(colMain, 1);
            g.fillRect(x+10*sc+jl, y-88*sc+bounce+kF, 6*sc, 12*sc);
            g.fillRect(x+12*sc+jl, y-88*sc+bounce+kF*1.3, 4*sc, 8*sc);

            // ── Aura vitesse ──
            if (this.speed > 0.90) {
                const sA2 = Math.min(0.25, (this.speed-0.90)*1.0);
                g.fillStyle(colMain, sA2*0.35);
                g.fillEllipse(x+jl, y-38*sc+bounce, 52*sc, 80*sc);
            }
        }
    }

    _drawHUD() {
        const g = this.hudGfx; g.clear();
        // Flash
        if (this.flashAlpha > 0) {
            g.fillStyle(this.flashColor, this.flashAlpha);
            g.fillRect(0, 0, this.W, this.H);
            this.flashAlpha = Math.max(0, this.flashAlpha - 0.038);
        }
        // Indicateur de biome (coin haut gauche, sous le score)
        const biomeLabel = this.biome === 'forest' ? '🌲 Forêt' : (this.biome === 'city' ? '🏙️ Ville' : '🌾 Campagne');
        if (!this._biomeLabelTxt) {
            this._biomeLabelTxt = this.add.text(14, 58, biomeLabel, { font: '11px Inter', color: '#334155', stroke: '#ffffff', strokeThickness: 2 }).setDepth(5);
        } else {
            this._biomeLabelTxt.setText(biomeLabel);
        }

        // Barre de proximité monstre — n'apparaît qu'une fois le monstre réveillé
        if (!this.monsterActive) return;
        const bW = 160, bH = 4, bX = this.W/2-bW/2, bY = 10;
        g.fillStyle(0xffffff, 0.55);
        g.fillRect(bX-1, bY-1, bW+2, bH+2);
        g.fillStyle(0xd8d2c5, 1);
        g.fillRect(bX, bY, bW, bH);
        const fill  = bW * this.monsterDist;
        const bCol  = this.monsterDist > 0.5 ? 0x22c55e : this.monsterDist > 0.28 ? 0xfacc15 : 0xdc2626;
        g.fillStyle(bCol, 0.9);
        g.fillRect(bX, bY, fill, bH);
    }
}; }

// ============================================================
//  COFFRE
// ============================================================
function openChest() {
    document.getElementById('chest-waiting').style.display = 'block';
    document.getElementById('chest-result').style.display  = 'none';
    document.getElementById('chest-emoji').textContent = chestType === 'legendary' ? '🌟' : '📦';
    document.getElementById('chest-title').textContent = chestType === 'legendary' ? 'COFFRE LÉGENDAIRE' : 'COFFRE NORMAL';
    document.getElementById('chest-desc').textContent  = chestType === 'legendary'
        ? 'Quelque chose de rare t\'attend...' : 'Ouvre pour débloquer un runner !';
    document.getElementById('chest-modal').classList.add('show');
}
function openChestRequest() {
    fetch('{{ route("chest.open") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ type: chestType })
    }).then(r => r.json()).then(data => {
        document.getElementById('chest-waiting').style.display = 'none';
        document.getElementById('chest-result').style.display  = 'block';
        if (data.success && data.character) {
            document.getElementById('result-emoji').textContent = data.character.emoji;
            document.getElementById('result-name').textContent  = data.character.name;
            document.getElementById('result-rarity').innerHTML  = `<span class="badge badge-${data.character.rarity}">${data.character.rarity}</span>`;
            document.getElementById('result-msg').textContent   = data.message;
        } else {
            document.getElementById('result-emoji').textContent = '😢';
            document.getElementById('result-name').textContent  = 'Oups...';
            document.getElementById('result-msg').textContent   = data.message || 'Erreur';
        }
    });
}
function closeChest() {
    document.getElementById('chest-modal').classList.remove('show');
    window.location.reload();
}
</script>
@endpush
