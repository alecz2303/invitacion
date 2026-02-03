@extends('invite.templates.layout')

@section('title', ($event->title ?? 'Cumpleaños Infantil').' · Invitación')

@section('head')
{{-- Tip: si tu layout ya carga Tailwind, no necesitas nada más --}}
<link
  href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;700&family=Poppins:wght@300;400;600&display=swap"
  rel="stylesheet">

<style>
/* ====== TU CSS (PEGADO / INTEGRADO) ====== */
:root {
  --bg: #fff7fb;
  --alt: #f2fbff;
  --text: #222;
  --muted: #555;

  --accent: {{ $theme['accent'] ?? '#ff3b7a' }};
  --accent2: {{ $theme['accent2'] ?? '#00c2ff' }};

  --card: #ffffffcc;
  --shadow: 0 10px 30px rgba(0, 0, 0, .10);
  --radius: 22px;
}

* { box-sizing: border-box }
html { scroll-behavior: smooth }
body {
  margin: 0;
  font-family: "Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial;
  background: var(--bg);
  color: var(--text);
  overflow-x: hidden;
}
h1,h2 { font-family: "Baloo 2", cursive; margin: 0 }
h2 { font-size: 1.9rem }
p { margin: 10px 0 0 }
.muted { color: var(--muted) }

.section {
  padding: 64px 18px;
  text-align: center;
  max-width: 980px;
  margin: 0 auto;
  position: relative;
}
.section.alt { background: var(--alt); max-width: none }

/* ---------- HERO ---------- */
.hero {
  position: relative;
  min-height: 100vh;
  background: url("../assets/img/portada.jpg") center/cover no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 24px;
  overflow: hidden;
}
.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0, 0, 0, .50), rgba(0, 0, 0, .25), rgba(0, 0, 0, .60));
}
.hero-content {
  position: relative;
  color: #fff;
  max-width: 760px;
  animation: float 6s ease-in-out infinite;
}
.badge {
  display: inline-block;
  padding: 10px 14px;
  border-radius: 999px;
  background: linear-gradient(90deg, rgba(255, 59, 122, .35), rgba(0, 194, 255, .35), rgba(255, 209, 102, .35));
  backdrop-filter: blur(6px);
  border: 1px solid rgba(255, 255, 255, .35);
  margin-bottom: 14px;
  font-weight: 600;
}
.hero h1 {
  font-size: 3.2rem;
  line-height: 1.05;
  background: linear-gradient(90deg, #fff, #ffd166, #ff7a6b, #00c2ff, #fff);
  background-size: 300% 300%;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: shine 6s linear infinite;
  filter: drop-shadow(0 10px 18px rgba(0, 0, 0, .25));
}
.subtitle { font-size: 1.05rem; opacity: .95; margin-top: 10px }
.chips { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin: 18px 0 22px; }
.chip {
  background: rgba(255, 255, 255, .18);
  border: 1px solid rgba(255, 255, 255, .25);
  padding: 10px 12px;
  border-radius: 999px;
  font-size: .92rem;
  backdrop-filter: blur(6px);
}

.btn {
  display: inline-block;
  margin: 10px 8px 0;
  padding: 13px 18px;
  border-radius: 999px;
  text-decoration: none;
  font-weight: 600;
  transition: transform .08s ease, filter .2s ease;
}
.btn:active { transform: scale(.98) }
.btn:hover { filter: brightness(1.06) }
.btn.primary { background: linear-gradient(90deg, var(--accent), var(--accent2)); color: #fff; box-shadow: var(--shadow); }
.btn.ghost { background: rgba(255, 255, 255, .18); border: 1px solid rgba(255, 255, 255, .25); color: #fff; }
.btn.whatsapp { background: #1fbf5b; color: #fff; box-shadow: var(--shadow); animation: pulse 2.2s infinite; }

.scroll-hint {
  position: absolute;
  bottom: 18px;
  left: 0;
  right: 0;
  color: rgba(255, 255, 255, .85);
  font-size: .9rem;
  letter-spacing: .2px;
  animation: bob 1.8s ease-in-out infinite;
}

/* ---------- COUNTDOWN ---------- */
.countdown {
  display: grid;
  grid-template-columns: repeat(4, minmax(70px, 120px));
  gap: 12px;
  justify-content: center;
  margin-top: 22px;
}
.cd-box {
  background: var(--card);
  border-radius: 18px;
  box-shadow: var(--shadow);
  padding: 14px 10px;
  animation: pop 1.5s ease-in-out infinite alternate;
}
.cd-num { font-family: "Baloo 2", cursive; font-size: 2rem }
.cd-lbl { font-size: .85rem; color: var(--muted); margin-top: -6px }
.note {
  margin: 22px auto 0;
  max-width: 640px;
  background: rgba(255, 255, 255, .88);
  border: 1px solid rgba(0, 0, 0, .05);
  border-radius: 18px;
  padding: 14px 14px;
}

/* ---------- CARDS ---------- */
.card-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(180px, 1fr));
  gap: 14px;
  max-width: 920px;
  margin: 24px auto 0;
  padding: 0 10px;
}
.card {
  background: rgba(255, 255, 255, .75);
  border: 1px solid rgba(0, 0, 0, .06);
  border-radius: var(--radius);
  padding: 18px 14px;
  box-shadow: var(--shadow);
  transform: translateZ(0);
  transition: transform .25s ease;
}
.card:hover { transform: translateY(-6px) }
.card-title { color: var(--muted); font-size: .9rem }
.card-value { font-family: "Baloo 2", cursive; font-size: 1.4rem; margin-top: 4px }
.card-sub { color: var(--muted); font-size: .9rem }
.mini { margin-top: 16px; color: var(--muted); font-size: .95rem }

/* ---------- FOOTER ---------- */
.footer { padding: 30px 18px; text-align: center; background: #101010; color: #fff; }
.footer-inner { max-width: 980px; margin: 0 auto }
.small { font-size: .92rem }
.footer .muted { color: rgba(255, 255, 255, .7) }

/* ---------- MUSIC BUTTON ---------- */
.music-btn {
  position: fixed;
  top: 16px;
  right: 16px;
  width: 46px;
  height: 46px;
  border-radius: 50%;
  border: none;
  box-shadow: var(--shadow);
  background: rgba(0, 0, 0, .75);
  color: #fff;
  font-size: 18px;
  cursor: pointer;
  z-index: 50;
}

/* ---------- ENTRY SCREEN ---------- */
.enter-screen {
  position: fixed;
  inset: 0;
  background: linear-gradient(180deg, #ff3b7a, #00c2ff);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}
.enter-card {
  background: rgba(255, 255, 255, .92);
  border-radius: 24px;
  padding: 28px 22px;
  text-align: center;
  max-width: 340px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, .25);
  animation: pop 1.2s ease-in-out infinite alternate;
}
.enter-icon { font-size: 48px; margin-bottom: 10px }
.enter-card h2 { font-family: "Baloo 2", cursive; margin-bottom: 6px }
.enter-card p { color: #555; font-size: .95rem }
.enter-small { margin-top: 10px; font-size: .85rem }

/* ---------- CONFETTI ---------- */
.confetti { position: fixed; inset: 0; pointer-events: none; z-index: 3; }
.confetti::before, .confetti::after {
  content: "✨ 🎉 ⭐ 🎈 ✨ 🎶 ⭐ 🎉";
  position: absolute;
  top: -12%;
  left: 0;
  width: 100%;
  font-size: 26px;
  text-align: center;
  animation: confettiFall 10s linear infinite;
  opacity: .55;
  filter: blur(.2px);
}
.confetti::after { animation-delay: 5s; opacity: .45; }

/* ---------- BALLOONS ---------- */
.balloons { position: fixed; inset: 0; pointer-events: none; z-index: 2; }
.balloon {
  position: absolute;
  width: 54px;
  height: 68px;
  border-radius: 50% 50% 45% 45%;
  opacity: .75;
  filter: drop-shadow(0 10px 10px rgba(0, 0, 0, .12));
  animation: balloonUp 12s linear infinite;
}
.balloon::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: -14px;
  width: 2px;
  height: 44px;
  background: rgba(255, 255, 255, .65);
  transform: translateX(-50%);
  border-radius: 2px;
}
.b1{ left: 8%;  top: 110%; background: rgba(255, 59, 122, .75); animation-duration: 13s; }
.b2{ left: 22%; top: 120%; background: rgba(0, 194, 255, .70); animation-duration: 15s; }
.b3{ left: 40%; top: 115%; background: rgba(255, 209, 102, .75); animation-duration: 14s; }
.b4{ left: 60%; top: 130%; background: rgba(151, 255, 193, .70); animation-duration: 16s; }
.b5{ left: 78%; top: 125%; background: rgba(190, 139, 255, .70); animation-duration: 15s; }
.b6{ left: 90%; top: 118%; background: rgba(255, 255, 255, .70); animation-duration: 14s; }

/* ---------- REVEAL ON SCROLL ---------- */
.reveal { opacity: 0; transform: translateY(18px); transition: opacity .8s ease, transform .8s ease; }
.reveal.show { opacity: 1; transform: translateY(0); }

/* ---------- MODAL ---------- */
.modal { position: fixed; inset: 0; display: none; z-index: 1000; }
.modal.show { display: block; }
.modal-backdrop { position: absolute; inset: 0; background: rgba(0, 0, 0, .78); }
.modal-content {
  position: relative;
  width: min(92vw, 980px);
  height: min(82vh, 720px);
  margin: 7vh auto 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-content img {
  max-width: 100%;
  max-height: 100%;
  border-radius: 18px;
  box-shadow: 0 18px 50px rgba(0, 0, 0, .45);
  background: #111;
  object-fit: contain;
}
.modal-close {
  position: absolute;
  top: -10px;
  right: -10px;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, .92);
  cursor: pointer;
  font-size: 18px;
  box-shadow: var(--shadow);
}
.modal-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 46px;
  height: 46px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, .92);
  cursor: pointer;
  font-size: 28px;
  box-shadow: var(--shadow);
}
.modal-nav.left { left: -12px }
.modal-nav.right { right: -12px }
.modal-hint {
  position: absolute;
  bottom: -34px;
  left: 0;
  right: 0;
  text-align: center;
  font-size: .9rem;
  color: rgba(255, 255, 255, .75);
}

/* RSVP Form */
.rsvp-form {
  max-width: 520px;
  margin: 22px auto 0;
  background: rgba(255, 255, 255, .75);
  border: 1px solid rgba(0, 0, 0, .06);
  border-radius: 22px;
  padding: 18px 14px;
  box-shadow: var(--shadow);
}
.rsvp-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 10px 8px;
}
.rsvp-label { font-weight: 600; }
.rsvp-select {
  width: 120px;
  padding: 10px 12px;
  border-radius: 14px;
  border: 1px solid rgba(0, 0, 0, .10);
  background: rgba(255, 255, 255, .95);
  font-size: 1rem;
}

/* ===== PARALLAX ===== */
.parallax-section {
  position: relative;
  min-height: 78vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  overflow: hidden;
  max-width: none;
}
.px-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0, 0, 0, .62), rgba(0, 0, 0, .28), rgba(0, 0, 0, .62));
}
.px-content { position: relative; z-index: 2; width: min(980px, 92vw); color: #000; }
.muted-on-dark { color: rgba(255, 255, 255, .82); }
.mini-on-dark { color: rgba(255, 255, 255, .85); }

@media (max-width: 768px) {
  .parallax-section { min-height: 70vh; }
}

.gallery-hidden{ display:none; }

/* Botón premium */
.gallery-btn{
  position:relative;
  width:min(720px, 92vw);
  height:140px;
  margin:18px auto 0;
  border:none;
  border-radius:24px;
  overflow:hidden;
  cursor:pointer;
  padding:0;
  box-shadow: 0 18px 45px rgba(0,0,0,.14);
  transform: translateZ(0);
  background: #111;
}
.gallery-btn__bg{
  position:absolute;
  inset:-10px;
  background-size:cover;
  background-position:center;
  filter: blur(10px) saturate(1.2) brightness(.9);
  transform: scale(1.08);
}
.gallery-btn::after{
  content:"";
  position:absolute;
  inset:0;
  background: linear-gradient(90deg, rgba(255,59,122,.50), rgba(0,194,255,.35), rgba(0,0,0,.35));
}
.gallery-btn__content{
  position:relative;
  z-index:2;
  height:100%;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:6px;
  color:#fff;
  padding:14px;
}
.gallery-btn__title{
  font-family:"Baloo 2", cursive;
  font-size:1.8rem;
  line-height:1;
  text-shadow: 0 8px 18px rgba(0,0,0,.35);
}
.gallery-btn__meta{ font-size:1rem; color: rgba(255,255,255,.85); }
.gallery-btn:hover{ filter: brightness(1.05); }
@media (max-width:520px){
  .gallery-btn{ height:130px; }
  .gallery-btn__title{ font-size:1.55rem; }
}
.gallery-btn__title{
  position:relative;
  background: linear-gradient(90deg, #ffffff, #ffd166, #ffffff);
  background-size: 200% 100%;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: galleryShine 3s ease-in-out infinite;
}
@keyframes galleryShine{
  0%{ background-position: 0% 50%; }
  50%{ background-position: 100% 50%; }
  100%{ background-position: 0% 50%; }
}

/* HOTFIX H2 BADGE */
.section h2{
  display:inline-block !important;
  padding:10px 18px !important;
  border-radius:999px !important;
  margin-bottom:16px !important;
  font-family:"Baloo 2", cursive !important;
  font-size:1.6rem !important;
  font-weight:700 !important;
  background:linear-gradient(90deg, rgba(255,59,122,.38), rgba(0,194,255,.38), rgba(255,209,102,.38)) !important;
  color:#fff !important;
  backdrop-filter: blur(6px);
  border:1px solid rgba(255,255,255,.38) !important;
  box-shadow:0 12px 28px rgba(0,0,0,.22) !important;
  animation: badgeFloat 4.5s ease-in-out infinite !important;
}
@keyframes badgeFloat{ 0%{transform:translateY(0);} 50%{transform:translateY(-4px);} 100%{transform:translateY(0);} }
.parallax-section .px-content{ color:#fff !important; }
.parallax-section h2{
  background:linear-gradient(90deg, rgba(255,59,122,.45), rgba(0,194,255,.45), rgba(255,209,102,.45)) !important;
  border:1px solid rgba(255,255,255,.40) !important;
  box-shadow:0 14px 32px rgba(0,0,0,.35) !important;
}
.parallax-section h2, .section.alt h2{ isolation:isolate; }

/* Hotfix legibilidad en parallax */
.parallax-section .px-content{ color:#fff !important; }
.parallax-section .countdown .cd-num{ color:#111 !important; -webkit-text-fill-color:#111 !important; }
.parallax-section .countdown .cd-lbl{ color:#333 !important; }
.parallax-section .note{ color:#111 !important; }
.parallax-section .card, .parallax-section .rsvp-form{ color:#111 !important; }
.parallax-section .card-title, .parallax-section .card-sub, .parallax-section .mini{ color:#333 !important; }
.parallax-section .muted, .parallax-section .muted-on-dark{ color:rgba(255,255,255,.85) !important; }
.parallax-section .card .muted, .parallax-section .note .muted{ color:#333 !important; }

/* Animations */
@keyframes float{ 0%{transform:translateY(0)} 50%{transform:translateY(-12px)} 100%{transform:translateY(0)} }
@keyframes shine{ 0%{background-position:0% 50%} 100%{background-position:100% 50%} }
@keyframes pulse{ 0%{box-shadow:0 0 0 0 rgba(31,191,91,.6)} 70%{box-shadow:0 0 0 18px rgba(31,191,91,0)} 100%{box-shadow:0 0 0 0 rgba(31,191,91,0)} }
@keyframes pop{ from{transform:scale(1)} to{transform:scale(1.05)} }
@keyframes bob{ 0%{transform:translateY(0)} 50%{transform:translateY(-7px)} 100%{transform:translateY(0)} }
@keyframes confettiFall{ 0%{transform:translateY(0)} 100%{transform:translateY(120vh)} }
@keyframes balloonUp{ 0%{transform:translateY(0) translateX(0)} 50%{transform:translateY(-70vh) translateX(10px)} 100%{transform:translateY(-140vh) translateX(-8px)} }

/* Responsive */
@media (max-width:820px){
  .hero h1{ font-size:2.6rem }
  .countdown{ grid-template-columns: repeat(2, minmax(120px, 1fr)); max-width:420px; margin-left:auto; margin-right:auto; }
  .card-grid{ grid-template-columns: 1fr; max-width:520px }
  .modal-nav.left{ left:6px }
  .modal-nav.right{ right:6px }
  .modal-close{ right:6px }
}

/* =====================================================
   PARALLAX IMAGEN COMPLETA + FONDO BLUR (PREMIUM)
   Pegar al FINAL del CSS
===================================================== */

/* 0) Apagamos el background directo del section (lo movemos a pseudo-elementos) */
.parallax-section{
  background-image: none !important;
  background-color: #0b0b0b; /* fallback */
}

/* 1) Capa de relleno: cover + blur (para que se vea “llenito”) */
.parallax-section::before{
  content:"";
  position:absolute;
  inset:0;
  z-index:0;

  background-image: var(--px);
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center calc(50% + var(--pxy, 0px));

  filter: blur(18px) saturate(1.25) brightness(.72);
  transform: scale(1.12);
}

/* 2) Capa principal: contain (aquí se ve COMPLETA la imagen) */
.parallax-section::after{
  content:"";
  position:absolute;
  inset:0;
  z-index:1;

  background-image: var(--px);
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center calc(50% + var(--pxy, 0px));
}

/* 3) Asegurar orden de capas: blur (0), imagen (1), overlay (2), contenido (3) */
.parallax-section .px-overlay{ z-index:2; }
.parallax-section .px-content{ z-index:3; }

</style>
@endsection

@section('content')
@php
  $asMediaUrl = function($val){
    if(!$val) return null;
    if(is_string($val) && preg_match('/^https?:\/\//i', $val)) return $val;
    return asset('storage/'.$val);
  };

  $title = $event->title ?? 'Cumpleaños Infantil';

  // THEME (nuevo config/cumple.php)
  $badgeText     = $theme['badge_text'] ?? '🎉 ¡Estás invitado!';
  $subtitle      = $theme['subtitle'] ?? 'Una fiesta llena de alegría y diversión';
  $noteCountdown = $theme['note_countdown'] ?? '✨ Ven con ganas de celebrar, abrazar y tomarte fotos bonitas.';
  $detailsNote   = $theme['details_note'] ?? 'Si puedes, llega puntual para disfrutar todo desde el inicio 🥳';
  $galleryHint   = $theme['gallery_hint'] ?? 'Desliza para cambiar de foto y toca fuera para cerrar.';
  $mapsHint      = $theme['maps_hint'] ?? 'Toca el botón para abrir Google Maps';
  $footerText    = $theme['footer_text'] ?? 'Hecho con 💛';

  $waTpl = $theme['whatsapp_message_template'] ??
    "🎉 ¡Hola! Confirmo mi asistencia al evento el {DATE} a las {TIME} en {VENUE}. 💛\n\nAsistimos: {ADULTS} adulto(s) y {KIDS} niño(s).";

  $eventoISO = optional($event->starts_at)->toIso8601String();

  $datePretty = optional($event->starts_at)->locale('es')->translatedFormat('j \\d\\e F \\d\\e Y');
  $yearPretty = optional($event->starts_at)->format('Y');
  $timeText = optional($event->starts_at)->format('g:i A');

  $venue = $event->venue ?? 'Nombre del salón o domicilio';
  $city  = $event->city ?? ($theme['city'] ?? 'Tuxtla Gtz.');
  $mapsUrl = $event->maps_url ?? ($theme['maps_url'] ?? null);

  $addressShort = $event->address_short ?? ($theme['address_short'] ?? $venue);

  $heroFromTheme = $theme['hero_image_url'] ?? null;
  $heroFromEvent = $event->hero_image_path ? asset('storage/'.$event->hero_image_path) : null;
  $heroImg = $heroFromTheme ?: $heroFromEvent;

  // Parallax: tratar como image (tema) + fallback a tu ruta
  $pxBase = asset('storage/templates/cumple/img/parallax');
  $pxCountdown = $asMediaUrl($theme['px_countdown_url'] ?? null) ?: ($pxBase.'/countdown.png');
  $pxDetalles  = $asMediaUrl($theme['px_detalles_url'] ?? null)  ?: ($pxBase.'/detalles.png');
  $pxUbicacion = $asMediaUrl($theme['px_ubicacion_url'] ?? null) ?: ($pxBase.'/ubicacion.png');

  $chips = $theme['chips'] ?? null;
  if(is_string($chips)){
    $tmp = json_decode($chips, true);
    if(json_last_error()===JSON_ERROR_NONE) $chips = $tmp;
  }
  if(!is_array($chips)){
    $chips = [
      '🎪 Fiesta Plim Plim',
      '🎈 Globos & confeti',
      '📍 '.$city,
    ];
  }

  // Música: soporta url o path
  $musicUrl = $theme['music_url'] ?? ($event->music_url ?? null);
  $musicUrl = $asMediaUrl($musicUrl) ?? asset('storage/templates/cumple/music/musica.mp3');

  // WhatsApp (nuevo config): número solo del theme
  $waNumber = preg_replace('/\D+/', '', (string)($theme['whatsapp_number'] ?? ''));

  // Galería (hasta 12)
  $galleryPhotos = [];
  if($event->photos && $event->photos->count()){
    foreach($event->photos->take(12) as $p){
      $galleryPhotos[] = asset('storage/'.$p->path);
    }
  }
@endphp

{{-- Enter Screen --}}
<div id="enterScreen" class="enter-screen">
  <div class="enter-card">
    <div class="enter-icon">🎵</div>
    <h2>¡Bienvenido!</h2>
    <p>Toca para entrar y disfrutar la invitación</p>
    <button id="enterBtn" class="btn primary">Entrar</button>
    <div class="enter-small muted">Tip: sube el volumen del cel 🔊</div>
  </div>
</div>

{{-- Música --}}
<audio id="music" loop preload="none">
  <source src="{{ $musicUrl }}" type="audio/mpeg">
</audio>
<button id="musicBtn" class="music-btn" aria-label="Música">🔇</button>

{{-- Efectos --}}
<div class="balloons" aria-hidden="true">
  <span class="balloon b1"></span>
  <span class="balloon b2"></span>
  <span class="balloon b3"></span>
  <span class="balloon b4"></span>
  <span class="balloon b5"></span>
  <span class="balloon b6"></span>
</div>
<div class="confetti" aria-hidden="true"></div>

{{-- HERO --}}
<header class="hero" style="{{ $heroImg ? "background-image:url('".$heroImg."')" : '' }}">
  <div class="hero-overlay"></div>

  <div class="hero-content">
    <div class="badge">{{ $badgeText }}</div>
    <h1>{{ $title }}</h1>
    <p class="subtitle">{{ $subtitle }}</p>

    <div class="chips">
      @foreach($chips as $c)
        <span class="chip">{{ $c }}</span>
      @endforeach
    </div>

    <a class="btn primary" href="#rsvp">Confirmar asistencia</a>
    <a class="btn ghost" href="#detalles">Ver detalles</a>
  </div>

  <div class="scroll-hint">Desliza ↓</div>
</header>

{{-- COUNTDOWN (Parallax) --}}
<section class="section parallax-section parallax-countdown reveal" style="--px: url('{{$pxCountdown}}')">
  <div class="px-overlay"></div>

  <div class="px-content">
    <h2>⏳ Cuenta regresiva</h2>
    <p class="muted muted-on-dark">Muy pronto celebraremos un día muy especial</p>

    <div class="countdown" id="countdown">
      <div class="cd-box"><div class="cd-num" id="d">--</div><div class="cd-lbl">Días</div></div>
      <div class="cd-box"><div class="cd-num" id="h">--</div><div class="cd-lbl">Horas</div></div>
      <div class="cd-box"><div class="cd-num" id="m">--</div><div class="cd-lbl">Min</div></div>
      <div class="cd-box"><div class="cd-num" id="s">--</div><div class="cd-lbl">Seg</div></div>
    </div>

    <div class="note">
      {{ $noteCountdown }}
    </div>
  </div>
</section>

{{-- DETALLES (Parallax) --}}
<section class="section parallax-section parallax-detalles reveal" id="detalles" style="--px: url('{{$pxDetalles}}')">
  <div class="px-overlay"></div>

  <div class="px-content">
    <h2>📅 Detalles del evento</h2>

    <div class="card-grid">
      <div class="card">
        <div class="card-title">Fecha</div>
        <div class="card-value">{{ optional($event->starts_at)->locale('es')->translatedFormat('j \\d\\e F') ?? '—' }}</div>
        <div class="card-sub">{{ $yearPretty ?? '—' }}</div>
      </div>

      <div class="card">
        <div class="card-title">Hora</div>
        <div class="card-value">{{ $timeText ?? '—' }}</div>
        <div class="card-sub">Hora de México</div>
      </div>

      <div class="card">
        <div class="card-title">Lugar</div>
        <div class="card-value">{{ $venue }}</div>
        <div class="card-sub">{{ $city }}</div>
      </div>
    </div>

    <p class="muted muted-on-dark" style="margin-top:18px;">
      {{ $detailsNote }}
    </p>
  </div>
</section>

{{-- UBICACIÓN (Parallax) --}}
<section class="section parallax-section parallax-ubicacion reveal" style="--px: url('{{$pxUbicacion}}')">
  <div class="px-overlay"></div>

  <div class="px-content">
    <h2>📍 Ubicación</h2>
    <p class="muted muted-on-dark">{{ $mapsHint }}</p>

    @if($mapsUrl)
      <a class="btn primary" target="_blank" rel="noopener" href="{{ $mapsUrl }}">
        Abrir en Maps
      </a>
    @endif

    <div class="mini mini-on-dark">
      <strong>Dirección:</strong> {{ $addressShort }}
    </div>
  </div>
</section>

{{-- GALERÍA --}}
@if(!empty($galleryPhotos))
<section class="section alt reveal">
  <h2>📸 Fotos</h2>
  <p class="muted">Toca para ver la galería completa</p>

  <button id="openGalleryBtn" class="gallery-btn" type="button">
    <span class="gallery-btn__bg" id="galleryBtnBg" aria-hidden="true"></span>

    <span class="gallery-btn__content">
      <span class="gallery-btn__title" id="galleryBtnTitle">Ver fotos</span>
      <span class="gallery-btn__meta" id="galleryBtnMeta">Cargando…</span>
    </span>
  </button>

  <div class="note">
    {{ $galleryHint }}
  </div>

  <div class="gallery-hidden" aria-hidden="true">
    @foreach($galleryPhotos as $idx => $u)
      <img src="{{ $u }}" alt="Foto {{ $idx+1 }}" loading="lazy" class="gallery-item">
    @endforeach
  </div>
</section>
@endif

{{-- RSVP --}}
<section class="section reveal" id="rsvp">
  <h2>✅ Confirma tu asistencia</h2>
  <p class="muted">Selecciona cuántos adultos y niños asistirán, y confirma por WhatsApp</p>

  <div class="rsvp-form">
    <div class="rsvp-row">
      <label class="rsvp-label" for="adults">Adultos</label>
      <select id="adults" class="rsvp-select">
        @for($i=0; $i<=10; $i++)
          <option value="{{ $i }}" @selected($i===0)>{{ $i }}</option>
        @endfor
      </select>
    </div>

    <div class="rsvp-row">
      <label class="rsvp-label" for="kids">Niños</label>
      <select id="kids" class="rsvp-select">
        @for($i=0; $i<=10; $i++)
          <option value="{{ $i }}" @selected($i===0)>{{ $i }}</option>
        @endfor
      </select>
    </div>

    <button id="rsvpBtn" class="btn whatsapp" type="button">
      Confirmar por WhatsApp 💬
    </button>

    <div class="note">
      Tip: Si todavía no sabes, deja 0 y 0 y manda el mensaje igual 😉
    </div>
  </div>
</section>

<footer class="footer">
  <div class="footer-inner">
    <div class="small">{{ $footerText }}</div>
  </div>
</footer>

{{-- MODAL --}}
@if(!empty($galleryPhotos))
<div id="modal" class="modal" aria-hidden="true">
  <div class="modal-backdrop" id="modalBackdrop"></div>
  <div class="modal-content" role="dialog" aria-modal="true" aria-label="Vista de foto">
    <button class="modal-close" id="modalClose" aria-label="Cerrar">✕</button>
    <button class="modal-nav left" id="modalPrev" aria-label="Anterior">‹</button>
    <img id="modalImg" src="" alt="Foto ampliada">
    <button class="modal-nav right" id="modalNext" aria-label="Siguiente">›</button>
    <div class="modal-hint muted">{{ $galleryHint }}</div>
  </div>
</div>
@endif

@endsection

@section('scripts')
<script>
  // ====== CONFIG ======
  const EVENT_DATE = new Date(@json(optional($event->starts_at)->format('Y-m-d\TH:i:sP') ?? '2026-02-15T14:00:00-06:00'));

  // ====== COUNTDOWN ======
  const dEl = document.getElementById("d");
  const hEl = document.getElementById("h");
  const mEl = document.getElementById("m");
  const sEl = document.getElementById("s");

  function tick() {
    const now = new Date();
    const diff = EVENT_DATE.getTime() - now.getTime();

    if (diff <= 0) {
      dEl.textContent = "0";
      hEl.textContent = "00";
      mEl.textContent = "00";
      sEl.textContent = "00";
      return;
    }

    const totalSeconds = Math.floor(diff / 1000);
    const days = Math.floor(totalSeconds / (3600 * 24));
    const hours = Math.floor((totalSeconds % (3600 * 24)) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    dEl.textContent = days;
    hEl.textContent = String(hours).padStart(2, "0");
    mEl.textContent = String(minutes).padStart(2, "0");
    sEl.textContent = String(seconds).padStart(2, "0");
  }
  tick();
  setInterval(tick, 1000);

  // ====== MÚSICA (entrada) ======
  const music = document.getElementById("music");
  const musicBtn = document.getElementById("musicBtn");
  const enterScreen = document.getElementById("enterScreen");
  const enterBtn = document.getElementById("enterBtn");

  music.volume = 0;

  function setIcon() {
    musicBtn.textContent = music.paused ? "🔇" : "🔊";
  }

  async function fadeInMusic() {
    try {
      await music.play();
      let v = 0;
      const fade = setInterval(() => {
        v += 0.05;
        music.volume = Math.min(v, 1);
        if (v >= 1) clearInterval(fade);
      }, 120);
    } catch (e) { }
  }

  enterBtn.addEventListener("click", async () => {
    await fadeInMusic();
    enterScreen.style.display = "none";
    setIcon();
  });

  musicBtn.addEventListener("click", () => {
    if (music.paused) {
      fadeInMusic();
    } else {
      music.pause();
    }
    setIcon();
  });
  setIcon();

  // ====== REVEAL ON SCROLL ======
  const revealEls = document.querySelectorAll(".reveal");
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) {
        e.target.classList.add("show");
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.14 });
  revealEls.forEach(el => io.observe(el));

  // ====== MODAL GALERÍA ======
  const modal = document.getElementById("modal");
  const modalImg = document.getElementById("modalImg");
  const modalClose = document.getElementById("modalClose");
  const modalBackdrop = document.getElementById("modalBackdrop");
  const modalPrev = document.getElementById("modalPrev");
  const modalNext = document.getElementById("modalNext");

  const galleryItems = Array.from(document.querySelectorAll(".gallery-item"));
  let currentIndex = 0;

  function openModal(index) {
    currentIndex = index;
    modal.classList.add("show");
    modal.setAttribute("aria-hidden", "false");
    modalImg.src = galleryItems[currentIndex].src;
    document.body.style.overflow = "hidden";
  }

  function closeModal() {
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
    modalImg.src = "";
    document.body.style.overflow = "";
  }

  function showPrev() {
    currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
    modalImg.src = galleryItems[currentIndex].src;
  }

  function showNext() {
    currentIndex = (currentIndex + 1) % galleryItems.length;
    modalImg.src = galleryItems[currentIndex].src;
  }

  galleryItems.forEach((img, idx) => {
    img.addEventListener("click", () => openModal(idx));
  });

  modalClose?.addEventListener("click", closeModal);
  modalBackdrop?.addEventListener("click", closeModal);
  modalPrev?.addEventListener("click", showPrev);
  modalNext?.addEventListener("click", showNext);

  // Teclas
  document.addEventListener("keydown", (e) => {
    if (!modal.classList.contains("show")) return;
    if (e.key === "Escape") closeModal();
    if (e.key === "ArrowLeft") showPrev();
    if (e.key === "ArrowRight") showNext();
  });

  // Swipe
  let touchStartX = 0;
  let touchEndX = 0;

  modalImg?.addEventListener("touchstart", (e) => {
    touchStartX = e.changedTouches[0].screenX;
  }, { passive: true });

  modalImg?.addEventListener("touchend", (e) => {
    touchEndX = e.changedTouches[0].screenX;
    const dx = touchEndX - touchStartX;

    if (Math.abs(dx) < 40) return;
    if (dx > 0) showPrev();
    else showNext();
  }, { passive: true });

  // ====== RSVP WhatsApp ======
  const rsvpBtn = document.getElementById("rsvpBtn");
  const adultsSel = document.getElementById("adults");
  const kidsSel = document.getElementById("kids");
  const WHATSAPP_NUMBER = @json($waNumber);

  rsvpBtn?.addEventListener("click", () => {
    const adults = adultsSel.value;
    const kids = kidsSel.value;

    const tpl = @json($waTpl);
    const msg = String(tpl || "")
      .replaceAll("{DATE}", @json($datePretty ?? ""))
      .replaceAll("{TIME}", @json($timeText ?? ""))
      .replaceAll("{VENUE}", @json($venue ?? ""))
      .replaceAll("{ADULTS}", adults)
      .replaceAll("{KIDS}", kids);

    const url = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`;
    window.open(url, "_blank");
  });

  // ===== PARALLAX =====
  const pxSections = document.querySelectorAll(".parallax-section");
  let ticking = false;

  function parallaxTick() {
    const y = window.scrollY;

    pxSections.forEach((sec) => {
      const rect = sec.getBoundingClientRect();
      const top = rect.top + y;
      const height = rect.height;

      const progress = (y - top + window.innerHeight) / (height + window.innerHeight);
      const clamped = Math.max(0, Math.min(1, progress));

      const offset = (clamped - 0.5) * 50;
      sec.style.setProperty("--pxy", `${offset}px`);
    });

    ticking = false;
  }

  window.addEventListener("scroll", () => {
    if (!ticking) {
      window.requestAnimationFrame(parallaxTick);
      ticking = true;
    }
  });

  window.addEventListener("resize", parallaxTick);
  parallaxTick();

  // ====== BOTÓN GALERÍA (preview + contador + autoplay) ======
  const openGalleryBtn = document.getElementById("openGalleryBtn");
  const galleryBtnBg = document.getElementById("galleryBtnBg");
  const galleryBtnTitle = document.getElementById("galleryBtnTitle");
  const galleryBtnMeta = document.getElementById("galleryBtnMeta");

  const totalPhotos = galleryItems.length;

  const AUTO_PLAY = true;
  const AUTO_PLAY_MS = 2500;
  let autoplayTimer = null;

  function startAutoplay(){
    if(!AUTO_PLAY) return;
    stopAutoplay();
    autoplayTimer = setInterval(() => {
      if(modal.classList.contains("show")) showNext();
    }, AUTO_PLAY_MS);
  }

  function stopAutoplay(){
    if(autoplayTimer){
      clearInterval(autoplayTimer);
      autoplayTimer = null;
    }
  }

  function setupGalleryButton(){
    if(!openGalleryBtn) return;

    if(totalPhotos > 0 && galleryBtnBg){
      const previewSrc = galleryItems[0].getAttribute("src");
      galleryBtnBg.style.backgroundImage = `url('${previewSrc}')`;
    }

    if(galleryBtnTitle) galleryBtnTitle.textContent = "Ver fotos";
    if(galleryBtnMeta){
      galleryBtnMeta.textContent = `${totalPhotos} foto${totalPhotos === 1 ? "" : "s"}`;
    }

    openGalleryBtn.addEventListener("click", () => {
      openModal(0);
      startAutoplay();
    });
  }
  setupGalleryButton();

  ["click", "touchstart"].forEach(evt => {
    modal?.addEventListener(evt, () => stopAutoplay(), { passive:true });
  });

  // Hook close prev next para pausar autoplay
  const _closeModalOriginal = closeModal;
  closeModal = function(){
    stopAutoplay();
    _closeModalOriginal();
  };

  const _showPrevOriginal = showPrev;
  showPrev = function(){
    stopAutoplay();
    _showPrevOriginal();
  };

  const _showNextOriginal = showNext;
  showNext = function(){
    stopAutoplay();
    _showNextOriginal();
  };
</script>
@endsection