@extends('invite.templates.layout')

@section('title', ($event->title ?? 'Mis XV Años').' · Invitación')

@section('head')
<style>
  :root{
    --bg:#07070b;
    --bg2:#0c0c14;
    --text:#f5f5fb;
    --muted:#c7c7d8;
    --card: rgba(255,255,255,.06);
    --stroke: rgba(255,255,255,.12);

    /* tema por evento */
    --accent: {{ $event->theme['accent'] ?? '#d9b06c' }};
    --accent2: {{ $event->theme['accent2'] ?? '#b58cff' }};

    --radius:22px;
    --shadow: 0 20px 60px rgba(0,0,0,.45);
    --max: 1040px;
  }

  *{ box-sizing:border-box; }
  html{ scroll-behavior:smooth; }
  body{
    margin:0;
    font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
    color:var(--text);
    background:
      radial-gradient(1100px 700px at 20% -10%, color-mix(in oklab, var(--accent) 22%, transparent), transparent 60%),
      radial-gradient(900px 650px at 90% 0%, color-mix(in oklab, var(--accent2) 18%, transparent), transparent 55%),
      radial-gradient(900px 900px at 50% 110%, rgba(255,255,255,.06), transparent 60%),
      linear-gradient(180deg, var(--bg), var(--bg2));
    min-height:100vh;
  }

  a{ color:inherit; text-decoration:none; }
  .wrap{ max-width:var(--max); margin:0 auto; padding:22px; }

  /* Music button */
  .music-btn{
    position:fixed;
    right:16px;
    bottom:16px;
    z-index:999;
    width:52px;
    height:52px;
    border-radius:999px;
    border:1px solid rgba(255,255,255,.18);
    background:rgba(255,255,255,.08);
    color:#fff;
    cursor:pointer;
    font-size:18px;
    font-weight:900;
    display:flex;
    align-items:center;
    justify-content:center;
    backdrop-filter: blur(10px);
  }

  /* Enter screen */
  .enter-screen{
    position:fixed; inset:0; z-index:2000;
    display:flex; align-items:center; justify-content:center;
    background:rgba(0,0,0,.65);
    backdrop-filter: blur(10px);
    padding:18px;
  }
  .enter-card{
    width:min(520px, 92vw);
    border-radius:26px;
    border:1px solid rgba(255,255,255,.16);
    background: rgba(255,255,255,.08);
    box-shadow: var(--shadow);
    padding:22px;
    text-align:center;
  }
  .enter-icon{
    width:64px; height:64px; border-radius:999px;
    margin:0 auto 10px;
    display:flex; align-items:center; justify-content:center;
    background: linear-gradient(135deg, color-mix(in oklab, var(--accent) 40%, transparent), color-mix(in oklab, var(--accent2) 35%, transparent));
    border:1px solid rgba(255,255,255,.14);
    font-size:28px;
  }
  .enter-card h2{ margin:8px 0 6px; font-size:1.35rem; }
  .muted{ color:var(--muted); }
  .enter-small{ margin-top:10px; font-size:.92rem; }

  /* Buttons */
  .btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    padding:12px 16px;
    border-radius:16px;
    border:1px solid rgba(255,255,255,.16);
    background: rgba(255,255,255,.08);
    color: var(--text);
    cursor:pointer;
    font-weight:900;
    transition: transform .12s ease, background .12s ease, border-color .12s ease;
    user-select:none;
  }
  .btn:hover{ transform: translateY(-1px); border-color: rgba(255,255,255,.24); background: rgba(255,255,255,.10); }
  .btn.primary{
    background: linear-gradient(135deg, var(--accent), color-mix(in oklab, var(--accent) 65%, #000));
    border-color: color-mix(in oklab, var(--accent) 55%, transparent);
    color:#1b1202;
  }
  .btn.ghost{
    background: transparent;
    border-color: rgba(255,255,255,.18);
  }

  /* Hero fullscreen */
  .hero{
    position:relative;
    min-height: 92vh;
    border-radius: 28px;
    overflow:hidden;
    border:1px solid rgba(255,255,255,.14);
    box-shadow: var(--shadow);
  }
  .hero-bg{
    position:absolute; inset:0;
    background-size:cover;
    background-position:center;
    transform: scale(1.05);
    filter: saturate(1.02);
  }
  .hero-overlay{
    position:absolute; inset:0;
    background:
      radial-gradient(900px 650px at 30% 15%, rgba(0,0,0,.15), rgba(0,0,0,.65)),
      linear-gradient(to top, rgba(0,0,0,.70), rgba(0,0,0,.15));
  }
  .hero-content{
    position:relative;
    z-index:2;
    padding: 32px 22px;
    height:100%;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
  }
  .badge{
    display:inline-flex;
    gap:10px;
    align-items:center;
    padding:10px 14px;
    border-radius:999px;
    background: rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.18);
    backdrop-filter: blur(12px);
    font-weight:900;
    margin-bottom:12px;
  }
  .hero h1{
    margin:0;
    font-weight:950;
    font-size: clamp(2.3rem, 6vw, 3.6rem);
    line-height:1.05;
  }
  .subtitle{
    margin: 12px auto 0;
    max-width: 60ch;
    color: rgba(245,245,251,.88);
    line-height:1.6;
    font-size:1.06rem;
  }

  .chips{
    margin-top:14px;
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    justify-content:center;
  }
  .chip{
    padding:9px 12px;
    border-radius:999px;
    background: rgba(0,0,0,.20);
    border:1px solid rgba(255,255,255,.14);
    backdrop-filter: blur(10px);
    font-weight:800;
    font-size:.92rem;
  }

  .hero-cta{
    margin-top:18px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    justify-content:center;
  }

  .scroll-hint{
    position:absolute;
    bottom:14px;
    left:50%;
    transform:translateX(-50%);
    opacity:.86;
    font-weight:900;
    font-size:.95rem;
    padding:8px 12px;
    border-radius:999px;
    border:1px solid rgba(255,255,255,.14);
    background: rgba(0,0,0,.18);
    backdrop-filter: blur(10px);
  }

  /* Sections */
  .section{ margin-top:18px; }
  .parallax-section{
    position:relative;
    border-radius:28px;
    overflow:hidden;
    border:1px solid rgba(255,255,255,.12);
    box-shadow: 0 18px 55px rgba(0,0,0,.35);
  }
  .px-overlay{
    position:absolute; inset:0;
    background: linear-gradient(to bottom, rgba(0,0,0,.25), rgba(0,0,0,.70));
  }
  .px-content{
    position:relative;
    z-index:2;
    padding:22px;
    text-align:center;
  }
  .px-content h2{ margin:0; font-size:1.35rem; }
  .muted-on-dark{ color: rgba(245,245,251,.78); }

  /* Countdown boxes */
  .countdown{
    margin-top:14px;
    display:flex;
    gap:12px;
    justify-content:center;
    flex-wrap:wrap;
  }
  .cd-box{
    width:110px;
    border-radius:20px;
    border:1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.08);
    backdrop-filter: blur(10px);
    padding:12px 10px;
  }
  .cd-num{ font-weight:950; font-size:2.0rem; line-height:1; }
  .cd-lbl{ margin-top:6px; font-weight:800; color: rgba(245,245,251,.75); }

  .note{
    margin-top:14px;
    border-radius:18px;
    border:1px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.06);
    padding:12px 14px;
    color: rgba(245,245,251,.86);
    display:inline-block;
  }

  /* Cards grid */
  .card-grid{
    margin-top:16px;
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:12px;
    text-align:left;
  }
  .card{
    border-radius:22px;
    border:1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.08);
    backdrop-filter: blur(10px);
    padding:16px;
  }
  .card-title{ color: rgba(245,245,251,.70); font-weight:800; font-size:.92rem; }
  .card-value{ margin-top:8px; font-weight:950; font-size:1.15rem; }
  .card-sub{ margin-top:4px; color: rgba(245,245,251,.72); font-weight:700; }

  /* Alt section (gallery) */
  .section.alt{
    border-radius:28px;
    border:1px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.05);
    padding:22px;
    box-shadow: 0 16px 55px rgba(0,0,0,.25);
    text-align:center;
  }

  /* Gallery button glass centered */
  .gallery-btn{
    --g1: var(--accent);
    --g2: var(--accent2);
    position:relative;
    width:100%;
    height: 190px;
    border:none;
    border-radius:26px;
    overflow:hidden;
    padding:0;
    cursor:pointer;
    margin-top:14px;
    background:transparent;
    border:1px solid rgba(255,255,255,.14);
    box-shadow:
      0 18px 60px rgba(0,0,0,.45),
      0 0 0 1px rgba(255,255,255,.05) inset,
      0 18px 60px color-mix(in oklab, var(--g1) 18%, transparent);
  }
  .gallery-btn::before{
    content:"";
    position:absolute;
    inset:0;
    border-radius:26px;
    padding:1px;
    background: linear-gradient(135deg, var(--g1), var(--g2));
    -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity:.55;
    pointer-events:none;
  }
  .gallery-btn__bg{
    position:absolute;
    inset:0;
    background-size:cover;
    background-position:center;
    filter: blur(10px) brightness(.72);
    transform: scale(1.12);
  }
  .gallery-btn::after{
    content:"";
    position:absolute;
    inset:0;
    background: linear-gradient(to bottom, rgba(255,255,255,.10), rgba(0,0,0,.40));
    backdrop-filter: blur(14px);
  }
  .gallery-btn__content{
    position:relative;
    z-index:2;
    height:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
    gap:6px;
    padding:20px;
  }
  .gallery-btn__title{
    font-weight:950;
    font-size:1.35rem;
    text-shadow: 0 10px 25px rgba(0,0,0,.45);
  }
  .gallery-btn__meta{
    font-size:.95rem;
    opacity:.86;
  }
  .gallery-hidden{ display:none; }

  /* Gallery modal (with arrows) */
  .gallery-modal{
    position:fixed;
    inset:0;
    background: rgba(0,0,0,.82);
    z-index:9999;
    display:none;
    align-items:center;
    justify-content:center;
    padding:18px;
    opacity:0;
    transition: opacity .18s ease;
  }
  .gallery-modal.open{ display:flex; opacity:1; }
  .gallery-modal img{
    max-width:92vw;
    max-height:82vh;
    object-fit:contain;
    transform: scale(.96);
    opacity: 0;
    transition: transform .18s ease, opacity .18s ease;
    border-radius:18px;
    border:1px solid rgba(255,255,255,.12);
    box-shadow:0 24px 80px rgba(0,0,0,.55);
  }
  .gallery-modal.open img{ transform: scale(1); opacity: 1; }
  .gallery-close{
    position:absolute;
    top:14px;
    right:14px;
    width:44px;
    height:44px;
    border-radius:999px;
    border:1px solid rgba(255,255,255,.18);
    background:rgba(255,255,255,.08);
    color:#fff;
    cursor:pointer;
    font-size:18px;
    font-weight:900;
    display:flex;
    align-items:center;
    justify-content:center;
    backdrop-filter: blur(10px);
  }
  .gallery-nav{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    width:54px;
    height:54px;
    border-radius:999px;
    border:1px solid rgba(255,255,255,.18);
    background:rgba(255,255,255,.08);
    color:#fff;
    cursor:pointer;
    font-size:34px;
    font-weight:900;
    display:flex;
    align-items:center;
    justify-content:center;
    user-select:none;
    backdrop-filter: blur(10px);
  }
  .gallery-prev{ left:14px; }
  .gallery-next{ right:14px; }
  @media (max-width: 640px){ .gallery-nav{ display:none; } }

  /* RSVP section */
  .rsvp{
    border-radius:28px;
    border:1px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.06);
    padding:22px;
    box-shadow: 0 16px 55px rgba(0,0,0,.25);
    text-align:center;
  }
  .rsvp .statusBadge{
    display:inline-flex; gap:8px; align-items:center;
    padding:8px 12px; border-radius:999px;
    font-weight:900; font-size:.92rem;
    border:1px solid rgba(255,255,255,.16);
    background: rgba(255,255,255,.08);
    margin-top:8px;
  }
  .statusBadge.ok{ border-color: rgba(76,209,124,.45); background: rgba(76,209,124,.14); }
  .statusBadge.no{ border-color: rgba(255,90,106,.45); background: rgba(255,90,106,.14); }
  .statusBadge.pending{ }

  .rsvp-note{ margin-top:10px; color: rgba(245,245,251,.78); }

  /* Footer */
  .footer{
    text-align:center;
    margin-top:18px;
    color: rgba(245,245,251,.72);
    font-size:.95rem;
    padding: 16px 6px;
    opacity:.95;
  }

  @media (max-width: 860px){
    .card-grid{ grid-template-columns: 1fr; }
  }
</style>
@endsection

@section('content')
@php
  $quince = $event->celebrant_name ?? 'Mis XV Años';
  $subtitle = $event->theme['subtitle'] ?? 'Una noche para soñar, celebrar y agradecer';
  $message = $event->message ?? 'Con la bendición de Dios y el amor de mi familia...';

  $datePretty = optional($event->starts_at)->locale('es')->translatedFormat('j \\d\\e F \\d\\e Y');
  $yearPretty = optional($event->starts_at)->format('Y');
  $timeText = optional($event->starts_at)->format('g:i A');
  $venue = $event->venue ?? '—';

  $mapsUrl = $event->maps_url ?? null;   // ✅ nuevo campo
  $whatsUrl = $event->theme['whatsapp_url'] ?? null;

  $eventoISO = optional($event->starts_at)->toIso8601String();

  // hero image fallback (si no hay, usamos gradiente)
  $heroImg = $event->hero_image_path ? asset('storage/'.$event->hero_image_path) : null;

  // status invite
  $status = $invite->status; // ACTIVE | CONFIRMED | DECLINED

  $publicHash = request()->route('hash') ?? ($invite->hash_id ?? $invite->hash ?? null);
  $rsvpUrl = $publicHash ? route('invite.rsvp', ['hash' => $publicHash]) : null;
@endphp

{{-- Enter screen (solo si hay música) --}}
@if($event->music_path)
<div id="enterScreen" class="enter-screen">
  <div class="enter-card">
    <div class="enter-icon">🎵</div>
    <h2>¡Bienvenido!</h2>
    <p class="muted">Toca para entrar y disfrutar la invitación</p>
    <button id="enterBtn" class="btn primary" type="button">Entrar</button>
    <div class="enter-small muted">Tip: sube el volumen del cel 🔊</div>
  </div>
</div>
@endif

{{-- Music button --}}
@if($event->music_path)
<button id="musicBtn" class="music-btn" aria-label="Música">🔇</button>
@endif

<div class="wrap">

  {{-- HERO --}}
  <header class="hero" id="top">
    <div class="hero-bg" style="{{ $heroImg ? "background-image:url('".$heroImg."')" : "background: radial-gradient(700px 500px at 30% 10%, color-mix(in oklab, var(--accent) 35%, transparent), transparent 60%), radial-gradient(650px 500px at 85% 20%, color-mix(in oklab, var(--accent2) 30%, transparent), transparent 60%), linear-gradient(180deg, rgba(255,255,255,.06), rgba(0,0,0,.22));" }}"></div>
    <div class="hero-overlay"></div>

    <div class="hero-content">
      <div class="badge">✨ Invitación personal · {{ $invite->guest_name }}</div>

      <h1>{{ $event->title ?? 'Mis XV Años' }}</h1>
      <p class="subtitle">{{ $subtitle }}</p>

      <div class="chips">
        <span class="chip">👗 {{ $event->dress_code ?? 'Formal' }}</span>
        <span class="chip">📍 {{ $venue }}</span>
        <span class="chip">📅 {{ $datePretty }}</span>
      </div>

      <div class="hero-cta">
        <a class="btn primary" href="#rsvp">Confirmar asistencia</a>
        <a class="btn ghost" href="#detalles">Ver detalles</a>
      </div>
    </div>

    <div class="scroll-hint">Desliza ↓</div>
  </header>

  {{-- COUNTDOWN --}}
  <section class="section parallax-section" id="countdownSection">
    <div class="px-overlay"></div>
    <div class="px-content">
      <h2>⏳ Cuenta regresiva</h2>
      <p class="muted muted-on-dark">Muy pronto celebraremos un día muy especial</p>

      <div class="countdown">
        <div class="cd-box"><div class="cd-num" id="d">--</div><div class="cd-lbl">Días</div></div>
        <div class="cd-box"><div class="cd-num" id="h">--</div><div class="cd-lbl">Horas</div></div>
        <div class="cd-box"><div class="cd-num" id="m">--</div><div class="cd-lbl">Min</div></div>
        <div class="cd-box"><div class="cd-num" id="s">--</div><div class="cd-lbl">Seg</div></div>
      </div>

      <div class="note">✨ Ven con ganas de celebrar, abrazar y tomarte fotos bonitas.</div>
    </div>
  </section>

  {{-- DETALLES --}}
  <section class="section parallax-section reveal" id="detalles">
    <div class="px-overlay"></div>
    <div class="px-content">
      <h2>📅 Detalles del evento</h2>

      <div class="card-grid">
        <div class="card">
          <div class="card-title">Fecha</div>
          <div class="card-value">{{ $datePretty }}</div>
          <div class="card-sub">{{ $yearPretty }}</div>
        </div>

        <div class="card">
          <div class="card-title">Hora</div>
          <div class="card-value">{{ $timeText }}</div>
          <div class="card-sub">Hora de México</div>
        </div>

        <div class="card">
          <div class="card-title">Lugar</div>
          <div class="card-value">{{ $venue }}</div>
          <div class="card-sub">Te esperamos 💖</div>
        </div>
      </div>

      <p class="muted muted-on-dark" style="margin-top:18px;">
        Si puedes, llega puntual para disfrutar todo desde el inicio 🥳
      </p>
    </div>
  </section>

  {{-- UBICACIÓN --}}
  <section class="section parallax-section reveal" id="ubicacion">
    <div class="px-overlay"></div>
    <div class="px-content">
      <h2>📍 Ubicación</h2>
      <p class="muted muted-on-dark">Toca el botón para abrir Google Maps</p>

      @if($mapsUrl)
        <a class="btn primary" target="_blank" rel="noopener" href="{{ $mapsUrl }}">Abrir en Maps</a>
      @else
        <div class="note">Aún no se configuró el mapa del evento.</div>
      @endif

      <div class="note" style="margin-top:12px;">
        <strong>Dirección:</strong> {{ $venue }}
      </div>
    </div>
  </section>

  {{-- GALERÍA --}}
  @if($event->photos && $event->photos->count())
    <section class="section alt reveal" id="fotos">
      <h2>📸 Fotos</h2>
      <p class="muted">Toca para ver la galería completa</p>

      <button id="openGalleryBtn" class="gallery-btn" type="button">
        <span class="gallery-btn__bg" style="background-image:url('{{ asset('storage/'.$event->photos->first()->path) }}')" aria-hidden="true"></span>
        <span class="gallery-btn__content">
          <span class="gallery-btn__title">Ver fotos</span>
          <span class="gallery-btn__meta">{{ $event->photos->count() }} recuerdos especiales</span>
        </span>
      </button>

      <div class="note">Desliza para cambiar de foto y toca fuera para cerrar.</div>

      <div class="gallery-hidden" aria-hidden="true">
        @foreach($event->photos->take(12) as $p)
          <img src="{{ asset('storage/'.$p->path) }}" alt="Foto" loading="lazy">
        @endforeach
      </div>
    </section>
  @endif

  {{-- RSVP --}}
  <section class="section rsvp reveal" id="rsvp">
    <h2>✅ Confirma tu asistencia</h2>
    <p class="muted">Tu invitación tiene <strong>{{ $invite->seats }}</strong> lugares reservados.</p>

    @if($status === 'CONFIRMED')
      <div class="statusBadge ok">✅ Asistencia confirmada</div>
      <div class="rsvp-note">Gracias por confirmar 💖</div>
    @elseif($status === 'DECLINED')
      <div class="statusBadge no">❌ No podrá asistir</div>
      <div class="rsvp-note">Gracias por avisarnos ✨</div>
    @else
      <div class="statusBadge pending">⏳ Pendiente de confirmar</div>

      <div style="margin-top:14px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
        <button class="btn primary" id="btnYes" type="button">Confirmo asistencia</button>
        <button class="btn" id="btnNo" type="button">No podré asistir</button>
      </div>

      <div class="note" style="margin-top:12px;">
        Invitación personal e intransferible. Agradecemos respetar los lugares asignados.
      </div>
    @endif
  </section>

  <footer class="footer">
    <div class="small">Con cariño, <strong>{{ $quince }}</strong> ✨</div>
  </footer>

</div>
@endsection

@section('scripts')
<script>
  // helpers
  //const qs = (id) => document.getElementById(id);

  // Countdown
  const EVENTO_ISO = @json($eventoISO);
  function startCountdown(){
    if(!EVENTO_ISO) return;
    const target = new Date(EVENTO_ISO).getTime();
    const d = qs('d'), h = qs('h'), m = qs('m'), s = qs('s');

    function tick(){
      const now = Date.now();
      let diff = Math.max(0, target - now);

      const days = Math.floor(diff / (1000*60*60*24));
      diff -= days * (1000*60*60*24);
      const hours = Math.floor(diff / (1000*60*60));
      diff -= hours * (1000*60*60);
      const mins = Math.floor(diff / (1000*60));
      diff -= mins * (1000*60);
      const secs = Math.floor(diff / 1000);

      if(d) d.textContent = String(days);
      if(h) h.textContent = String(hours);
      if(m) m.textContent = String(mins);
      if(s) s.textContent = String(secs);
    }
    tick();
    setInterval(tick, 1000);
  }
  startCountdown();

  // Music (enter screen)
  @if($event->music_path)
  const MUSIC_URL = @json(asset('storage/'.$event->music_path));
  const enterScreen = qs('enterScreen');
  const enterBtn = qs('enterBtn');
  const musicBtn = qs('musicBtn');

  let audio = new Audio(MUSIC_URL);
  audio.loop = true;
  window.__partyx_audio = audio;

  function setMusicIcon(){
    if(!musicBtn) return;
    musicBtn.textContent = audio.paused ? '🔇' : '🔊';
  }

  if(enterBtn){
    enterBtn.addEventListener('click', async ()=>{
      try{
        await audio.play();
        if(enterScreen) enterScreen.style.display = 'none';
        setMusicIcon();
      }catch(e){
        // igual deja entrar
        if(enterScreen) enterScreen.style.display = 'none';
        setMusicIcon();
      }
    });
  }

  if(musicBtn){
    musicBtn.addEventListener('click', async ()=>{
      try{
        if(audio.paused) await audio.play();
        else audio.pause();
      }catch(e){}
      setMusicIcon();
    });
    setMusicIcon();
  }
  @endif

  // Gallery modal (with arrows + animation + ducking)
  (function(){
    const btn = qs('openGalleryBtn');
    if(!btn) return;

    const imgs = Array.from(document.querySelectorAll('.gallery-hidden img'));
    if(!imgs.length) return;

    const srcs = imgs.map(i => i.src);
    let index = 0;

    const modal = document.createElement('div');
    modal.className = 'gallery-modal';
    modal.innerHTML = `
      <button class="gallery-close" type="button" aria-label="Cerrar">✕</button>
      <button class="gallery-nav gallery-prev" type="button" aria-label="Anterior">‹</button>
      <img src="" alt="Foto">
      <button class="gallery-nav gallery-next" type="button" aria-label="Siguiente">›</button>
    `;
    document.body.appendChild(modal);

    const modalImg = modal.querySelector('img');
    const closeBtn = modal.querySelector('.gallery-close');
    const prevBtn = modal.querySelector('.gallery-prev');
    const nextBtn = modal.querySelector('.gallery-next');

    const audio = window.__partyx_audio || null;
    let prevVolume = null;

    function setImage(){
      modalImg.src = srcs[index];
    }
    function openAt(i){
      index = i;
      setImage();
      modal.classList.add('open');
      document.body.style.overflow = 'hidden';

      // duck music
      if(audio){
        prevVolume = audio.volume;
        audio.volume = Math.max(0.12, prevVolume * 0.25);
      }
    }
    function close(){
      modal.classList.remove('open');
      document.body.style.overflow = '';

      // restore music
      if(audio && prevVolume !== null){
        audio.volume = prevVolume;
        prevVolume = null;
      }
    }
    function go(dir){
      index = (index + dir + srcs.length) % srcs.length;
      setImage();
    }

    btn.addEventListener('click', ()=>openAt(0));
    closeBtn.addEventListener('click', close);
    modal.addEventListener('click', (e)=>{ if(e.target === modal) close(); });

    prevBtn.addEventListener('click', ()=>go(-1));
    nextBtn.addEventListener('click', ()=>go(1));

    document.addEventListener('keydown', (e)=>{
      if(!modal.classList.contains('open')) return;
      if(e.key === 'Escape') close();
      if(e.key === 'ArrowLeft') go(-1);
      if(e.key === 'ArrowRight') go(1);
    });

    let startX=0;
    modal.addEventListener('touchstart', (e)=>{ startX = e.touches[0].clientX; }, {passive:true});
    modal.addEventListener('touchend', (e)=>{
      const dx = e.changedTouches[0].clientX - startX;
      if(Math.abs(dx)>40) go(dx < 0 ? 1 : -1);
    }, {passive:true});
  })();

  // RSVP (tu endpoint actual)
  //const qs = (id) => document.getElementById(id);

    //const RSVP_URL = @json($rsvpUrl);
    const csrf = @json(csrf_token());

    if (!RSVP_URL) {
        console.warn('RSVP_URL missing');
        const yes = document.getElementById('btnYes');
        const no = document.getElementById('btnNo');
        if (yes) yes.disabled = true;
        if (no) no.disabled = true;
    }

    async function postRSVP(resp){ // resp: 'SI' | 'NO'
    const res = await fetch(RSVP_URL, {
        method: 'POST',
        credentials: 'same-origin', // 👈 importante para cookies/CSRF
        headers:{
        'Content-Type':'application/json',
        'Accept':'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ response: resp })
    });

    let data = null;
    try { data = await res.json(); } catch(e) {}

    if(!res.ok){
        throw new Error('HTTP ' + res.status);
    }
    return data;
    }

    const yes = qs('btnYes');
    const no  = qs('btnNo');

    if(yes) yes.addEventListener('click', async ()=>{
    try{
        yes.disabled = true; if(no) no.disabled = true;
        await postRSVP('SI');
        location.reload();
    }catch(e){
        alert('No se pudo enviar. Intenta de nuevo.');
        yes.disabled = false; if(no) no.disabled = false;
    }
    });

    if(no) no.addEventListener('click', async ()=>{
    try{
        no.disabled = true; if(yes) yes.disabled = true;
        await postRSVP('NO');
        location.reload();
    }catch(e){
        alert('No se pudo enviar. Intenta de nuevo.');
        no.disabled = false; if(yes) yes.disabled = false;
    }
    });
</script>
@endsection
