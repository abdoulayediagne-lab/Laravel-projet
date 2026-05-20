@extends('layouts.app')
@section('title', 'Jouer')

@push('styles')
<style>
    .game-wrapper { display: flex; flex-direction: column; align-items: center; gap: 1.5rem; }

    .pregame {
        background: #12122a; border: 1px solid #2a2a50;
        border-radius: 16px; padding: 2rem; width: 100%; max-width: 880px;
    }
    .pregame h2 { font-family: 'Bangers', cursive; font-size: 1.8rem; letter-spacing: 2px; margin-bottom: 1.5rem; }
    .section-label { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 0.8rem; }

    .char-grid { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
    .char-card {
        width: 90px; cursor: pointer; border-radius: 12px;
        border: 2px solid #2a2a50; padding: 0.8rem; text-align: center;
        transition: all 0.2s; background: #0a0a14;
    }
    .char-card:hover { border-color: #7c3aed; transform: translateY(-2px); }
    .char-card.selected { border-color: #7c3aed; background: rgba(124,58,237,0.15); }
    .char-card .emoji { font-size: 2.2rem; }
    .char-card .cname { font-size: 0.75rem; font-weight: 700; margin-top: 0.3rem; }

    .diff-btns { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
    .diff-btn {
        flex: 1; padding: 0.9rem; border-radius: 10px; border: 2px solid #2a2a50;
        background: #0a0a14; color: #e2e8f0; font-weight: 700; cursor: pointer;
        font-size: 1rem; transition: all 0.2s; font-family: 'Inter', sans-serif;
    }
    .diff-btn.selected-normal { border-color: #06b6d4; background: rgba(6,182,212,0.1); color: #06b6d4; }
    .diff-btn.selected-hard   { border-color: #ef4444; background: rgba(239,68,68,0.1);  color: #ef4444; }

    .keys-config { display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .key-btn {
        padding: 0.4rem 1rem; border-radius: 6px; border: 1px solid #2a2a50;
        background: #0a0a14; color: #e2e8f0; cursor: pointer; font-weight: 700;
        font-family: 'Inter', sans-serif; transition: all 0.2s;
    }
    .key-btn.active { border-color: #7c3aed; background: rgba(124,58,237,0.2); color: #a78bfa; }

    #start-btn {
        width: 100%; padding: 1.1rem; font-size: 1.3rem; border-radius: 10px; border: none;
        background: linear-gradient(135deg, #7c3aed, #06b6d4); color: white;
        font-weight: 800; cursor: pointer; font-family: 'Bangers', cursive;
        letter-spacing: 2px; transition: all 0.2s;
    }
    #start-btn:hover { opacity: 0.9; transform: translateY(-2px); }

    #game-container { display: none; flex-direction: column; align-items: center; width: 100%; max-width: 880px; }
    #game-canvas-wrapper { border-radius: 12px; overflow: hidden; border: 2px solid #2a2a50; width: 100%; }
    #game-canvas-wrapper canvas { display: block; width: 100% !important; }

    #gameover-panel {
        display: none; background: #12122a; border: 1px solid #2a2a50;
        border-radius: 16px; padding: 2.5rem; text-align: center; width: 100%; max-width: 880px;
    }
    #gameover-panel h2 { font-family: 'Bangers', cursive; font-size: 3.5rem; color: #ef4444; letter-spacing: 3px; }
    .go-score { font-size: 3rem; font-weight: 800; color: #fbbf24; margin: 1rem 0; font-family: 'Bangers', cursive; letter-spacing: 2px; }
    .go-sub { color: #64748b; margin-bottom: 1.5rem; }
    .go-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

    #chest-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 1000; align-items: center; justify-content: center; }
    #chest-modal.show { display: flex; }
    .chest-box { background: #12122a; border: 1px solid #2a2a50; border-radius: 20px; padding: 3rem; text-align: center; max-width: 400px; width: 90%; animation: popIn 0.3s ease; }
    @keyframes popIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .chest-emoji { font-size: 5rem; margin-bottom: 1rem; }
    .chest-title { font-family: 'Bangers', cursive; font-size: 2rem; letter-spacing: 2px; margin-bottom: 0.5rem; }
    .chest-desc { color: #64748b; margin-bottom: 2rem; font-size: 0.95rem; }
    .chest-result { display: none; }
    .result-emoji { font-size: 4rem; margin-bottom: 0.5rem; }
    .result-name { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.3rem; }
    .result-rarity { margin-bottom: 1.5rem; }
</style>
@endpush

@section('content')
<div class="game-wrapper">

    <!-- PRÉ-JEU -->
    <div class="pregame" id="pregame-panel">
        <h2>⚙️ Configure ta Run</h2>

        <div class="section-label">👤 Choisis ton personnage</div>
        <div class="char-grid" id="char-grid">
            @foreach($userCharacters as $char)
            <div class="char-card {{ $loop->first ? 'selected' : '' }}"
                 data-id="{{ $char->id }}" onclick="selectChar(this)">
                <div class="emoji">{{ $char->emoji }}</div>
                <div class="cname">{{ $char->name }}</div>
            </div>
            @endforeach
        </div>

        <div class="section-label">⚡ Difficulté</div>
        <div class="diff-btns">
            <button class="diff-btn selected-normal" data-diff="normal" onclick="selectDiff(this)">🟦 Normal — Coffre standard</button>
            <button class="diff-btn" data-diff="hard" onclick="selectDiff(this)">🔴 Difficile — Coffre Légendaire</button>
        </div>

        <div class="section-label">⌨️ Touches</div>
        <div class="keys-config">
            <span style="color:#94a3b8; font-size:0.85rem;">Contrôles :</span>
            <button class="key-btn active" data-keys="arrows" onclick="selectKeys(this)">⬆️ Flèches</button>
            <button class="key-btn" data-keys="zqsd" onclick="selectKeys(this)">Z Q S D</button>
            <span style="color:#64748b; font-size:0.85rem; margin-left:1rem;" id="keys-hint">
                ↑ Sauter &nbsp;|&nbsp; ↓ Glisser &nbsp;|&nbsp; ← → Changer de voie / Tourner
            </span>
        </div>

        <button id="start-btn" onclick="startGame()">🏃 LANCER LA RUN !</button>
    </div>

    <!-- ZONE JEU -->
    <div id="game-container">
        <div id="game-canvas-wrapper"></div>
    </div>

    <!-- GAME OVER -->
    <div id="gameover-panel">
        <h2>💀 GAME OVER</h2>
        <div class="go-score" id="go-score">0</div>
        <div class="go-sub">
            🪙 <span id="go-coins">0</span> pièces &nbsp;|&nbsp; ⏱️ <span id="go-duration">0</span>s de survie
        </div>
        <div class="go-actions">
            <button class="btn btn-primary" onclick="openChest()">📦 Ouvrir mon coffre</button>
            <button class="btn btn-outline" onclick="restartGame()">🔄 Rejouer</button>
            <a href="{{ route('leaderboard.index') }}" class="btn btn-outline">🏆 Classement</a>
        </div>
    </div>
</div>

<!-- MODAL COFFRE -->
<div id="chest-modal">
    <div class="chest-box">
        <div id="chest-waiting">
            <div class="chest-emoji" id="chest-emoji">📦</div>
            <div class="chest-title" id="chest-title">Coffre Normal</div>
            <div class="chest-desc" id="chest-desc">Ouvre pour débloquer un personnage !</div>
            <button class="btn btn-gold" style="width:100%;padding:1rem;font-size:1.1rem;" onclick="openChestRequest()">✨ Ouvrir !</button>
        </div>
        <div id="chest-result" class="chest-result">
            <div class="result-emoji" id="result-emoji">🧑</div>
            <div class="result-name" id="result-name"></div>
            <div class="result-rarity" id="result-rarity"></div>
            <p id="result-msg" style="color:#94a3b8; margin-bottom:1.5rem;"></p>
            <button class="btn btn-primary" style="width:100%;" onclick="closeChest()">Super !</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/phaser/3.60.0/phaser.min.js"></script>
<script>
// =============================================
//  ÉTAT GLOBAL
// =============================================
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
    document.querySelectorAll('.diff-btn').forEach(b => b.classList.remove('selected-normal','selected-hard'));
    selectedDiff = el.dataset.diff;
    el.classList.add(selectedDiff === 'hard' ? 'selected-hard' : 'selected-normal');
    chestType = selectedDiff === 'hard' ? 'legendary' : 'normal';
}
function selectKeys(el) {
    document.querySelectorAll('.key-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    selectedKeys = el.dataset.keys;
    const hint = document.getElementById('keys-hint');
    hint.textContent = selectedKeys === 'zqsd'
        ? 'Z Sauter | S Glisser | Q D Changer de voie / Tourner'
        : '↑ Sauter | ↓ Glisser | ← → Changer de voie / Tourner';
}

function startGame() {
    document.getElementById('pregame-panel').style.display  = 'none';
    document.getElementById('gameover-panel').style.display = 'none';
    document.getElementById('game-container').style.display = 'flex';
    if (phaserGame) phaserGame.destroy(true);
    phaserGame = new Phaser.Game({
        type: Phaser.AUTO, width: 880, height: 420,
        parent: 'game-canvas-wrapper',
        backgroundColor: '#0a0a14',
        physics: { default: 'arcade', arcade: { debug: false } },
        scene: TempleScene
    });
}
function restartGame() {
    document.getElementById('gameover-panel').style.display = 'none';
    document.getElementById('game-container').style.display = 'flex';
    if (phaserGame) phaserGame.destroy(true);
    phaserGame = new Phaser.Game({
        type: Phaser.AUTO, width: 880, height: 420,
        parent: 'game-canvas-wrapper',
        backgroundColor: '#0a0a14',
        physics: { default: 'arcade', arcade: { debug: false } },
        scene: TempleScene
    });
}

// =============================================
//  SCÈNE PHASER — PSEUDO-3D TEMPLE RUN
// =============================================
class TempleScene extends Phaser.Scene {
    constructor() { super('TempleScene'); }

    create() {
        const W = this.scale.width;
        const H = this.scale.height;
        this.W = W; this.H = H;

        // --- Perspective setup ---
        // Vanishing point
        this.vp = { x: W / 2, y: H * 0.22 };
        // Chemin au bas de l'écran (large) et au VP (étroit)
        this.pathNear = { left: W * 0.08, right: W * 0.92, y: H - 20 };
        this.pathFar  = { left: W/2 - 28, right: W/2 + 28, y: this.vp.y };

        // --- État du jeu ---
        this.lane       = 1;
        this.jumping    = false;
        this.jumpT      = 0;
        this.ducking    = false;
        this.gameOver   = false;
        this.score      = 0;
        this.coins      = 0;
        this.elapsed    = 0;
        this.speed      = selectedDiff === 'hard' ? 0.70 : 0.52;
        this.objects    = [];
        this.turnWarn   = null;
        this.turnCooldown = 0;
        this.laneChangeCooldown = 0;
        this.flashColor = null;
        this.flashAlpha = 0;

        // Monstre derrière
        this.monsterDist = 1.0;  // distance du monstre (1=loin, 0=rattrapé)
        this.monsterGfx  = this.add.graphics().setDepth(2.5);

        // Compte à rebours avant départ
        this.countdown   = 3;
        this.countdownActive = true;
        this.countTxt = this.add.text(W/2, H/2, '3', {
            font: 'bold 120px Bangers', color: '#fbbf24',
            stroke: '#000', strokeThickness: 8
        }).setOrigin(0.5).setDepth(10);
        this.time.addEvent({ delay: 1000, repeat: 2, callback: () => {
            this.countdown--;
            if (this.countdown > 0) {
                this.countTxt.setText('' + this.countdown);
                this.cameras.main.shake(100, 0.005);
            } else {
                this.countTxt.setText('GO !');
                this.countTxt.setColor('#10b981');
                this.time.delayedCall(500, () => {
                    this.countTxt.setAlpha(0);
                    this.countdownActive = false;
                });
            }
        }});

        // --- Graphics layers ---
        this.bgGfx      = this.add.graphics().setDepth(0);
        this.pathGfx    = this.add.graphics().setDepth(1);
        this.objGfx     = this.add.graphics().setDepth(2);
        this.playerGfx  = this.add.graphics().setDepth(3);
        this.hudGfx     = this.add.graphics().setDepth(4);

        // --- Textes HUD ---
        this.scoreTxt = this.add.text(16, 14, 'Score: 0',
            { font: 'bold 22px Bangers', color: '#e2e8f0', letterSpacing: 1 }).setDepth(5);
        this.coinTxt  = this.add.text(16, 44, '🪙 0',
            { font: 'bold 18px Inter', color: '#fbbf24' }).setDepth(5);
        this.diffTxt  = this.add.text(W - 14, 14,
            selectedDiff === 'hard' ? '🔴 DIFFICILE' : '🟦 NORMAL',
            { font: 'bold 15px Inter', color: selectedDiff === 'hard' ? '#ef4444' : '#06b6d4' })
            .setOrigin(1, 0).setDepth(5);

        this.warnTxt = this.add.text(W/2, H/2 - 40, '', {
            font: 'bold 52px Bangers', color: '#ef4444', letterSpacing: 3,
            stroke: '#000', strokeThickness: 6
        }).setOrigin(0.5).setDepth(6).setAlpha(0);

        this.laneHintTxt = this.add.text(W/2, H - 14,
            selectedKeys === 'zqsd' ? 'Z=Sauter  S=Glisser  Q/D=Voie/Virage' : '↑=Sauter  ↓=Glisser  ←/→=Voie/Virage',
            { font: '13px Inter', color: '#1e293b' }).setOrigin(0.5).setDepth(5);

        // --- Touches ---
        const kb = this.input.keyboard;
        this.keys = {
            left:  kb.addKey('LEFT'),  right: kb.addKey('RIGHT'),
            up:    kb.addKey('UP'),    down:  kb.addKey('DOWN'),
            space: kb.addKey('SPACE'),
            q: kb.addKey('Q'), d: kb.addKey('D'),
            z: kb.addKey('Z'), s: kb.addKey('S'),
        };
        // Pour détecter un keydown (pas hold) sur gauche/droite
        this.keys.left.on('down',  () => this.handleHorizontal('left'));
        this.keys.right.on('down', () => this.handleHorizontal('right'));
        this.keys.q.on('down',     () => this.handleHorizontal('left'));
        this.keys.d.on('down',     () => this.handleHorizontal('right'));

        // --- Spawner initial ---
        this.spawnTimer   = 0;
        this.turnTimer    = Phaser.Math.Between(7000, 12000);

        // Spawn quelques pièces initiales
        for (let i = 0; i < 3; i++) {
            this.objects.push({ type:'coin', lane: Phaser.Math.Between(0,2), depth: i * 0.25 });
        }
    }

    // --- Convertit (voie, profondeur) → position écran ---
    // Mapping perspective correct : les objets accélèrent visuellement en s'approchant
    screenPos(lane, depth, jumpOffset = 0) {
        // Courbe perspective : quadratique pour que les objets semblent accélérer
        const t   = depth * depth;
        const lx  = this.pathFar.left  + (this.pathNear.left  - this.pathFar.left)  * t;
        const rx  = this.pathFar.right + (this.pathNear.right - this.pathFar.right) * t;
        const y   = this.pathFar.y     + (this.pathNear.y     - this.pathFar.y)     * t;
        const laneW = (rx - lx) / 3;
        const cx  = lx + laneW * lane + laneW / 2;
        const sc  = 0.05 + t * 0.95;
        const jumpPx = jumpOffset * laneW * 0.7;
        return { x: cx, y: y - jumpPx, scale: sc };
    }

    handleHorizontal(dir) {
        if (this.gameOver) return;
        if (this.laneChangeCooldown > 0) return;

        // Si virage en cours, valider le virage
        if (this.turnWarn) {
            if (dir === this.turnWarn.dir) {
                // Bon virage !
                this.objects = this.objects.filter(o => o.type !== 'wall');
                this.turnWarn = null;
                this.turnCooldown = 4000;
                this.flashScreen(0x10b981, 0.4); // flash vert
                this.tweens.add({ targets: this.warnTxt, alpha: 0, duration: 200 });
            } else {
                // Mauvais virage → mur
                this.triggerGameOver();
            }
            return;
        }

        // Changer de voie
        if (dir === 'left'  && this.lane > 0) { this.lane--; this.laneChangeCooldown = 180; }
        if (dir === 'right' && this.lane < 2) { this.lane++; this.laneChangeCooldown = 180; }
    }

    flashScreen(color, alpha) {
        this.flashColor = color;
        this.flashAlpha = alpha;
    }

    update(time, delta) {
        if (this.gameOver) return;
        if (this.countdownActive) {
            this.drawBackground();
            this.drawPath();
            this.drawMonster(0);
            this.drawPlayer(0);
            return;
        }

        this.elapsed += delta;
        this.score    = Math.floor(this.elapsed / 80 + this.coins * 5);
        this.scoreTxt.setText('Score: ' + this.score);

        // Cooldowns
        if (this.laneChangeCooldown > 0) this.laneChangeCooldown -= delta;
        if (this.turnCooldown > 0) this.turnCooldown -= delta;

        // Vitesse progressive
        this.speed = (selectedDiff === 'hard' ? 0.70 : 0.52) + (this.elapsed / 1000) * 0.008;

        // Saut
        const jumpKey = this.keys.up.isDown || this.keys.space.isDown ||
                        (selectedKeys === 'zqsd' && this.keys.z.isDown);
        if (jumpKey && !this.jumping) {
            this.jumping = true;
            this.jumpT   = 0;
        }
        if (this.jumping) {
            this.jumpT += delta / 480;
            if (this.jumpT >= 1) { this.jumping = false; this.jumpT = 0; }
        }
        const jumpOffset = this.jumping ? Math.sin(this.jumpT * Math.PI) : 0;

        // Se baisser
        this.ducking = this.keys.down.isDown || (selectedKeys === 'zqsd' && this.keys.s.isDown);

        // --- Déplacer objets ---
        const dt = delta / 1000;
        for (const obj of this.objects) obj.depth += this.speed * dt;

        // --- Collision ---
        // Fenêtre de collision précise : seulement quand l'objet est vraiment au niveau du joueur
        for (const obj of this.objects) {
            if (obj.depth < 0.91 || obj.depth > 1.00) continue;

            if (obj.type === 'coin' && obj.lane === this.lane) {
                obj.dead = true;
                this.coins++;
                this.coinTxt.setText('🪙 ' + this.coins);
                this.flashScreen(0xfbbf24, 0.15);
            }
            if (obj.type === 'obstacle' && obj.lane === this.lane) {
                // Obstacle bas → safe si on est EN TRAIN de sauter (milieu du saut)
                const safeJump = this.jumping && this.jumpT > 0.08 && this.jumpT < 0.92;
                // Obstacle haut → safe si accroupi
                const safeDuck = this.ducking;
                const hit = obj.isHigh ? !safeDuck : !safeJump;
                if (hit) { this.triggerGameOver(); return; }
            }
            if (obj.type === 'wall' && obj.depth > 0.95) {
                this.triggerGameOver(); return;
            }
        }

        // Supprimer objets passés ou morts
        this.objects = this.objects.filter(o => o.depth <= 1.05 && !o.dead);

        // --- Spawn objets ---
        this.spawnTimer += delta;
        const spawnDelay = Math.max(600, 1400 - this.elapsed * 0.04);
        if (this.spawnTimer >= spawnDelay) {
            this.spawnTimer = 0;
            this.spawnObject();
        }

        // --- Virage ---
        if (this.turnCooldown <= 0) {
            this.turnTimer -= delta;
            if (this.turnTimer <= 0) {
                this.turnTimer = Phaser.Math.Between(8000, 14000);
                this.triggerTurnWarning();
            }
        }
        if (this.turnWarn) {
            this.turnWarn.timeLeft -= delta;
            // Faire clignoter l'avertissement
            this.warnTxt.setAlpha(Math.abs(Math.sin(this.elapsed / 150)));
            if (this.turnWarn.timeLeft <= 0) {
                // Trop tard — mur atteint
                this.triggerGameOver(); return;
            }
        }

        // --- Monstre qui se rapproche ---
        this.monsterDist = Math.max(0, this.monsterDist - delta * 0.00004);
        if (this.monsterDist <= 0) { this.triggerGameOver(); return; }

        // Alerte monstre proche
        if (this.monsterDist < 0.3) {
            this.flashScreen(0xef4444, 0.06 + (0.3 - this.monsterDist) * 0.15);
        }

        // --- Dessin ---
        this.drawBackground();
        this.drawPath();
        this.drawMonster(this.monsterDist);
        this.drawObjects(jumpOffset);
        this.drawPlayer(jumpOffset);
        if (this.flashAlpha > 0) {
            this.hudGfx.clear();
            this.hudGfx.fillStyle(this.flashColor, this.flashAlpha);
            this.hudGfx.fillRect(0, 0, this.W, this.H);
            this.flashAlpha = Math.max(0, this.flashAlpha - 0.04);
        } else {
            this.hudGfx.clear();
        }
    }

    spawnObject() {
        const r = Math.random();
        if (r < 0.45) {
            // Pièces (1 à 4 dans une rangée)
            const baseLane = Phaser.Math.Between(0, 2);
            const count    = Phaser.Math.Between(1, 3);
            for (let i = 0; i < count; i++) {
                const l = Math.min(2, baseLane + i);
                this.objects.push({ type:'coin', lane: l, depth: 0.01 + i * 0.04 });
            }
        } else if (r < 0.55) {
            // Obstacle bas : flammes ou rochers (faut sauter)
            const obsType = Math.random() < 0.5 ? 0 : 1; // 0=feu, 1=rocher
            this.objects.push({ type:'obstacle', lane: Phaser.Math.Between(0,2), depth: 0.01, isHigh: false, obsType });
        } else if (r < 0.80) {
            // Obstacle haut : branche (faut se baisser)
            this.objects.push({ type:'obstacle', lane: 1, depth: 0.01, isHigh: true, obsType: 2 });
        } else {
            // Combo : deux obstacles côte à côte
            const freeLane = Phaser.Math.Between(0, 2);
            for (let l = 0; l < 3; l++) {
                if (l !== freeLane) {
                    this.objects.push({ type:'obstacle', lane: l, depth: 0.01 + l * 0.01, isHigh: false, obsType: 1 });
                }
            }
        }
    }

    triggerTurnWarning() {
        const dir = Math.random() < 0.5 ? 'left' : 'right';
        this.turnWarn = { dir, timeLeft: 3000 };
        // Mur qui arrive
        this.objects.push({ type:'wall', lane:0, depth:0.01 });
        this.objects.push({ type:'wall', lane:1, depth:0.01 });
        this.objects.push({ type:'wall', lane:2, depth:0.01 });
        // Texte
        this.warnTxt.setText(dir === 'left' ? '← TOURNER À GAUCHE !' : 'TOURNER À DROITE ! →');
        this.warnTxt.setAlpha(1);
    }

    triggerGameOver() {
        if (this.gameOver) return;
        this.gameOver = true;
        lastScore    = this.score;
        lastCoins    = this.coins;
        lastDuration = Math.floor(this.elapsed / 1000);
        this.flashScreen(0xef4444, 0.7);
        this.cameras.main.shake(400, 0.018);

        fetch('{{ route("game.score") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ score: lastScore, coins_collected: lastCoins, difficulty: selectedDiff, duration: lastDuration, character_id: selectedCharId })
        }).then(r => r.json()).then(d => { if (d.success) chestType = d.chest_type; });

        this.time.delayedCall(800, () => {
            document.getElementById('game-container').style.display  = 'none';
            document.getElementById('gameover-panel').style.display  = 'block';
            document.getElementById('go-score').textContent    = lastScore;
            document.getElementById('go-coins').textContent    = lastCoins;
            document.getElementById('go-duration').textContent = lastDuration;
        });
    }

    // ==== DESSIN ====
    drawBackground() {
        const g = this.bgGfx; g.clear();
        // Ciel dégradé
        g.fillGradientStyle(0x0a0a14, 0x0a0a14, 0x1a0a2e, 0x1a0a2e, 1);
        g.fillRect(0, 0, this.W, this.vp.y + 20);
        // Sol lointain
        g.fillStyle(0x1e1b4b, 1);
        g.fillRect(0, this.vp.y + 20, this.W, this.H - this.vp.y - 20);
        // Étoiles
        if (!this._stars) {
            this._stars = Array.from({length: 50}, () => ({
                x: Phaser.Math.Between(0, this.W),
                y: Phaser.Math.Between(0, this.vp.y),
                r: Math.random() > 0.8 ? 2 : 1
            }));
        }
        g.fillStyle(0xffffff, 0.6);
        this._stars.forEach(s => g.fillCircle(s.x, s.y, s.r));
        // Lune
        g.fillStyle(0xfbbf24, 0.12); g.fillCircle(this.W - 70, 45, 32);
        g.fillStyle(0xfbbf24, 0.35); g.fillCircle(this.W - 70, 45, 24);
        // Arbres / colonnes de temple (décor)
        g.fillStyle(0x2d1b69, 1);
        [60, 150, this.W-150, this.W-60].forEach(x => {
            g.fillRect(x - 8, this.vp.y - 60, 16, 80);
            g.fillRect(x - 20, this.vp.y - 70, 40, 12);
        });
    }

    drawPath() {
        const g = this.pathGfx; g.clear();
        const { left: fl, right: fr, y: fy } = this.pathFar;
        const { left: nl, right: nr, y: ny } = this.pathNear;

        // Sol du chemin
        g.fillStyle(0x3730a3, 1);
        g.fillPoints([
            { x: fl, y: fy }, { x: fr, y: fy },
            { x: nr, y: ny }, { x: nl, y: ny }
        ], true);

        // Bandes de séparation de voies
        g.lineStyle(2, 0x7c3aed, 0.5);
        for (let l = 1; l < 3; l++) {
            const ft = fl + (fr - fl) * l / 3;
            const nt = nl + (nr - nl) * l / 3;
            g.beginPath();
            g.moveTo(ft, fy); g.lineTo(nt, ny);
            g.strokePath();
        }

        // Bords du chemin
        g.lineStyle(3, 0x7c3aed, 1);
        g.beginPath(); g.moveTo(fl, fy); g.lineTo(nl, ny); g.strokePath();
        g.beginPath(); g.moveTo(fr, fy); g.lineTo(nr, ny); g.strokePath();

        // Ligne de perspective (centre)
        g.lineStyle(1, 0x4338ca, 0.3);
        g.beginPath(); g.moveTo(this.W/2, fy); g.lineTo(this.W/2, ny); g.strokePath();

        // Carreaux de sol (effet de vitesse)
        const numLines = 8;
        const tOffset  = (this.elapsed * 0.0004 * this.speed) % (1 / numLines);
        for (let i = 0; i < numLines; i++) {
            const t = (i / numLines + tOffset) % 1;
            if (t < 0.05) continue;
            const lx = fl + (nl - fl) * t;
            const rx = fr + (nr - fr) * t;
            const y  = fy + (ny - fy) * t;
            g.lineStyle(1, 0x4338ca, t * 0.4);
            g.beginPath(); g.moveTo(lx, y); g.lineTo(rx, y); g.strokePath();
        }

        // Murs / bordures du temple (côtés)
        g.fillStyle(0x1e1b4b, 1);
        // Mur gauche
        g.fillPoints([
            {x:0, y:fy}, {x:fl, y:fy}, {x:nl, y:ny}, {x:0, y:ny}
        ], true);
        // Mur droit
        g.fillPoints([
            {x:fr, y:fy}, {x:this.W, y:fy}, {x:this.W, y:ny}, {x:nr, y:ny}
        ], true);
        // Détails muraux
        g.fillStyle(0x312e81, 1);
        g.fillRect(0, fy - 8, nl, 8);
        g.fillRect(nr, fy - 8, this.W - nr, 8);
    }

    drawMonster(dist) {
        const g = this.monsterGfx; g.clear();
        // Le monstre est derrière le joueur — plus dist est petit, plus il est grand/proche
        const proximity = 1 - dist; // 0=loin, 1=tout proche
        const sc = 0.15 + proximity * 0.55;
        const mx = this.W / 2;
        const baseY = this.pathNear.y - 10;
        const size  = 80 * sc;

        // Ombre portée
        g.fillStyle(0x000000, 0.3 * sc);
        g.fillEllipse(mx, baseY - 5, size * 1.6, size * 0.35);

        // Corps du monstre (singe démon)
        // Torse
        g.fillStyle(0x1a0a00, 1);
        g.fillEllipse(mx, baseY - size * 0.6, size * 0.9, size * 1.1);

        // Tête
        g.fillStyle(0x2d1200, 1);
        g.fillCircle(mx, baseY - size * 1.25, size * 0.55);

        // Yeux rouges lumineux
        const eyeGlow = 0.6 + Math.sin(this.elapsed * 0.008) * 0.4;
        g.fillStyle(0xff0000, eyeGlow);
        g.fillCircle(mx - size * 0.18, baseY - size * 1.3, size * 0.12);
        g.fillCircle(mx + size * 0.18, baseY - size * 1.3, size * 0.12);
        // Pupilles
        g.fillStyle(0xff6600, 1);
        g.fillCircle(mx - size * 0.18, baseY - size * 1.3, size * 0.06);
        g.fillCircle(mx + size * 0.18, baseY - size * 1.3, size * 0.06);

        // Cornes
        g.fillStyle(0x4a1c00, 1);
        g.fillTriangle(
            mx - size*0.3, baseY - size*1.55,
            mx - size*0.15, baseY - size*1.85,
            mx - size*0.05, baseY - size*1.55
        );
        g.fillTriangle(
            mx + size*0.3,  baseY - size*1.55,
            mx + size*0.15, baseY - size*1.85,
            mx + size*0.05, baseY - size*1.55
        );

        // Bras (animés)
        const armSwing = Math.sin(this.elapsed * 0.01) * 15;
        g.fillStyle(0x1a0a00, 1);
        // Bras gauche
        g.fillRoundedRect(
            mx - size*0.85, baseY - size*0.8 + armSwing,
            size*0.35, size*0.6, size*0.1
        );
        // Bras droit
        g.fillRoundedRect(
            mx + size*0.5, baseY - size*0.8 - armSwing,
            size*0.35, size*0.6, size*0.1
        );

        // Dents
        g.fillStyle(0xffffff, 0.9);
        for (let i = 0; i < 4; i++) {
            g.fillTriangle(
                mx - size*0.2 + i*size*0.13, baseY - size*1.1,
                mx - size*0.14 + i*size*0.13, baseY - size*0.95,
                mx - size*0.08 + i*size*0.13, baseY - size*1.1
            );
        }

        // Effet de vitesse (lignes derrière le monstre)
        if (proximity > 0.4) {
            g.lineStyle(2, 0xef4444, proximity * 0.4);
            for (let i = 0; i < 5; i++) {
                const lx = mx - size * (0.8 + i * 0.3);
                const rx = mx + size * (0.8 + i * 0.3);
                const ly = baseY - size * (0.3 + i * 0.15);
                g.beginPath(); g.moveTo(lx - size*0.5, ly); g.lineTo(lx, ly); g.strokePath();
                g.beginPath(); g.moveTo(rx, ly); g.lineTo(rx + size*0.5, ly); g.strokePath();
            }
        }
    }

    drawObjects(jumpOffset) {
        const g = this.objGfx; g.clear();
        // Trier par profondeur (les plus lointains en premier)
        const sorted = [...this.objects].sort((a, b) => a.depth - b.depth);

        for (const obj of sorted) {
            if (obj.depth <= 0 || obj.depth > 1.02) continue;
            const pos = this.screenPos(obj.lane, obj.depth);
            const sc  = pos.scale;
            const x   = pos.x;
            const y   = pos.y;

            if (obj.type === 'coin') {
                // Pièce dorée style temple
                const r = 10 * sc;
                g.fillStyle(0xfbbf24, 1);
                g.fillCircle(x, y, r);
                g.fillStyle(0xfde68a, 0.9);
                g.fillCircle(x - r*0.2, y - r*0.2, r * 0.45);
                g.lineStyle(1.5 * sc, 0xf59e0b, 1);
                g.strokeCircle(x, y, r);

            } else if (obj.type === 'obstacle') {
                const obsType = obj.obsType || 0;

                if (obj.isHigh) {
                    // Branche d'arbre / tronc horizontal — faut se baisser
                    const lx2 = this.pathFar.left  + (this.pathNear.left  - this.pathFar.left)  * obj.depth * obj.depth;
                    const rx2 = this.pathFar.right + (this.pathNear.right - this.pathFar.right) * obj.depth * obj.depth;
                    const bw  = (rx2 - lx2) * 0.92;
                    const bh  = 22 * sc;
                    const by  = y - bh * 1.6;
                    // Tronc principal
                    g.fillStyle(0x5c3317, 1);
                    g.fillRect(x - bw/2, by, bw, bh);
                    // Texture bois
                    g.fillStyle(0x7a4a28, 0.5);
                    g.fillRect(x - bw/2 + 3, by + 4, bw - 6, bh * 0.35);
                    // Mousses
                    g.fillStyle(0x2d6a2d, 0.8);
                    g.fillRect(x - bw/2, by, bw, 5 * sc);
                    // Poteaux support
                    g.fillStyle(0x4a2810, 1);
                    g.fillRect(x - bw/2 - 4, by, 10 * sc, bh + 20 * sc);
                    g.fillRect(x + bw/2 - 6, by, 10 * sc, bh + 20 * sc);

                } else if (obsType === 1) {
                    // Racines / rochers — faut sauter
                    const rw = 52 * sc, rh = 38 * sc;
                    // Rocher principal
                    g.fillStyle(0x6b7280, 1);
                    g.fillEllipse(x, y - rh*0.5, rw, rh);
                    g.fillStyle(0x9ca3af, 0.5);
                    g.fillEllipse(x - rw*0.15, y - rh*0.7, rw*0.5, rh*0.4);
                    // Petits rochers
                    g.fillStyle(0x4b5563, 1);
                    g.fillEllipse(x + rw*0.4, y - rh*0.3, rw*0.4, rh*0.3);
                    g.fillEllipse(x - rw*0.45, y - rh*0.25, rw*0.35, rh*0.25);

                } else {
                    // Flammes — faut sauter
                    const fw = 44 * sc;
                    const fh = 50 * sc;
                    const flicker = Math.sin(this.elapsed * 0.025 + obj.lane) * 0.15;
                    // Base feu
                    g.fillStyle(0xef4444, 1);
                    g.fillTriangle(x, y - fh * (1.1 + flicker), x - fw*0.5, y, x + fw*0.5, y);
                    // Feu milieu
                    g.fillStyle(0xf97316, 0.9);
                    g.fillTriangle(x, y - fh*(0.85 + flicker), x - fw*0.35, y, x + fw*0.35, y);
                    // Cœur feu
                    g.fillStyle(0xfbbf24, 0.8);
                    g.fillTriangle(x, y - fh*(0.55 + flicker), x - fw*0.2, y, x + fw*0.2, y);
                    // Braises
                    g.fillStyle(0xff6b35, 0.6);
                    g.fillCircle(x + fw*0.3, y - fh*(0.7 + flicker), 4*sc);
                    g.fillCircle(x - fw*0.25, y - fh*(0.9 + flicker), 3*sc);
                }
            } else if (obj.type === 'wall') {
                // Mur de virage
                const lx = this.pathFar.left  + (this.pathNear.left  - this.pathFar.left)  * obj.depth;
                const rx = this.pathFar.right + (this.pathNear.right - this.pathFar.right) * obj.depth;
                const wy = this.pathFar.y     + (this.pathNear.y     - this.pathFar.y)     * obj.depth;
                const alpha = Math.min(0.95, obj.depth * 1.5);
                g.fillStyle(0xef4444, alpha);
                g.fillRect(lx, wy - 80 * sc, rx - lx, 80 * sc);
                g.fillStyle(0xff6b6b, alpha * 0.5);
                g.fillRect(lx, wy - 80 * sc, rx - lx, 10 * sc);
                break; // Un seul mur suffit visuellement
            }
        }
    }

    drawPlayer(jumpOffset) {
        const g = this.playerGfx; g.clear();
        const pos = this.screenPos(this.lane, 0.96, jumpOffset);
        const sc  = pos.scale * 0.9;
        const x   = pos.x;
        const y   = pos.y;

        if (this.ducking) {
            // Accroupi
            g.fillStyle(0x7c3aed, 1);
            g.fillEllipse(x, y - 12 * sc, 52 * sc, 28 * sc);
            g.fillStyle(0xfcd34d, 1);
            g.fillCircle(x, y - 24 * sc, 14 * sc);
        } else {
            // Debout — léger bounce de course
            const bounce = this.jumping ? 0 : Math.sin(this.elapsed * 0.012) * 2;
            // Corps
            g.fillStyle(0x7c3aed, 1);
            g.fillRoundedRect(x - 16*sc, y - 42*sc + bounce, 32*sc, 36*sc, 6*sc);
            // Tête
            g.fillStyle(0xfcd34d, 1);
            g.fillCircle(x, y - 52*sc + bounce, 14*sc);
            // Yeux
            g.fillStyle(0x0a0a14, 1);
            g.fillCircle(x - 5*sc, y - 54*sc + bounce, 2.5*sc);
            g.fillCircle(x + 5*sc, y - 54*sc + bounce, 2.5*sc);
            // Jambes (animation de course)
            const legSwing = Math.sin(this.elapsed * 0.018) * 8 * sc;
            g.fillStyle(0x5b21b6, 1);
            g.fillRect(x - 10*sc, y - 14*sc, 10*sc, 18*sc + legSwing);
            g.fillRect(x + 2*sc,  y - 14*sc, 10*sc, 18*sc - legSwing);
        }

        // Ombre
        g.fillStyle(0x000000, 0.25);
        g.fillEllipse(x, this.pathNear.y - 8, 40*sc, 10*sc);
    }
}

// =============================================
//  COFFRE & SAUVEGARDE
// =============================================
function openChest() {
    document.getElementById('chest-waiting').style.display = 'block';
    document.getElementById('chest-result').style.display  = 'none';
    document.getElementById('chest-emoji').textContent = chestType === 'legendary' ? '🌟' : '📦';
    document.getElementById('chest-title').textContent = chestType === 'legendary' ? 'Coffre Légendaire !' : 'Coffre Normal';
    document.getElementById('chest-desc').textContent  = chestType === 'legendary' ? 'Mode difficile réussi... quelque chose de rare t\'attend !' : 'Ouvre pour débloquer un personnage !';
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
