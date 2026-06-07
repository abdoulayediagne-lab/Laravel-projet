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
        this.hintTxt  = this.add.text(W/2, H-11,
            selectedKeys === 'zqsd' ? 'Z=Sauter · Q/D=Voie' : '↑=Sauter · ←/→=Voie',
            { font: '10px Inter', color: '#5b5448' }).setOrigin(0.5).setDepth(5);

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
            this.drawBg(); this.drawPath(); this.drawMonster(); this.drawPlayer(0); return;
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
        const g = this.bgGfx; g.clear();
        const fy = this.vp.y;

        // Ciel clair de matin (dégradé doux, pas de noir)
        g.fillGradientStyle(0x9fd3ee, 0x9fd3ee, 0xeaf6fb, 0xeaf6fb, 1);
        g.fillRect(0, 0, this.W, fy + 25);

        // Soleil doux
        const sunX = this.W * 0.80, sunY = fy * 0.32;
        g.fillStyle(0xfff3c4, 0.45);
        g.fillCircle(sunX, sunY, 42);
        g.fillStyle(0xfffaeb, 0.95);
        g.fillCircle(sunX, sunY, 22);

        // Nuages
        if (!this._clouds) this._clouds = [
            { x: this.W*0.16, y: fy*0.32, s: 1.00 },
            { x: this.W*0.46, y: fy*0.58, s: 0.70 },
            { x: this.W*0.66, y: fy*0.24, s: 0.85 },
        ];
        this._clouds.forEach(c => {
            g.fillStyle(0xffffff, 0.65);
            g.fillEllipse(c.x,        c.y,        72*c.s, 22*c.s);
            g.fillEllipse(c.x+28*c.s, c.y-9*c.s,  46*c.s, 20*c.s);
            g.fillEllipse(c.x-26*c.s, c.y-5*c.s,  42*c.s, 17*c.s);
        });

        // Sol latéral clair
        g.fillStyle(0xe7e1d6, 1);
        g.fillRect(0, fy + 25, this.W, this.H - fy - 25);

        // Bâtiments pastel
        if (!this._bld) this._bld = [
            { x:8,   w:52, h:105, c:0xc7d4e3, wins:[[12,18],[12,40],[12,62],[32,18],[32,40]] },
            { x:66,  w:33, h:72,  c:0xe9ddc9, wins:[[8,14],[8,34],[20,14],[20,34]] },
            { x:106, w:58, h:128, c:0xd6e3d2, wins:[[10,18],[10,44],[10,70],[30,18],[30,44],[30,70],[46,18],[46,44]] },
            { x:178, w:28, h:55,  c:0xc7d4e3, wins:[[6,12],[6,32],[16,12]] },
            { x:this.W-72,  w:48, h:98,  c:0xe9ddc9, wins:[[8,18],[8,44],[28,18],[28,44]] },
            { x:this.W-128, w:38, h:78,  c:0xd6e3d2, wins:[[7,12],[7,32],[22,12],[22,32]] },
            { x:this.W-190, w:54, h:118, c:0xc7d4e3, wins:[[8,18],[8,48],[8,78],[28,18],[28,48],[28,78],[44,18],[44,48]] },
            { x:this.W-252, w:33, h:62,  c:0xe9ddc9, wins:[[7,12],[7,32],[20,12]] },
        ];
        this._bld.forEach(b => {
            const by = fy - b.h + 22;
            g.fillStyle(b.c, 1);
            g.fillRect(b.x, by, b.w, b.h);
            // Contour clair
            g.lineStyle(1, 0xffffff, 0.55);
            g.strokeRect(b.x, by, b.w, b.h);
            // Fenêtres calmes (lueur fixe, pas de scintillement agressif)
            b.wins.forEach(([wx,wy], i) => {
                if (i % 3 === 1) return;
                g.fillStyle(0xfff3d6, 0.8);
                g.fillRect(b.x+wx, by+wy, 7, 5);
            });
        });

        // Lampadaires discrets
        [this.pathNear.left-15, this.pathNear.right+15].forEach(lx => {
            const dir = lx < this.W/2 ? 1 : -1;
            g.fillStyle(0x9aa6b2, 1);
            g.fillRect(lx-2, fy-25, 4, 50);
            g.fillRect(lx, fy-24, dir*14, 2);
            g.fillStyle(0xfff3c4, 0.9);
            g.fillCircle(lx + dir*14, fy-23, 3.5);
        });

        // Arbres (remplacent les graffitis, plus agréables à l'œil)
        if (!this._trees) this._trees = [
            { x: 26, s: 1.00 }, { x: this.W-34, s: 0.92 },
            { x: this.W*0.30, s: 0.62 }, { x: this.W*0.70, s: 0.68 },
        ];
        this._trees.forEach(t => {
            const ty = fy + 4;
            g.fillStyle(0x9c7a52, 1);
            g.fillRect(t.x-3*t.s, ty-15*t.s, 6*t.s, 17*t.s);
            g.fillStyle(0x9bcf8a, 0.95);
            g.fillCircle(t.x, ty-23*t.s, 14*t.s);
            g.fillStyle(0xc3e8b4, 0.85);
            g.fillCircle(t.x-6*t.s, ty-27*t.s, 8*t.s);
        });
    }

    drawPath() {
        const g = this.pathGfx; g.clear();
        const { left:fl, right:fr, y:fy } = this.pathFar;
        const { left:nl, right:nr, y:ny } = this.pathNear;

        // Trottoirs clairs
        g.fillStyle(0xd9d2c5, 1);
        g.fillPoints([{x:0,y:fy},{x:fl,y:fy},{x:nl,y:ny},{x:0,y:ny}], true);
        g.fillPoints([{x:fr,y:fy},{x:this.W,y:fy},{x:this.W,y:ny},{x:nr,y:ny}], true);

        // Asphalte gris doux (au lieu d'un noir agressif)
        g.fillStyle(0xb6b0a6, 1);
        g.fillPoints([{x:fl,y:fy},{x:fr,y:fy},{x:nr,y:ny},{x:nl,y:ny}], true);

        // Bordures blanches nettes
        g.lineStyle(2, 0xffffff, 0.8);
        g.beginPath(); g.moveTo(fl,fy); g.lineTo(nl,ny); g.strokePath();
        g.beginPath(); g.moveTo(fr,fy); g.lineTo(nr,ny); g.strokePath();

        // Lignes blanches pointillées animées (plus douces que le jaune vif)
        const dashOff = (this.elapsed * this.speed * 0.00042) % (1/9);
        for (let l = 1; l < 3; l++) {
            const ft = fl + (fr-fl)*l/3, nt = nl + (nr-nl)*l/3;
            for (let d = 0; d < 10; d++) {
                const t0 = ((d/9 + dashOff) % 1);
                const t1 = t0 + 0.038;
                if (t1 > 1 || t0 < 0.02) continue;
                const x0 = ft+(nt-ft)*t0, y0 = fy+(ny-fy)*t0;
                const x1 = ft+(nt-ft)*t1, y1 = fy+(ny-fy)*t1;
                g.lineStyle(0.8 + t0*2.5, 0xfdfdfd, 0.5 + t0*0.35);
                g.beginPath(); g.moveTo(x0,y0); g.lineTo(x1,y1); g.strokePath();
            }
        }

        // Kerb
        g.fillStyle(0x9c958a, 1);
        g.fillRect(0, fy-3, nl+1, 3);
        g.fillRect(nr-1, fy-3, this.W-nr+1, 3);
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
                g.fillStyle(0x334155, 1);
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
        // Le joueur est avancé vers le bas de l'écran (plus proche de la
        // caméra) pour bien le distinguer du monstre, qui reste en retrait.
        const pos = this.screenPos(this.lane, 0.99, jumpOff);
        const sc  = pos.scale * 0.92;
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
