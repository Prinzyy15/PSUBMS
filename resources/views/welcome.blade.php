<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Behavioral Monitoring System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root{
            --cream: #fbf8f2;
            --accent: #1f4e79;
            --muted: #6b6b6b;
            --card: #ffffff;
        }
        html,body{height:100%;margin:0;font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; background:var(--cream); color:#222}
        .wrap{min-height:100%;display:flex;align-items:center;justify-content:center;padding:40px}
    .card{width:100%;max-width:820px;background:var(--card);box-shadow:0 10px 30px rgba(16,24,40,0.08);border-radius:14px;padding:48px;text-align:center;position:relative;overflow:hidden}
    .logo{width:240px;max-width:60%;height:auto;margin:0 auto 18px;display:block;transition:transform 220ms cubic-bezier(.2,.9,.2,1)}
        h1{margin:0;font-size:22px;color:var(--accent);font-weight:600}
        p.lead{color:var(--muted);margin-top:8px;font-size:15px}
        .actions{margin-top:26px}
        .actions a{display:inline-block;padding:10px 18px;margin:0 8px;border-radius:8px;text-decoration:none;font-weight:600}
        .btn-primary{background:var(--accent);color:#fff}
        .btn-ghost{background:transparent;color:var(--accent);border:1px solid rgba(31,78,121,0.08)}

        /* Subtle float animation for logo */
        @keyframes floaty{0%{transform:translateY(0)}50%{transform:translateY(-8px)}100%{transform:translateY(0)}}
        .logo{animation:floaty 4s ease-in-out infinite}

        /* Decorative soft circle */
        .decor{position:absolute;right:-80px;top:-80px;width:260px;height:260px;border-radius:999px;background:linear-gradient(135deg, rgba(31,78,121,0.08), rgba(31,78,121,0.03));pointer-events:none}

    @media (max-width:640px){.card{padding:28px}.logo{width:180px}}

    .logo:hover{transform:scale(1.04)}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="decor" aria-hidden="true"></div>
            <img src="{{ asset('images/psu_logo.png') }}" class="logo" alt="logo">
            <h1>Behavioral Monitoring System</h1>
            <p class="lead">Pangasinan State University - ACC</p>

            <div class="actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn-primary">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">Login</a>
                        <a href="{{ route('inquiry') }}" class="btn-ghost">Contact Us</a>
                    @endauth
                @endif
            </div>

        </div>
    </div>
</body>
</html>