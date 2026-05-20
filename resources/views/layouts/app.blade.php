<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Street Run') — Street Run</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:      #000;
            --surface: #0a0a0a;
            --border:  #141414;
            --primary: #dc2626;
            --accent:  #facc15;
            --gold:    #facc15;
            --danger:  #ef4444;
            --success: #22c55e;
            --text:    #e5e7eb;
            --muted:   #3d3d3d;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        nav {
            background: #000;
            border-bottom: 1px solid #111;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 56px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        nav::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 56px; height: 2px;
            background: #dc2626;
        }

        .nav-logo {
            font-family: 'Bangers', cursive;
            font-size: 1.6rem;
            letter-spacing: 4px;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            position: relative;
        }
        .nav-logo span { color: #dc2626; }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.8rem;
            list-style: none;
        }

        .nav-links a {
            color: #2d2d2d;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.72rem;
            transition: color 0.15s;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .nav-links a:hover { color: #e5e7eb; }

        .nav-coins {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            border: 1px solid #1c1c1c;
            padding: 0.25rem 0.7rem;
            font-weight: 800;
            color: #facc15;
            font-size: 0.78rem;
            letter-spacing: 1px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            font-weight: 800;
            font-size: 0.72rem;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .btn-primary { background: #dc2626; color: #fff; }
        .btn-primary:hover { background: #b91c1c; }

        .btn-accent { background: #facc15; color: #000; }
        .btn-accent:hover { background: #eab308; }

        .btn-gold { background: #facc15; color: #000; }
        .btn-gold:hover { background: #eab308; }

        .btn-outline { background: transparent; border: 1px solid #1c1c1c; color: #6b7280; }
        .btn-outline:hover { border-color: #dc2626; color: #dc2626; }

        main {
            padding: 2rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card {
            background: #080808;
            border: 1px solid #141414;
            padding: 1.5rem;
        }

        .badge { display: inline-block; padding: 0.18rem 0.55rem; font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; }
        .badge-base      { background: rgba(107,114,128,0.15); color: #4b5563; border: 1px solid #1f2937; }
        .badge-normal    { background: rgba(59,130,246,0.1);   color: #3b82f6; border: 1px solid rgba(59,130,246,0.25); }
        .badge-legendary { background: rgba(250,204,21,0.1);   color: #facc15; border: 1px solid rgba(250,204,21,0.3); }

        #toast { position: fixed; bottom: 2rem; right: 2rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; }
        .toast-msg { background: #0a0a0a; border: 1px solid #141414; padding: 0.9rem 1.3rem; animation: slideIn 0.25s ease; max-width: 300px; font-size: 0.82rem; letter-spacing: 0.5px; }
        .toast-msg.success { border-left: 3px solid #22c55e; }
        .toast-msg.error   { border-left: 3px solid #dc2626; }

        @keyframes slideIn {
            from { transform: translateX(110%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav>
    <a href="{{ route('welcome') }}" class="nav-logo">STREET<span>RUN</span></a>
    <ul class="nav-links">
        @auth
            <li><a href="{{ route('game.index') }}">Jouer</a></li>
            <li><a href="{{ route('collection.index') }}">Collection</a></li>
            <li><a href="{{ route('leaderboard.index') }}">Classement</a></li>
            <li><a href="{{ route('profile.index') }}">Profil</a></li>
            <li><span class="nav-coins">🪙 {{ Auth::user()->coins }}</span></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline">Quitter</button>
                </form>
            </li>
        @else
            <li><a href="{{ route('leaderboard.index') }}">Classement</a></li>
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
