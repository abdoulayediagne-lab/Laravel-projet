<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Street Run') - Street Run</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:      #f6f4f0;
            --surface: #ffffff;
            --border:  #e7e1d6;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --accent:  #f59e0b;
            --gold:    #f59e0b;
            --danger:  #ef4444;
            --success: #16a34a;
            --text:    #33312c;
            --muted:   #8a8478;
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
            height: 56px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-logo {
            font-family: 'Bangers', cursive;
            font-size: 1.6rem;
            letter-spacing: 4px;
            color: var(--text);
            text-decoration: none;
            text-transform: uppercase;
            position: relative;
        }
        .nav-logo span { color: var(--primary); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.8rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.72rem;
            transition: color 0.15s;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .nav-links a:hover { color: var(--primary); }

        .nav-coins {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--bg);
            padding: 0.25rem 0.7rem;
            font-weight: 800;
            color: #b8790f;
            font-size: 0.78rem;
            letter-spacing: 1px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1.1rem;
            font-weight: 800;
            font-size: 0.72rem;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }

        .btn-accent { background: var(--accent); color: #fff; }
        .btn-accent:hover { background: #d97f06; }

        .btn-gold { background: var(--accent); color: #fff; }
        .btn-gold:hover { background: #d97f06; }

        .btn-outline { background: var(--surface); border: 1px solid var(--border); color: var(--muted); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }

        main {
            padding: 2rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.5rem;
        }

        .badge { display: inline-block; padding: 0.18rem 0.55rem; border-radius: 6px; font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; }
        .badge-base      { background: rgba(100,116,139,0.1);  color: #64748b; border: 1px solid rgba(100,116,139,0.25); }
        .badge-normal    { background: rgba(37,99,235,0.08);   color: #2563eb; border: 1px solid rgba(37,99,235,0.25); }
        .badge-legendary { background: rgba(245,158,11,0.1);   color: #d97f06; border: 1px solid rgba(245,158,11,0.3); }

        #toast { position: fixed; bottom: 2rem; right: 2rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; }
        .toast-msg { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 6px 24px rgba(0,0,0,0.08); padding: 0.9rem 1.3rem; animation: slideIn 0.25s ease; max-width: 300px; font-size: 0.82rem; letter-spacing: 0.5px; color: var(--text); }
        .toast-msg.success { border-left: 3px solid var(--success); }
        .toast-msg.error   { border-left: 3px solid var(--danger); }

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
