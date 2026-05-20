@extends('layouts.app')
@section('title', 'Jouer')

@push('styles')
<style>
/* ===== BASE ===== */
.game-wrapper { display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }

/* ===== PREGAME ===== */
.pregame {
    background: #000;
    border: 1px solid #1c1c1c;
    width: 100%; max-width: 880px;
    overflow: hidden;
    position: relative;
}

.pg-header {
    padding: 2.5rem 2.5rem 2rem;
    background: #050505;
    border-bottom: 1px solid #141414;
    position: relative;
    overflow: hidden;
}
.pg-header::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 80px; height: 3px;
    background: #dc2626;
}

.pg-eyebrow {
    font-size: 0.6rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 5px;
    color: #dc2626; margin-bottom: 0.5rem;
}

@keyframes glitch {
    0%,88%,100% { text-shadow: 3px 0 #dc2626, -3px 0 #3b82f6; transform: none; }
    90% { text-shadow: -3px 0 #dc2626, 3px 0 #3b82f6; transform: translateX(2px); }
    92% { text-shadow: 3px 2px #dc2626, -3px -2px #3b82f6; transform: translateX(-2px); clip-path: polygon(0 15%, 100% 15%, 100% 55%, 0 55%); }
    94% { text-shadow: none; transform: none; clip-path: none; }
}

.pg-title {
    font-family: 'Bangers', cursive;
    font-size: 4.5rem; line-height: 1;
    color: #fff; letter-spacing: 10px;
    margin: 0; text-transform: uppercase;
    animation: glitch 7s infinite;
}
.pg-sub {
    font-size: 0.72rem; color: #2d2d2d;
    text-transform: uppercase; letter-spacing: 3px;
    margin-top: 0.6rem; font-weight: 700;
}

/* sections */
.pg-block {
    padding: 1.5rem 2.5rem;
    border-bottom: 1px solid #0f0f0f;
}
.pg-block-inline {
    padding: 1.2rem 2.5rem;
    border-bottom: 1px solid #0f0f0f;
    display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;
}
.pg-label {
    font-size: 0.58rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 4px;
    color: #2d2d2d; margin-bottom: 1rem;
}
.pg-block-inline .pg-label { margin-bottom: 0; white-space: nowrap; }

/* Characters */
.char-grid { display: flex; gap: 0.6rem; flex-wrap: wrap; }
.char-card {
    width: 76px; cursor: pointer;
    border: 1px solid #141414;
    padding: 0.7rem 0.5rem;
    text-align: center;
    transition: all 0.12s;
    background: #060606;
    position: relative;
}
.char-card::after {
    content: ''; position: absolute;
    bottom: 0; left: 0; right: 0; height: 2px;
    background: transparent; transition: background 0.12s;
}
.char-card:hover { background: #0d0d0d; }
.char-card:hover::after { background: #facc15; }
.char-card.selected { border-color: #222; background: #0d0d0d; }
.char-card.selected::after { background: #facc15; }
.char-emoji { font-size: 1.9rem; line-height: 1.1; }
.char-name { font-size: 0.58rem; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; color: #333; margin-top: 0.3rem; }

/* Mode cards */
.mode-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; }
.mode-card {
    padding: 1.4rem 1.5rem;
    border: 1px solid #141414;
    cursor: pointer; transition: all 0.12s;
    background: #060606;
    position: relative; overflow: hidden;
}
.mode-card::before {
    content: ''; position: absolute;
    inset: 0; opacity: 0; transition: opacity 0.15s;
}
.mode-card.active-normal { border-color: #1d4ed8; }
.mode-card.active-normal::before { background: rgba(29,78,216,0.07); opacity: 1; }
.mode-card.active-hard   { border-color: #dc2626; }
.mode-card.active-hard::before   { background: rgba(220,38,38,0.07); opacity: 1; }
.mode-tag {
    font-size: 0.55rem; font-weight: 800;
    letter-spacing: 4px; text-transform: uppercase;
    color: #1d4ed8; margin-bottom: 0.3rem;
}
.mode-tag-red { color: #dc2626; }
.mode-title {
    font-family: 'Bangers', cursive;
    font-size: 2rem; letter-spacing: 5px;
    color: #e5e7eb; line-height: 1;
}
.mode-sub {
    font-size: 0.68rem; color: #333;
    text-transform: uppercase; letter-spacing: 1px;
    margin-top: 0.5rem;
}

/* Keys */
.keys-row { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
.key-btn {
    padding: 0.35rem 0.9rem;
    border: 1px solid #1a1a1a;
    background: #060606; color: #444;
    cursor: pointer; font-weight: 800;
    font-size: 0.7rem; text-transform: uppercase;
    letter-spacing: 1.5px; transition: all 0.12s;
    font-family: 'Inter', sans-serif;
}
.key-btn.active { border-color: #facc15; color: #facc15; background: rgba(250,204,21,0.05); }
.keys-hint { font-size: 0.68rem; color: #2a2a2a; text-transform: uppercase; letter-spacing: 2px; }

/* Start button */
@keyframes pulse-glow {
    0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.5), inset 0 0 0 0 rgba(220,38,38,0); }
    50%      { box-shadow: 0 0 20px 4px rgba(220,38,38,0.2), inset 0 1px 0 0 rgba(255,100,100,0.1); }
}
#start-btn {
    display: flex; align-items: center; justify-content: center; gap: 0.8rem;
    width: 100%; padding: 1.3rem;
    font-size: 1.5rem; border: none;
    background: #dc2626;
    color: #fff; font-weight: 800;
    cursor: pointer; font-family: 'Bangers', cursive;
    letter-spacing: 6px; text-transform: uppercase;
    transition: all 0.15s;
    animation: pulse-glow 2.5s infinite;
    position: relative; overflow: hidden;
}
#start-btn::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.06) 50%, transparent 100%);
    transform: translateX(-100%);
    transition: transform 0.5s;
}
#start-btn:hover::before { transform: translateX(100%); }
#start-btn:hover { background: #b91c1c; letter-spacing: 8px; }

/* Canvas */
#game-container { display: none; flex-direction: column; align-items: center; width: 100%; max-width: 880px; }
#game-canvas-wrapper { overflow: hidden; border: 1px solid #141414; width: 100%; }
#game-canvas-wrapper canvas { display: block; width: 100% !important; }

/* Game over */
@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
#gameover-panel {
    display: none;
    background: #000; border: 1px solid #1c1c1c;
    width: 100%; max-width: 880px;
    overflow: hidden; position: relative;
    animation: fadeSlideIn 0.3s ease;
}
#gameover-panel::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, #dc2626 0%, #dc262600 100%);
}
.go-inner { padding: 3rem 3rem 0; }
.go-eyebrow {
    font-size: 0.6rem; font-weight: 800;
    letter-spacing: 5px; text-transform: uppercase;
    color: #dc2626; margin-bottom: 0.5rem;
}
.go-title {
    font-family: 'Bangers', cursive;
    font-size: 5.5rem; letter-spacing: 6px;
    color: #fff; line-height: 1; margin: 0;
}
.go-stats {
    display: flex; align-items: stretch;
    border-top: 1px solid #111; border-bottom: 1px solid #111;
    margin-top: 2.5rem;
}
.go-stat {
    flex: 1; padding: 1.5rem 2rem;
    border-right: 1px solid #111;
}
.go-stat:last-child { border-right: none; }
.go-stat-val {
    font-family: 'Bangers', cursive;
    font-size: 2.8rem; letter-spacing: 3px;
    color: #facc15; line-height: 1;
}
.go-stat-key {
    font-size: 0.55rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 4px;
    color: #2d2d2d; margin-top: 0.4rem;
}
.go-actions { display: flex; gap: 0.6rem; padding: 1.5rem 2rem; flex-wrap: wrap; }

/* Chest modal */
#chest-modal {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.95);
    z-index: 1000; align-items: center; justify-content: center;
}
#chest-modal.show { display: flex; }
@keyframes popIn {
    from { transform: scale(0.9) translateY(8px); opacity: 0; }
    to   { transform: scale(1) translateY(0); opacity: 1; }
}
.chest-box {
    background: #000; border: 1px solid #1c1c1c;
    border-top: 2px solid #facc15;
    padding: 2.5rem; text-align: center;
    max-width: 380px; width: 90%;
    animation: popIn 0.2s ease;
}
.chest-tag {
    font-size: 0.58rem; font-weight: 800;
    letter-spacing: 5px; text-transform: uppercase;
    color: #facc15; margin-bottom: 1.2rem;
}
.chest-icon { font-size: 3.5rem; margin-bottom: 0.8rem; }
.chest-title {
    font-family: 'Bangers', cursive;
    font-size: 1.8rem; letter-spacing: 5px;
    color: #fff; margin-bottom: 0.4rem;
}
.chest-desc {
    font-size: 0.72rem; color: #333;
    text-transform: uppercase; letter-spacing: 2px;
    margin-bottom: 2rem;
}
.chest-result { display: none; }
.result-icon { font-size: 3.5rem; margin-bottom: 0.8rem; }
.result-name {
    font-family: 'Bangers', cursive;
    font-size: 1.8rem; letter-spacing: 4px;
    color: #fff; margin-bottom: 0.5rem;
}
.result-rarity { margin-bottom: 0.75rem; }
.result-msg {
    font-size: 0.7rem; color: #333;
    text-transform: uppercase; letter-spacing: 2px;
    margin-bottom: 1.5rem;
}
.btn-full { width: 100%; justify-content: center; padding: 0.9rem 1rem; font-size: 0.95rem; }
</style>
@endpush

@section('content')
<div class="game-wrapper">

    <!-- PRÉ-JEU -->
    <div class="pregame" id="pregame-panel">

        <div class="pg-header">
            <div class="pg-eyebrow">City Chase System — v2.0</div>
            <h1 class="pg-title">STREET RUN</h1>
            <p class="pg-sub">Fuis. Esquive. Survis.</p>
        </div>

        <div class="pg-block">
            <div class="pg-label">01 — Runner</div>
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

        <div class="pg-block">
            <div class="pg-label">02 — Mode</div>
            <div class="mode-grid">
                <div class="mode-card active-normal" data-diff="normal" onclick="selectDiff(this)">
                    <div class="mode-tag">Normal</div>
                    <div class="mode-title">STREET</div>
                    <div class="mode-sub">Coffre standard après la run</div>
                </div>
                <div class="mode-card" data-diff="hard" onclick="selectDiff(this)">
                    <div class="mode-tag mode-tag-red">Hard</div>
                    <div class="mode-title">BRUTAL</div>
                    <div class="mode-sub">Coffre Légendaire si tu survis</div>
                </div>
            </div>
        </div>

        <div class="pg-block-inline">
            <div class="pg-label">03 — Touches</div>
            <div class="keys-row">
                <button class="key-btn active" data-keys="arrows" onclick="selectKeys(this)">↑ ← → Flèches</button>
                <button class="key-btn" data-keys="zqsd" onclick="selectKeys(this)">Z Q D</button>
                <span class="keys-hint" id="keys-hint">↑ Sauter &nbsp;·&nbsp; ← → Changer de voie</span>
            </div>
        </div>

        <button id="start-btn" onclick="startGame()">
            <span>LANCER LA RUN</span>
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:1em;height:1em;flex-shrink:0"><path d="M5 3l14 9-14 9V3z"/></svg>
        </button>
    </div>

    <!-- ZONE JEU -->
    <div id="game-container">
        <div id="game-canvas-wrapper"></div>
    </div>

    <!-- GAME OVER -->
    <div id="gameover-panel">
        <div class="go-inner">
            <div class="go-eyebrow">Game Over</div>
            <h2 class="go-title">RATTRAPÉ.</h2>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/phaser/3.60.0/phaser.min.js"></script>
<script>
let selectedCharId = {{ $userCharacters->first()?->id ?? 'null' }};
let selectedDiff   = 'normal';
let selectedKeys   = 'arrows';
let phaserGame     = null;
let lastScore = 0, lastCoins = 0, lastDuration = 0, chestType = 'normal';

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
        ? 'Z Sauter · Q D Changer de voie'
        : '↑ Sauter · ← → Changer de voie';
}

function _buildGame() {
    return new Phaser.Game({
        type: Phaser.AUTO, width: 880, height: 420,
        parent: 'game-canvas-wrapper',
        backgroundColor: '#000000',
        physics: { default: 'arcade', arcade: { debug: false } },
        scene: StreetScene
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
// ============================================================
class StreetScene extends Phaser.Scene {
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
        this.monsterDist        = 0.88;
        this.monsterX           = W / 2;
        this.hitCooldown        = 0;
        this.spawnTimer         = 0;

        // Countdown
        this.countdown       = 3;
        this.countdownActive = true;
        this.countTxt = this.add.text(W/2, H/2, '3', {
            font: 'bold 110px Bangers', color: '#facc15',
            stroke: '#000', strokeThickness: 8
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

        // HUD texts
        this.scoreTxt = this.add.text(14, 12, 'SCORE: 0',
            { font: 'bold 20px Bangers', color: '#facc15', letterSpacing: 2 }).setDepth(5);
        this.coinTxt  = this.add.text(14, 38, '🪙 0',
            { font: '14px Inter', color: '#6b7280' }).setDepth(5);
        this.diffTxt  = this.add.text(W-14, 12,
            selectedDiff === 'hard' ? 'HARD' : 'NORMAL',
            { font: 'bold 14px Bangers', color: selectedDiff==='hard' ? '#dc2626' : '#3b82f6', letterSpacing: 3 })
            .setOrigin(1,0).setDepth(5);
        this.hintTxt  = this.add.text(W/2, H-11,
            selectedKeys === 'zqsd' ? 'Z=Sauter · Q/D=Voie' : '↑=Sauter · ←/→=Voie',
            { font: '10px Inter', color: '#1a1a1a' }).setOrigin(0.5).setDepth(5);

        // Touches
        const kb = this.input.keyboard;
        this.keys = {
            left:  kb.addKey('LEFT'),  right: kb.addKey('RIGHT'),
            up:    kb.addKey('UP'),    space: kb.addKey('SPACE'),
            q: kb.addKey('Q'), d: kb.addKey('D'), z: kb.addKey('Z'),
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
        if (next >= 0 && next <= 2) { this.lane = next; this.laneChangeCooldown = 150; }
    }

    flashScreen(col, alpha) { this.flashColor = col; this.flashAlpha = alpha; }

    update(time, delta) {
        if (this.gameOver) return;
        if (this.countdownActive) {
            this.drawBg(); this.drawPath(); this.drawMonster(1); this.drawPlayer(0); return;
        }

        this.elapsed += delta;
        this.score    = Math.floor(this.elapsed / 80 + this.coins * 5);
        this.scoreTxt.setText('SCORE: ' + this.score);

        if (this.laneChangeCooldown > 0) this.laneChangeCooldown -= delta;
        if (this.hitCooldown > 0)        this.hitCooldown -= delta;

        this.speed = (selectedDiff === 'hard' ? 0.68 : 0.48) + (this.elapsed / 1000) * 0.007;

        // Saut
        const jumpKey = this.keys.up.isDown || this.keys.space.isDown ||
                        (selectedKeys === 'zqsd' && this.keys.z.isDown);
        if (jumpKey && !this.jumping) { this.jumping = true; this.jumpT = 0; }
        if (this.jumping) {
            this.jumpT += delta / 520;
            if (this.jumpT >= 1) { this.jumping = false; this.jumpT = 0; }
        }
        const jumpOff = this.jumping ? Math.sin(this.jumpT * Math.PI) : 0;

        // Avancer objets
        const dt = delta / 1000;
        for (const o of this.objects) o.depth += this.speed * dt;

        // Collisions
        for (const o of this.objects) {
            if (o.depth < 0.90 || o.depth > 1.01) continue;
            if (o.type === 'coin' && o.lane === this.lane) {
                o.dead = true; this.coins++;
                this.coinTxt.setText('🪙 ' + this.coins);
                this.flashScreen(0xfacc15, 0.10);
            }
            if (o.type === 'jump' && o.lane === this.lane) {
                if (this.jumping) { o.dead = true; }
                else if (this.hitCooldown <= 0) {
                    o.dead = true;
                    this._hit();
                    if (this.monsterDist <= 0) { this.triggerGameOver(); return; }
                }
            }
            if (o.type === 'lane' && o.lane === this.lane && this.hitCooldown <= 0) {
                o.dead = true;
                this._hit();
                if (this.monsterDist <= 0) { this.triggerGameOver(); return; }
            }
        }

        this.objects = this.objects.filter(o => o.depth <= 1.06 && !o.dead);

        // Spawn
        this.spawnTimer += delta;
        const delay = Math.max(550, 1350 - this.elapsed * 0.035);
        if (this.spawnTimer >= delay) { this.spawnTimer = 0; this.spawnObject(); }

        // Monstre distance
        this.monsterDist = Math.min(1.0, this.monsterDist + delta * 0.000042);
        this.monsterDist = Math.max(0,   this.monsterDist - delta * 0.000004);
        if (this.monsterDist <= 0) { this.triggerGameOver(); return; }
        if (this.monsterDist < 0.28)
            this.flashScreen(0xef4444, 0.05 + (0.28 - this.monsterDist) * 0.18);

        // Draw
        this.drawBg();
        this.drawPath();
        this.drawMonster(this.monsterDist);
        this.drawObjects(jumpOff);
        this.drawPlayer(jumpOff);
        this._drawHUD();
    }

    _hit() {
        this.monsterDist -= 0.26;
        this.hitCooldown  = 850;
        this.flashScreen(0xef4444, 0.45);
        this.cameras.main.shake(200, 0.011);
    }

    spawnObject() {
        const r = Math.random();
        if (r < 0.40) {
            const base = Phaser.Math.Between(0,2);
            const cnt  = Phaser.Math.Between(1,3);
            for (let i = 0; i < cnt; i++)
                this.objects.push({ type:'coin', lane: Math.min(2, base+i), depth: 0.01 + i*0.045 });
        } else if (r < 0.70) {
            this.objects.push({ type:'jump', lane: Phaser.Math.Between(0,2), depth: 0.01 });
        } else {
            const free    = Phaser.Math.Between(0,2);
            const blocked = [0,1,2].filter(l => l !== free);
            const cnt     = Math.random() < 0.45 ? 1 : 2;
            blocked.slice(0, cnt).forEach(l =>
                this.objects.push({ type:'lane', lane: l, depth: 0.01 }));
        }
    }

    triggerGameOver() {
        if (this.gameOver) return;
        this.gameOver = true;
        lastScore = this.score; lastCoins = this.coins;
        lastDuration = Math.floor(this.elapsed / 1000);
        this.flashScreen(0xef4444, 0.75);
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
        const g = this.bgGfx; g.clear();
        const fy = this.vp.y;

        // Ciel nuit profond
        g.fillGradientStyle(0x000000, 0x000000, 0x0a0a12, 0x0a0a12, 1);
        g.fillRect(0, 0, this.W, fy + 25);

        // Sol latéral
        g.fillStyle(0x0d0d0d, 1);
        g.fillRect(0, fy + 25, this.W, this.H - fy - 25);

        // Bâtiments
        if (!this._bld) this._bld = [
            { x:8,   w:52, h:105, wins:[[12,18],[12,40],[12,62],[32,18],[32,40]] },
            { x:66,  w:33, h:72,  wins:[[8,14],[8,34],[20,14],[20,34]] },
            { x:106, w:58, h:128, wins:[[10,18],[10,44],[10,70],[30,18],[30,44],[30,70],[46,18],[46,44]] },
            { x:178, w:28, h:55,  wins:[[6,12],[6,32],[16,12]] },
            { x:this.W-72,  w:48, h:98,  wins:[[8,18],[8,44],[28,18],[28,44]] },
            { x:this.W-128, w:38, h:78,  wins:[[7,12],[7,32],[22,12],[22,32]] },
            { x:this.W-190, w:54, h:118, wins:[[8,18],[8,48],[8,78],[28,18],[28,48],[28,78],[44,18],[44,48]] },
            { x:this.W-252, w:33, h:62,  wins:[[7,12],[7,32],[20,12]] },
        ];
        this._bld.forEach(b => {
            const by = fy - b.h + 22;
            g.fillStyle(0x111827, 1);
            g.fillRect(b.x, by, b.w, b.h);
            // Contour subtil
            g.lineStyle(1, 0x1f2937, 1);
            g.strokeRect(b.x, by, b.w, b.h);
            b.wins.forEach(([wx,wy], i) => {
                if (i % 3 === 1) return;
                g.fillStyle(i%2===0 ? 0xfacc15 : 0x3b82f6, 0.55 + Math.sin(this.elapsed*0.0008 + i)*0.2);
                g.fillRect(b.x+wx, by+wy, 7, 5);
            });
        });

        // Lampadaires
        [this.pathNear.left-15, this.pathNear.right+15].forEach(lx => {
            const dir = lx < this.W/2 ? 1 : -1;
            g.fillStyle(0x1f2937, 1);
            g.fillRect(lx-2, fy-25, 5, 55);
            g.fillRect(lx, fy-23, dir*18, 3);
            g.fillStyle(0xfacc15, 0.12);
            g.fillCircle(lx + dir*18, fy-22, 16);
            g.fillStyle(0xfacc15, 0.7);
            g.fillCircle(lx + dir*18, fy-22, 4);
        });

        // Graffiti
        if (!this._graf) this._graf = [
            {x:22, y:fy-14, w:38, h:16, c:0xdc2626},
            {x:68, y:fy-7,  w:28, h:11, c:0xfacc15},
            {x:this.W-58,  y:fy-11, w:34, h:15, c:0xdc2626},
            {x:this.W-105, y:fy-20, w:26, h:11, c:0x3b82f6},
        ];
        this._graf.forEach(gr => {
            g.fillStyle(gr.c, 0.45);
            g.fillRect(gr.x, gr.y, gr.w, gr.h);
            g.fillStyle(gr.c, 0.2);
            g.fillRect(gr.x+3, gr.y-4, gr.w*0.55, 3);
        });
    }

    drawPath() {
        const g = this.pathGfx; g.clear();
        const { left:fl, right:fr, y:fy } = this.pathFar;
        const { left:nl, right:nr, y:ny } = this.pathNear;

        // Trottoirs
        g.fillStyle(0x111111, 1);
        g.fillPoints([{x:0,y:fy},{x:fl,y:fy},{x:nl,y:ny},{x:0,y:ny}], true);
        g.fillPoints([{x:fr,y:fy},{x:this.W,y:fy},{x:this.W,y:ny},{x:nr,y:ny}], true);

        // Asphalte
        g.fillStyle(0x1c1c1c, 1);
        g.fillPoints([{x:fl,y:fy},{x:fr,y:fy},{x:nr,y:ny},{x:nl,y:ny}], true);

        // Highlight lane actuelle
        {
            const t = 0.93*0.93;
            const lxN = fl + (nl-fl)*t, rxN = fr + (nr-fr)*t;
            const lW = (rxN-lxN)/3;
            const hx = lxN + lW*this.lane;
            const hy = fy + (ny-fy)*t;
            g.fillStyle(0x3b82f6, 0.07);
            g.fillPoints([
                {x: fl + (nl-fl)*0.1 + (fr-fl)*this.lane/3*0.1, y: fy+(ny-fy)*0.1},
                {x: fl + (nl-fl)*0.1 + (fr-fl)*(this.lane+1)/3*0.1, y: fy+(ny-fy)*0.1},
                {x: hx+lW, y: hy}, {x: hx, y: hy}
            ], true);
            // Indicateur bas de voie
            g.fillStyle(0x3b82f6, 0.5);
            g.fillRect(hx+4, ny-3, lW-8, 3);
        }

        // Bordures blanches
        g.lineStyle(2, 0xe5e7eb, 0.7);
        g.beginPath(); g.moveTo(fl,fy); g.lineTo(nl,ny); g.strokePath();
        g.beginPath(); g.moveTo(fr,fy); g.lineTo(nr,ny); g.strokePath();

        // Lignes jaunes pointillées animées
        const dashOff = (this.elapsed * this.speed * 0.00042) % (1/9);
        for (let l = 1; l < 3; l++) {
            const ft = fl + (fr-fl)*l/3, nt = nl + (nr-nl)*l/3;
            for (let d = 0; d < 10; d++) {
                const t0 = ((d/9 + dashOff) % 1);
                const t1 = t0 + 0.038;
                if (t1 > 1 || t0 < 0.02) continue;
                const x0 = ft+(nt-ft)*t0, y0 = fy+(ny-fy)*t0;
                const x1 = ft+(nt-ft)*t1, y1 = fy+(ny-fy)*t1;
                g.lineStyle(0.8 + t0*2.5, 0xfacc15, 0.55 + t0*0.35);
                g.beginPath(); g.moveTo(x0,y0); g.lineTo(x1,y1); g.strokePath();
            }
        }

        // Lignes de vitesse
        const spOff = (this.elapsed * this.speed * 0.00055) % (1/6);
        for (let i = 0; i < 7; i++) {
            const t = ((i/6 + spOff) % 1);
            if (t < 0.04) continue;
            const lx = fl+(nl-fl)*t, rx = fr+(nr-fr)*t, y = fy+(ny-fy)*t;
            g.lineStyle(0.5+t*1.2, 0xffffff, t*0.12);
            g.beginPath(); g.moveTo(lx,y); g.lineTo(rx,y); g.strokePath();
        }

        // Kerb
        g.fillStyle(0x374151, 1);
        g.fillRect(0, fy-3, nl+1, 3);
        g.fillRect(nr-1, fy-3, this.W-nr+1, 3);
    }

    drawMonster(dist) {
        const g = this.monsterGfx; g.clear();
        const proximity = 1 - dist;

        // Toujours tracker la voie du joueur (smooth)
        const pp = this.screenPos(this.lane, 0.97);
        this.monsterX += (pp.x - this.monsterX) * 0.10;
        const mx = this.monsterX;

        // Position verticale : monte depuis le bas
        const monsterBaseY = this.H + 22 + (1 - proximity) * 125;
        const size = 48 + proximity * 105;

        // Skip si tête toujours sous l'écran
        if (monsterBaseY - size * 1.85 > this.H) return;

        // Aura rouge
        if (dist < 0.38) {
            g.fillStyle(0xef4444, (0.38 - dist) * 0.45);
            g.fillCircle(mx, monsterBaseY - size*0.85, size*1.7);
        }

        // Ombre
        g.fillStyle(0x000000, 0.35);
        g.fillEllipse(mx, monsterBaseY - 3, size*1.9, size*0.28);

        // Corps
        g.fillStyle(0x0f172a, 1);
        g.fillEllipse(mx, monsterBaseY - size*0.55, size*0.88, size*1.05);

        // Tête
        g.fillStyle(0x1e293b, 1);
        g.fillCircle(mx, monsterBaseY - size*1.22, size*0.51);

        // Yeux rouges
        const eyeP = 0.6 + Math.sin(this.elapsed*0.009)*0.4;
        g.fillStyle(0xef4444, eyeP);
        g.fillCircle(mx - size*0.17, monsterBaseY - size*1.27, size*0.115);
        g.fillCircle(mx + size*0.17, monsterBaseY - size*1.27, size*0.115);
        g.fillStyle(0xfca5a5, 1);
        g.fillCircle(mx - size*0.17, monsterBaseY - size*1.27, size*0.048);
        g.fillCircle(mx + size*0.17, monsterBaseY - size*1.27, size*0.048);

        // Cornes
        g.fillStyle(0x334155, 1);
        g.fillTriangle(mx-size*0.27, monsterBaseY-size*1.47, mx-size*0.13, monsterBaseY-size*1.74, mx-size*0.03, monsterBaseY-size*1.47);
        g.fillTriangle(mx+size*0.27, monsterBaseY-size*1.47, mx+size*0.13, monsterBaseY-size*1.74, mx+size*0.03, monsterBaseY-size*1.47);

        // Bras animés
        const sw = Math.sin(this.elapsed * 0.013) * 11;
        g.fillStyle(0x0f172a, 1);
        g.fillRoundedRect(mx-size*0.82, monsterBaseY-size*0.78+sw, size*0.32, size*0.52, size*0.08);
        g.fillRoundedRect(mx+size*0.50, monsterBaseY-size*0.78-sw, size*0.32, size*0.52, size*0.08);

        // Dents
        g.fillStyle(0xe2e8f0, 0.8);
        for (let i = 0; i < 4; i++)
            g.fillTriangle(
                mx-size*0.18+i*size*0.12, monsterBaseY-size*1.07,
                mx-size*0.12+i*size*0.12, monsterBaseY-size*0.93,
                mx-size*0.06+i*size*0.12, monsterBaseY-size*1.07
            );

        // Lignes de vitesse
        if (proximity > 0.32) {
            const la = (proximity-0.32)*0.55;
            g.lineStyle(2, 0xdc2626, la);
            for (let i = 0; i < 4; i++) {
                const ly = monsterBaseY - size*(0.18+i*0.2);
                g.beginPath(); g.moveTo(mx-size*(0.95+i*0.2), ly); g.lineTo(mx-size*0.52, ly); g.strokePath();
                g.beginPath(); g.moveTo(mx+size*0.52, ly); g.lineTo(mx+size*(0.95+i*0.2), ly); g.strokePath();
            }
        }
    }

    drawObjects(jumpOff) {
        const g = this.objGfx; g.clear();
        const sorted = [...this.objects].sort((a,b) => a.depth-b.depth);

        for (const o of sorted) {
            if (o.depth <= 0 || o.depth > 1.02) continue;
            const pos = this.screenPos(o.lane, o.depth);
            const { x, y, scale: sc } = pos;
            const t2 = o.depth * o.depth;
            const lx2 = this.pathFar.left  + (this.pathNear.left  - this.pathFar.left)  * t2;
            const rx2 = this.pathFar.right + (this.pathNear.right - this.pathFar.right) * t2;
            const lW  = (rx2 - lx2) / 3;

            if (o.type === 'coin') {
                const r = 9*sc;
                g.fillStyle(0xfacc15, 1);
                g.fillCircle(x, y, r);
                g.fillStyle(0xfef9c3, 0.8);
                g.fillCircle(x-r*0.28, y-r*0.28, r*0.4);
                g.lineStyle(1.5*sc, 0xeab308, 1);
                g.strokeCircle(x, y, r);

            } else if (o.type === 'jump') {
                // Zèbre jaune/noir au sol
                const ox = lx2 + lW*o.lane, ow = lW*0.9, oh = 14*sc, oy = y-oh*0.5;
                g.fillStyle(0x111111, 1);
                g.fillRect(ox, oy, ow, oh);
                const ns = 5, sw2 = ow/ns;
                for (let s = 0; s < ns; s += 2) {
                    g.fillStyle(0xfacc15, 1);
                    g.fillRect(ox+s*sw2, oy, sw2, oh);
                }
                g.lineStyle(1.5*sc, 0xdc2626, 1);
                g.strokeRect(ox, oy, ow, oh);
                // Flèche montante quand proche
                if (o.depth > 0.42) {
                    const a = Math.min(1, (o.depth-0.42)*2.5);
                    const pulse = Math.abs(Math.sin(this.elapsed*0.015)) * 0.4 + 0.6;
                    g.fillStyle(0xfacc15, a*pulse);
                    g.fillTriangle(x, oy-20*sc, x-11*sc, oy-7*sc, x+11*sc, oy-7*sc);
                    g.fillStyle(0xfacc15, a*pulse*0.5);
                    g.fillTriangle(x, oy-32*sc, x-8*sc, oy-20*sc, x+8*sc, oy-20*sc);
                }

            } else if (o.type === 'lane') {
                // Barrière rouge verticale
                const bx = lx2 + lW*o.lane + lW*0.06;
                const bw = lW*0.88, bh = 72*sc, by = y-bh;
                g.fillStyle(0xdc2626, 0.93);
                g.fillRect(bx, by, bw, bh);
                g.fillStyle(0xfca5a5, 0.35);
                g.fillRect(bx+3, by+3, bw-6, bh*0.22);
                // Bande réfléchissante blanche
                g.fillStyle(0xf9fafb, 0.92);
                g.fillRect(bx, by+bh*0.40, bw, bh*0.15);
                // Bande jaune
                g.fillStyle(0xfacc15, 0.85);
                g.fillRect(bx, by+bh*0.65, bw, bh*0.10);
                g.lineStyle(1.5*sc, 0x7f1d1d, 1);
                g.strokeRect(bx, by, bw, bh);
                // Base
                g.fillStyle(0x1f2937, 1);
                g.fillRect(bx+bw*0.28, y-5*sc, bw*0.44, 8*sc);
            }
        }
    }

    drawPlayer(jumpOff) {
        const g = this.playerGfx; g.clear();
        const pos = this.screenPos(this.lane, 0.96, jumpOff);
        const sc  = pos.scale * 0.88;
        const x = pos.x, y = pos.y;
        const bounce = this.jumping ? 0 : Math.sin(this.elapsed*0.014)*2;

        // Jambes
        const ls = Math.sin(this.elapsed*0.019)*9*sc;
        g.fillStyle(0x1e3a5f, 1);
        g.fillRect(x-10*sc, y-13*sc+bounce, 10*sc, 17*sc+ls);
        g.fillRect(x+2*sc,  y-13*sc+bounce, 10*sc, 17*sc-ls);
        // Chaussures jaunes
        g.fillStyle(0xfacc15, 1);
        g.fillRect(x-12*sc, y+3*sc+bounce, 12*sc, 5*sc);
        g.fillRect(x+2*sc,  y+3*sc+bounce, 13*sc, 5*sc);
        // Hoodie bleu
        g.fillStyle(0x1d4ed8, 1);
        g.fillRoundedRect(x-15*sc, y-41*sc+bounce, 30*sc, 30*sc, 4*sc);
        g.fillStyle(0x1e40af, 0.65);
        g.fillRect(x-6*sc, y-27*sc+bounce, 12*sc, 9*sc);
        // Tête
        g.fillStyle(0xe2c9a8, 1);
        g.fillCircle(x, y-51*sc+bounce, 12*sc);
        // Casquette rouge
        g.fillStyle(0xdc2626, 1);
        g.fillRect(x-13*sc, y-61*sc+bounce, 26*sc, 8*sc);
        g.fillRect(x-15*sc, y-55*sc+bounce, 9*sc, 4*sc);
        // Yeux
        g.fillStyle(0x111111, 1);
        g.fillCircle(x-4*sc, y-52*sc+bounce, 1.8*sc);
        g.fillCircle(x+4*sc, y-52*sc+bounce, 1.8*sc);
        // Ombre
        g.fillStyle(0x000000, 0.2);
        g.fillEllipse(x, this.pathNear.y-7, 36*sc, 8*sc);
    }

    _drawHUD() {
        const g = this.hudGfx; g.clear();
        // Flash
        if (this.flashAlpha > 0) {
            g.fillStyle(this.flashColor, this.flashAlpha);
            g.fillRect(0, 0, this.W, this.H);
            this.flashAlpha = Math.max(0, this.flashAlpha - 0.038);
        }
        // Barre de proximité monstre (en haut au centre)
        const bW = 160, bH = 3, bX = this.W/2-bW/2, bY = 10;
        g.fillStyle(0x111111, 1);
        g.fillRect(bX, bY, bW, bH);
        const fill  = bW * this.monsterDist;
        const bCol  = this.monsterDist > 0.5 ? 0x22c55e : this.monsterDist > 0.28 ? 0xfacc15 : 0xdc2626;
        g.fillStyle(bCol, 0.9);
        g.fillRect(bX, bY, fill, bH);
    }
}

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
