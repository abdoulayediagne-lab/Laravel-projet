<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Temple Run' }} — Temple Run</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:       #0a0a14;
            --surface:  #12122a;
            --border:   #2a2a50;
            --primary:  #7c3aed;
            --accent:   #06b6d4;
            --gold:     #fbbf24;
            --danger:   #ef4444;
            --success:  #10b981;
            --text:     #e2e8f0;
            --muted:    #64748b;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-logo {
            font-family: 'Bangers', cursive;
            font-size: 1.8rem;
            letter-spacing: 2px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-links a:hover { color: var(--text); }

        .nav-coins {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.3);
            padding: 0.3rem 0.8rem;
            border-radius: 99px;
            font-weight: 700;
            color: var(--gold);
            font-size: 0.9rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #6d28d9; transform: translateY(-1px); }

        .btn-accent { background: var(--accent); color: #0a0a14; }
        .btn-accent:hover { opacity: 0.9; transform: translateY(-1px); }

        .btn-gold { background: var(--gold); color: #0a0a14; }
        .btn-gold:hover { opacity: 0.9; transform: translateY(-1px); }

        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }

        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-base      { background: rgba(100,116,139,0.2); color: #94a3b8; border: 1px solid #334155; }
        .badge-normal    { background: rgba(6,182,212,0.15);  color: var(--accent); border: 1px solid rgba(6,182,212,0.3); }
        .badge-legendary { background: rgba(251,191,36,0.15); color: var(--gold);   border: 1px solid rgba(251,191,36,0.4); }

        #toast { position: fixed; bottom: 2rem; right: 2rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; }
        .toast-msg { background: var(--surface); border: 1px solid var(--border); padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease; max-width: 320px; font-size: 0.9rem; }
        .toast-msg.success { border-color: var(--success); }
        .toast-msg.error   { border-color: var(--danger); }

        @keyframes slideIn {
            from { transform: translateX(120%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav>
    <a href="{{ route('welcome') }}" class="nav-logo">🏃 TEMPLE RUN</a>
    <ul class="nav-links">
        @auth
            <li><a href="{{ route('game.index') }}">🎮 Jouer</a></li>
            <li><a href="{{ route('collection.index') }}">🃏 Collection</a></li>
            <li><a href="{{ route('leaderboard.index') }}">🏆 Classement</a></li>
            <li><a href="{{ route('profile.index') }}">👤 Profil</a></li>
            <li><span class="nav-coins">🪙 {{ Auth::user()->coins }}</span></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="font-size:0.85rem;padding:0.4rem 0.9rem;">Déconnexion</button>
                </form>
            </li>
        @else
            <li><a href="{{ route('leaderboard.index') }}">🏆 Classement</a></li>
            <li><a href="{{ route('login') }}" class="btn btn-outline">Connexion</a></li>
            <li><a href="{{ route('register') }}" class="btn btn-primary">S'inscrire</a></li>
        @endauth
    </ul>
</nav>

<main>
    @yield('content')
</main>

<div id="toast"></div>

<script>
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const msg = document.createElement('div');
    msg.className = `toast-msg ${type}`;
    msg.textContent = message;
    toast.appendChild(msg);
    setTimeout(() => msg.remove(), 4000);
}
</script>
@stack('scripts')
</body>
</html>
