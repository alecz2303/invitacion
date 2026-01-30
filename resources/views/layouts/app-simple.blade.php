<!doctype html>
<html lang="es-MX">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'PartyX Events')</title>
  <style>
    :root{--bg:#0b0b12;--card:rgba(255,255,255,.06);--stroke:rgba(255,255,255,.12);--text:#fff;--muted:rgba(255,255,255,.75);--radius:16px}
    body{margin:0;font-family:system-ui,Segoe UI,Roboto,Arial;background:var(--bg);color:var(--text)}
    a{color:inherit}
    .wrap{max-width:1040px;margin:0 auto;padding:20px}
    .top{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px}
    .card{background:var(--card);border:1px solid var(--stroke);border-radius:var(--radius);padding:16px}
    .muted{color:var(--muted)}
    .row{display:flex;gap:10px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);cursor:pointer;font-weight:800;text-decoration:none}
    .btnPrimary{background:#d9b06c;color:#1b1202;border-color:rgba(217,176,108,.6)}
    .btnDanger{background:rgba(255,90,106,.18);border-color:rgba(255,90,106,.35)}
    input,select{width:100%;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(0,0,0,.18);color:#fff;outline:none}
    label{display:block;font-size:.9rem;color:var(--muted);margin:10px 0 6px}
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px;border-bottom:1px solid rgba(255,255,255,.12);text-align:left}
    th{color:var(--muted);font-weight:800;font-size:.9rem}
    .badge{display:inline-flex;align-items:center;gap:8px;padding:8px 10px;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06)}
    .flash{margin:10px 0;padding:10px 12px;border-radius:12px;border:1px solid rgba(76,209,124,.35);background:rgba(76,209,124,.12)}
    .err{margin:10px 0;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,90,106,.35);background:rgba(255,90,106,.12)}
    .small{font-size:.9rem}
    .right{text-align:right}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="top">
      <div>
        <div style="font-weight:900;font-size:1.2rem">@yield('h1','PartyX Events')</div>
        <div class="muted small">@yield('subtitle')</div>
      </div>
      <div class="row">
        @auth
          <span class="badge small">👤 {{ auth()->user()->name }} <span class="muted">({{ auth()->user()->role }})</span></span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btnDanger" type="submit">Salir</button>
          </form>
        @endauth
      </div>
    </div>

    @if (session('status'))
      <div class="flash">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="err">
        <b>Revisa:</b>
        <ul>
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </div>
</body>
</html>
