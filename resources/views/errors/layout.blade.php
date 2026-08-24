<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('code') — @yield('title')</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f4f7fb; color: #111827; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        main { width: min(100%, 560px); padding: 44px; border: 1px solid #dce4ef; border-radius: 18px; background: #fff; box-shadow: 0 18px 55px rgba(15, 23, 42, .08); text-align: center; }
        .code { margin: 0; color: #64748b; font-size: 14px; font-weight: 800; letter-spacing: .16em; }
        h1 { margin: 14px 0 10px; font-size: clamp(28px, 5vw, 40px); line-height: 1.15; }
        p { margin: 0 auto; max-width: 430px; color: #64748b; font-size: 16px; line-height: 1.65; }
        nav { display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-top: 28px; }
        a { min-height: 44px; display: inline-flex; align-items: center; justify-content: center; padding: 0 18px; border-radius: 10px; background: #111827; color: #fff; font-size: 14px; font-weight: 750; text-decoration: none; }
        a.secondary { border: 1px solid #cbd5e1; background: #fff; color: #334155; }
        @media (max-width: 520px) { main { padding: 32px 22px; } nav, a { width: 100%; } }
    </style>
</head>
<body>
    <main>
        <p class="code">ERREUR @yield('code')</p>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <nav aria-label="Actions">
            @auth
                <a href="{{ route('dashboard') }}">Retour au tableau de bord</a>
            @else
                <a href="{{ route('home') }}">Retour à l’accueil</a>
            @endauth
            <a class="secondary" href="{{ url()->current() }}">Réessayer</a>
        </nav>
    </main>
</body>
</html>
