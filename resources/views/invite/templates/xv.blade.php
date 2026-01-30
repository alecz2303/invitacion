@extends('invite.templates.layout')

@section('title', ($event->title ?? 'Mis XV Años').' · Invitación')

@section('head')
<style>
  :root{
    --bg:#07070b;
    --bg2:#0c0c14;
    --card: rgba(255,255,255,.06);
    --stroke: rgba(255,255,255,.10);
    --text:#f5f5fb;
    --muted:#c7c7d8;
    --accent:#d9b06c;
    --accent2:#b58cff;
    --danger:#ff5a6a;
    --ok:#4cd17c;
    --radius:22px;
    --shadow: 0 20px 60px rgba(0,0,0,.45);
    --max: 980px;
  }

  *{ box-sizing:border-box; }
  body{
    margin:0;
    font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
    color:var(--text);
    background:
      radial-gradient(1100px 700px at 20% -10%, rgba(217,176,108,.20), transparent 60%),
      radial-gradient(900px 650px at 90% 0%, rgba(181,140,255,.18), transparent 55%),
      radial-gradient(900px 900px at 50% 110%, rgba(255,255,255,.06), transparent 60%),
      linear-gradient(180deg, var(--bg), var(--bg2));
    min-height:100vh;
  }

  a{ color:inherit; text-decoration:none; }
  .wrap{ max-width:var(--max); margin:0 auto; padding:22px; }

  .topbar{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px; opacity:.95; }
  .badge{ display:inline-flex; align-items:center; gap:10px; padding:10px 14px; border-radius:999px; background: rgba(255,255,255,.06); border:1px solid var(--stroke); backdrop-filter: blur(10px); }
  .dot{ width:10px; height:10px; border-radius:999px; background: linear-gradient(135deg, var(--accent), var(--accent2)); box-shadow: 0 0 0 4px rgba(217,176,108,.12); }
  .small{ font-size:.92rem; color:var(--muted); }

  .hero{ position:relative; border-radius:var(--radius); border:1px solid var(--stroke); background: linear-gradient(135deg, rgba(255,255,255,.08), rgba(255,255,255,.03)); box-shadow: var(--shadow); overflow:hidden; }
  .hero::before{
    content:""; position:absolute; inset:-2px;
    background:
      radial-gradient(700px 500px at 30% 10%, rgba(217,176,108,.28), transparent 60%),
      radial-gradient(650px 500px at 85% 20%, rgba(181,140,255,.25), transparent 60%);
    pointer-events:none;
  }
  .heroInner{ position:relative; padding:36px 20px; text-align:center; }
  .kicker{ letter-spacing:.22em; text-transform:uppercase; font-size:.78rem; color:var(--muted); margin-bottom:10px; }
  .name{ font-weight:900; font-size: clamp(2.2rem, 5vw, 3.3rem); margin: 0; line-height: 1.05; }
  .subtitle{ margin: 12px auto 0; max-width: 60ch; color:var(--muted); font-size: 1.02rem; line-height: 1.55; }

  .grid{ margin-top:16px; display:grid; grid-template-columns: 1.1fr .9fr; gap:14px; align-items:start; }

  .card{ border-radius:var(--radius); border:1px solid var(--stroke); background: var(--card); backdrop-filter: blur(10px); box-shadow: 0 16px 50px rgba(0,0,0,.30); }
  .cardPad{ padding:18px; }
  .cardTitle{ font-weight:800; margin: 0 0 8px; font-size:1.05rem; }
  .cardText{ margin:0; color:var(--muted); line-height:1.55; }

  .facts{ display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:12px; }
  .fact{ padding:14px; border-radius:18px; border:1px solid rgba(255,255,255,.08); background: rgba(0,0,0,.12); }
  .fact .l{ color:var(--muted); font-size:.9rem; margin-bottom:6px; }
  .fact .v{ font-weight:800; }

  .btnRow{ display:flex; gap:10px; flex-wrap:wrap; margin-top:14px; }
  .btn{ display:inline-flex; align-items:center; justify-content:center; gap:10px; padding:12px 14px; border-radius:16px; border:1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.06); color: var(--text); cursor:pointer; font-weight:800; transition: transform .12s ease, background .12s ease, border-color .12s ease; user-select:none; }
  .btn:hover{ transform: translateY(-1px); border-color: rgba(255,255,255,.22); background: rgba(255,255,255,.08); }
  .btnPrimary{ background: linear-gradient(135deg, var(--accent), rgba(217,176,108,.75)); color:#1b1202; border-color: rgba(217,176,108,.55); }
  .btnDanger{ background: rgba(255,90,106,.14); border-color: rgba(255,90,106,.35); }

  .countdown{ display:flex; gap:10px; flex-wrap:wrap; margin-top:12px; }
  .pill{ padding:10px 12px; border-radius:999px; border:1px solid rgba(255,255,255,.10); background: rgba(0,0,0,.14); color: var(--text); font-weight:900; }
  .pill span{ color:var(--muted); font-weight:700; margin-left:6px; }

  .divider{ height:1px; background: rgba(255,255,255,.10); margin: 14px 0; }

  input{
    width:100%;
    padding:12px 12px;
    border-radius:16px;
    border:1px solid rgba(255,255,255,.14);
    background: rgba(0,0,0,.15);
    color: var(--text);
    outline:none;
    font-size:1rem;
  }
  input::placeholder{ color: rgba(199,199,216,.70); }

  .heroPhoto{
    width: 140px;
    height: 140px;
    border-radius: 999px;
    object-fit: cover;
    border: 1px solid rgba(255,255,255,.18);
    box-shadow: 0 18px 50px rgba(0,0,0,.35);
    margin: 10px auto 0;
    display:block;
  }

  .galleryGrid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
    margin-top:12px;
  }
  .galleryItem{
    padding:0;
    border:1px solid rgba(255,255,255,.10);
    border-radius:16px;
    overflow:hidden;
    background:transparent;
    cursor:pointer;
  }
  .galleryItem img{
    width:100%;
    height:120px;
    object-fit:cover;
    display:block;
  }

  .galleryLightbox{
    position:fixed;
    inset:0;
    display:none;
    align-items:center;
    justify-content:center;
    background:rgba(0,0,0,.78);
    z-index:9999;
    padding:18px;
  }
  .galleryLightbox.open{ display:flex; }
  .galleryLightbox img{
    max-width:min(920px, 92vw);
    max-height:82vh;
    border-radius:18px;
    border:1px solid rgba(255,255,255,.12);
    box-shadow:0 24px 80px rgba(0,0,0,.55);
  }

  .statusBadge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 12px;
    border-radius:999px;
    font-weight:900;
    font-size:.9rem;
    border:1px solid transparent;
  }

  .statusBadge.pending{
    background:rgba(255,255,255,.08);
    border-color:rgba(255,255,255,.18);
    color:#fff;
  }

  .statusBadge.ok{
    background:rgba(76,209,124,.18);
    border-color:rgba(76,209,124,.45);
    color:#eafff2;
  }

  .statusBadge.no{
    background:rgba(255,90,106,.18);
    border-color:rgba(255,90,106,.45);
    color:#ffecee;
  }

  .section{
    margin-top:20px;
  }
  .section.alt{
    background:rgba(255,255,255,.04);
    border-radius:22px;
    padding:18px;
  }
  .section h2{
    margin:0 0 4px;
  }
  .muted{
    color:var(--muted);
    font-size:.95rem;
  }

  /* ===== Galería botón estilo glass ===== */

  .gallery-btn{
    position:relative;
    width:100%;
    height:180px;
    border:none;
    border-radius:26px;
    overflow:hidden;
    padding:0;
    cursor:pointer;
    margin-top:14px;
    background:transparent;
    border: 1px solid rgba(255,255,255,.14);
    box-shadow:
      0 18px 60px rgba(0,0,0,.45),
      0 0 0 1px rgba(255,255,255,.05) inset,
      0 18px 60px color-mix(in oklab, var(--g1, #d9b06c) 18%, transparent);
  }

  .gallery-btn::before{
    content:"";
    position:absolute;
    inset:0;
    border-radius:26px;
    padding:1px;
    background: linear-gradient(135deg, var(--g1, #d9b06c), var(--g2, #b58cff));
    -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity:.55;
    pointer-events:none;
  }

  /* Fondo con blur */
  .gallery-btn__bg{
    position:absolute;
    inset:0;
    background-size:cover;
    background-position:center;
    filter: blur(8px) brightness(.75);
    transform: scale(1.12);
  }

  /* Capa glass */
  .gallery-btn::after{
    content:"";
    position:absolute;
    inset:0;
    background:
      linear-gradient(
        to bottom,
        rgba(255,255,255,.10),
        rgba(0,0,0,.35)
      );
    backdrop-filter: blur(12px);
  }

  /* Contenido centrado */
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

  /* Texto principal */
  .gallery-btn__title{
    font-weight:900;
    font-size:1.35rem;
    letter-spacing:.02em;
  }

  /* Subtexto */
  .gallery-btn__meta{
    font-size:.9rem;
    opacity:.85;
  }

  /* Hover sutil */
  .gallery-btn:hover .gallery-btn__bg{
    filter: blur(6px) brightness(.85);
  }

  .gallery-btn__title{
    text-shadow: 0 6px 20px rgba(0,0,0,.45);
  }

  .note{
    font-size:.85rem;
    color:var(--muted);
    margin-top:10px;
  }

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

  .gallery-modal.open{
    display:flex;
    opacity:1;
  }

  .gallery-modal img{
    max-width:92vw;
    max-height:82vh;
    object-fit:contain;

    transform: scale(.96);
    opacity: 0;
    transition: transform .18s ease, opacity .18s ease;
  }

  .gallery-modal.open img{
    transform: scale(1);
    opacity: 1;
  }

  /* Close */
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
  }

  /* Flechas PC */
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
  }

  .gallery-prev{ left:14px; }
  .gallery-next{ right:14px; }

  @media (max-width: 640px){
    .gallery-nav{ display:none; } /* en móvil mejor swipe */
  }

  .gallery-hidden{
    display:none;
  }

  .lbClose{
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
  }

  .lbNav{
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
  }
  .lbPrev{ left:14px; }
  .lbNext{ right:14px; }

  @media (max-width:520px){
    .galleryGrid{ grid-template-columns:repeat(2,1fr); }
    .galleryItem img{ height:110px; }
  }

  .status{ display:none; margin-top:12px; padding:12px; border-radius:16px; border:1px solid rgba(255,255,255,.12); color: var(--muted); }
  .status.ok{ display:block; border-color: rgba(76,209,124,.40); background: rgba(76,209,124,.12); color: var(--text); }
  .status.bad{ display:block; border-color: rgba(255,90,106,.40); background: rgba(255,90,106,.12); color: var(--text); }

  .footer{ text-align:center; margin-top:18px; color: var(--muted); font-size:.95rem; padding: 16px 6px; opacity:.95; }
  .loading{ font-size:.95rem; color: var(--muted); margin-top: 10px; opacity:.95; }

  @media (max-width:860px){ .grid{ grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
@php
  // Defaults por si faltan datos
  $quince = $event->celebrant_name ?? '—';
  $subtitle = $event->theme['subtitle'] ?? 'Una noche para soñar, celebrar y agradecer';
  $message = $event->message ?? 'Con la bendición de Dios y el amor de mi familia...';
  $dateText = optional($event->starts_at)->locale('es')->translatedFormat('l j \\d\\e F \\d\\e Y');
  $timeText = optional($event->starts_at)->format('g:i a');
  $venue = $event->venue ?? '—';
  $dress = $event->dress_code ?? 'Formal';

  // Opcional: maps/whats en theme JSON
  $mapsUrl = $event->maps_url;
  $whatsUrl = $event->theme['whatsapp_url'] ?? null;

  $accent = $event->theme['accent'] ?? '#d9b06c';
  $accent2 = $event->theme['accent2'] ?? '#b58cff';

  $eventoISO = optional($event->starts_at)->toIso8601String();
@endphp

<div class="wrap">

  <div class="topbar">
    <div class="badge">
      <div class="dot"></div>
      <div class="small" id="inviteLine">Invitación personal</div>
    </div>
    <div class="small" id="invIdLine">ID: {{ $invite->id }}</div>
  </div>

  @php
    $status = $invite->status; // ACTIVE | CONFIRMED | DECLINED
  @endphp

  <div style="display:flex;justify-content:center;margin-bottom:10px">
    @if($status === 'CONFIRMED')
      <div class="statusBadge ok">✅ Asistencia confirmada</div>
    @elseif($status === 'DECLINED')
      <div class="statusBadge no">❌ No podrá asistir</div>
    @else
      <div class="statusBadge pending">⏳ Pendiente de confirmar</div>
    @endif
  </div>

  <section class="hero">
    <div class="heroInner">
      <div class="kicker">Mis XV Años</div>
      <h1 class="name" id="qName">{{ $quince }}</h1>
      @if($event->hero_image_path)
        <img class="heroPhoto" src="{{ asset('storage/'.$event->hero_image_path) }}" alt="Foto">
      @endif
      <p class="subtitle" id="qSubtitle">{{ $subtitle }}</p>

      @if($event->music_path)
        <div class="btnRow" style="justify-content:center; margin-top:10px;">
          <button class="btn" id="btnMusic">▶ Música{{ $event->music_title ? ': '.$event->music_title : '' }}</button>
        </div>
      @endif

      <div class="countdown" aria-label="Cuenta regresiva" style="justify-content:center;">
        <div class="pill" id="cdDays">0<span>días</span></div>
        <div class="pill" id="cdHours">0<span>horas</span></div>
        <div class="pill" id="cdMins">0<span>min</span></div>
      </div>

      <div class="btnRow" style="justify-content:center; margin-top:16px;">
        @if($mapsUrl)
          <div class="divider"></div>
          <h2 class="cardTitle">Ubicación</h2>

          <div style="border-radius:18px; overflow:hidden; border:1px solid rgba(255,255,255,.10); background:rgba(0,0,0,.12)">
            <iframe
              src="{{ $mapsUrl }}"
              width="100%"
              height="260"
              style="border:0; display:block"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        @endif
        <button class="btn" id="btnCalendar">Agregar a calendario</button>
      </div>

      <div class="loading" id="loadingLine">
        @if($invite->status !== 'ACTIVE')
          Confirmación registrada ✅
        @else
          Invitación validada ✓
        @endif
      </div>
    </div>
  </section>

  <div class="grid">

    <!-- IZQ -->
    <section class="card cardPad">
      <h2 class="cardTitle">Mensaje</h2>
      <p class="cardText" id="messageText">{{ $message }}</p>

      <div class="divider"></div>

      <h2 class="cardTitle">Datos del evento</h2>
      <div class="facts">
        <div class="fact">
          <div class="l">Fecha</div>
          <div class="v" id="evtDateText">{{ $dateText }}</div>
        </div>
        <div class="fact">
          <div class="l">Hora</div>
          <div class="v" id="evtTimeText">{{ $timeText }}</div>
        </div>
        <div class="fact">
          <div class="l">Lugar</div>
          <div class="v" id="evtPlaceText">{{ $venue }}</div>
        </div>
        <div class="fact">
          <div class="l">Vestimenta</div>
          <div class="v" id="evtDressText">{{ $dress }}</div>
        </div>
      </div>

      <div class="divider"></div>

      <h2 class="cardTitle">Regalos</h2>
      <p class="cardText" id="giftText">
        {{ $event->theme['gift_text'] ?? 'El mejor regalo es tu compañía. Si deseas obsequiarnos algo más, agradecemos lluvia de sobres.' }}
      </p>

      @if($event->photos && $event->photos->count())
        <section class="section alt reveal">
          <h2>📸 Fotos</h2>
          <p class="muted">Toca para ver la galería completa</p>

          <button id="openGalleryBtn" class="gallery-btn" type="button" style="--g1: {{ $accent }}; --g2: {{ $accent2 }};">
            <span class="gallery-btn__bg"
                  style="background-image:url('{{ asset('storage/'.$event->photos->first()->path) }}')"
                  aria-hidden="true"></span>

            <span class="gallery-btn__content">
              <span class="gallery-btn__title">Ver fotos</span>
              <span class="gallery-btn__meta">
                {{ $event->photos->count() }} recuerdos especiales
              </span>
            </span>
          </button>

          <div class="note">
            Desliza para cambiar de foto y toca fuera para cerrar.
          </div>

          <!-- Imágenes ocultas -->
          <div class="gallery-hidden" aria-hidden="true">
            @foreach($event->photos->take(12) as $p)
              <img src="{{ asset('storage/'.$p->path) }}"
                  alt="Foto"
                  loading="lazy"
                  class="gallery-item">
            @endforeach
          </div>
        </section>
        @endif

      @if($whatsUrl)
        <div class="btnRow">
          <button class="btn" id="btnWhats">Contactar por WhatsApp</button>
        </div>
      @endif
    </section>

    <!-- DER: RSVP -->
    @if($invite->status === 'ACTIVE')
      <aside class="card cardPad" id="rsvpCard">
        <h2 class="cardTitle">Confirmación de asistencia</h2>

        <p class="cardText" id="reservedNote">
          Hemos reservado <b id="reservedSeats">{{ $invite->seats }}</b> lugares especialmente para <b>{{ $invite->guest_name }}</b>.
          Agradecemos confirmar tu asistencia.
        </p>

        <div style="margin-top:12px;">
          <div class="small">Nombre del invitado</div>
          <input id="guestName" value="{{ $invite->guest_name }}" placeholder="Escribe tu nombre" autocomplete="name" />
        </div>

        <div class="btnRow">
          <button class="btn btnPrimary" id="btnYes" {{ $invite->status !== 'ACTIVE' ? 'disabled' : '' }}>Confirmo asistencia</button>
          <button class="btn btnDanger" id="btnNo" {{ $invite->status !== 'ACTIVE' ? 'disabled' : '' }}>No podré asistir</button>
        </div>

        <div class="status" id="statusBox"></div>

        <div class="divider"></div>

        <p class="small" style="margin:0;">
          Invitación personal e intransferible. Agradecemos respetar los lugares asignados.
        </p>
      </aside>
    @else
      <aside class="card cardPad">
        <h2 class="cardTitle">Confirmación</h2>
        <p class="cardText">
          @if($invite->status === 'CONFIRMED')
            Gracias por confirmar tu asistencia 💖
          @else
            Gracias por avisarnos. Te mandamos un abrazo ✨
          @endif
        </p>
      </aside>
    @endif  

  </div>

  <div class="footer" id="footerLine">
    Con cariño, <b id="qSignature">{{ $quince }}</b> ✨
  </div>

</div>
@endsection

@section('scripts')
<script>
  // Countdown (usa starts_at del evento)
  const EVENTO_ISO = @json($eventoISO);

  function startCountdown(){
    if(!EVENTO_ISO) return;

    const target = new Date(EVENTO_ISO).getTime();
    const cdDays = qs("cdDays");
    const cdHours = qs("cdHours");
    const cdMins = qs("cdMins");

    function tick(){
      const now = Date.now();
      let diff = Math.max(0, target - now);

      const days = Math.floor(diff / (1000*60*60*24));
      diff -= days * (1000*60*60*24);
      const hours = Math.floor(diff / (1000*60*60));
      diff -= hours * (1000*60*60);
      const mins = Math.floor(diff / (1000*60));

      cdDays.innerHTML = `${days}<span>días</span>`;
      cdHours.innerHTML = `${hours}<span>horas</span>`;
      cdMins.innerHTML = `${mins}<span>min</span>`;
    }
    tick();
    setInterval(tick, 1000);
  }

  startCountdown();

  (function(){
    const btn = document.getElementById('openGalleryBtn');
    if(!btn) return;

    const imgs = Array.from(document.querySelectorAll('.gallery-hidden img'));
    if(!imgs.length) return;

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

    const srcs = imgs.map(i => i.src);

    // 🎶 ducking música
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

      // baja volumen música
      if(audio){
        prevVolume = audio.volume;
        audio.volume = Math.max(0.12, prevVolume * 0.25);
      }
    }

    function close(){
      modal.classList.remove('open');
      document.body.style.overflow = '';

      // restaura volumen
      if(audio && prevVolume !== null){
        audio.volume = prevVolume;
        prevVolume = null;
      }
    }

    function go(dir){
      index = (index + dir + srcs.length) % srcs.length;
      setImage();
    }

    // abrir
    btn.addEventListener('click', ()=>openAt(0));

    // cerrar
    closeBtn.addEventListener('click', close);
    modal.addEventListener('click', (e)=>{
      if(e.target === modal) close();
    });

    // flechas
    prevBtn.addEventListener('click', ()=>go(-1));
    nextBtn.addEventListener('click', ()=>go(1));

    // teclado
    document.addEventListener('keydown', (e)=>{
      if(!modal.classList.contains('open')) return;
      if(e.key === 'Escape') close();
      if(e.key === 'ArrowLeft') go(-1);
      if(e.key === 'ArrowRight') go(1);
    });

    // swipe móvil
    let startX = 0;
    modal.addEventListener('touchstart', (e)=>{ startX = e.touches[0].clientX; }, {passive:true});
    modal.addEventListener('touchend', (e)=>{
      const dx = e.changedTouches[0].clientX - startX;
      if(Math.abs(dx) > 40){
        go(dx < 0 ? 1 : -1);
      }
    }, {passive:true});
  })();

  // Maps / Whats (si existen)
  const MAPS_URL = @json($mapsUrl);
  const WHATS_URL = @json($whatsUrl);

  if (MAPS_URL && qs('btnMaps')) qs('btnMaps').addEventListener('click', () => window.open(MAPS_URL, '_blank'));
  if (WHATS_URL && qs('btnWhats')) qs('btnWhats').addEventListener('click', () => window.open(WHATS_URL, '_blank'));

  // Calendar (ICS)
  const TITLE = @json($event->title ?? 'Evento');
  const LOCATION = @json($event->venue ?? '');
  if (qs('btnCalendar')) {
    qs('btnCalendar').addEventListener('click', () => {
      const start = EVENTO_ISO ? new Date(EVENTO_ISO) : new Date();
      const end = new Date(start.getTime() + 3 * 60 * 60 * 1000);

      const pad = (n) => String(n).padStart(2, "0");
      const fmt = (d) => (
        d.getUTCFullYear() +
        pad(d.getUTCMonth()+1) +
        pad(d.getUTCDate()) + "T" +
        pad(d.getUTCHours()) +
        pad(d.getUTCMinutes()) +
        pad(d.getUTCSeconds()) + "Z"
      );

      const ics =
`BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//PartyX Events//ES
BEGIN:VEVENT
UID:${(crypto.randomUUID ? crypto.randomUUID() : String(Date.now()))}
DTSTAMP:${fmt(new Date())}
DTSTART:${fmt(start)}
DTEND:${fmt(end)}
SUMMARY:${TITLE}
LOCATION:${LOCATION}
DESCRIPTION:Invitación digital - PartyX Events
END:VEVENT
END:VCALENDAR`;

      const blob = new Blob([ics], { type: "text/calendar;charset=utf-8" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "evento.ics";
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    });
  }
</script>

@if($event->music_path)
<script>
  const MUSIC_URL = @json(asset('storage/'.$event->music_path));
  let audio = new Audio(MUSIC_URL);
  audio.loop = true;

  window.__partyx_audio = audio;

  const btn = document.getElementById('btnMusic');
  if(btn){
    btn.addEventListener('click', async () => {
      try{
        if(audio.paused){
          await audio.play();
          btn.textContent = '⏸ Pausar{{ $event->music_title ? ': '.$event->music_title : '' }}';
        } else {
          audio.pause();
          btn.textContent = '▶ Música{{ $event->music_title ? ': '.$event->music_title : '' }}';
        }
      } catch(e){
        alert('Tu navegador bloqueó la reproducción automática. Presiona de nuevo.');
      }
    });
  }
</script>
@endif

@endsection
