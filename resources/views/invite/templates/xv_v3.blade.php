@extends('invite.templates.layout')

@section('title', ($event->title ?? 'Mis XV Años').' · Invitación')

@section('head')
<style>
  :root{
    --paper:#fbfaf7;
    --card:#ffffff;
    --ink:#2a2a2a;
    --muted:#6f6f6f;

    --accent: {{ $event->theme['accent'] ?? '#b59b6a' }};   /* dorado */
    --accent2: {{ $event->theme['accent2'] ?? '#3e6b5b' }}; /* verde */

    --radius: 22px;
    --shadow: 0 18px 46px rgba(0,0,0,.10);
    --line: rgba(0,0,0,.10);
    --max: 980px;
  }

  *{ box-sizing:border-box; }
  html{ scroll-behavior:smooth; }
  body{
    margin:0;
    font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
    color:var(--ink);
    background: var(--paper);
  }
  a{ color:inherit; text-decoration:none; }

  .wrap{ max-width:var(--max); margin:0 auto; padding:18px; }

  /* ===== Contenedor tipo “hoja” ===== */
  .page{
    position:relative;
    border-radius: 26px;
    background: linear-gradient(180deg, #ffffff, #fbfaf7);
    border: 1px solid rgba(0,0,0,.12);
    box-shadow: var(--shadow);
    overflow:hidden;
  }
  .frame{
    position:absolute;
    inset: 14px;
    border: 2px solid rgba(181,155,106,.35);
    border-radius: 18px;
    pointer-events:none;
    z-index: 3;
  }

  /* ===== Flores decorativas (esquinas) ===== */
  .decor{
    position:absolute;
    pointer-events:none;
    user-select:none;
    width:min(280px, 58vw);
    top:-12px;
    filter: drop-shadow(0 18px 24px rgba(0,0,0,.10));
    opacity:.98;
    z-index: 5;
  }
  .decor.lt{ left:-12px; }
  .decor.rt{ right:-12px; transform: scaleX(-1); }
  .decor.lb{
    left:-12px; bottom:-16px; top:auto;
    transform: rotate(2deg);
    opacity:.92;
    z-index: 2;
  }
  .decor.rb{
    right:-12px; bottom:-16px; top:auto;
    transform: rotate(-2deg) scaleX(-1);
    opacity:.92;
    z-index: 2;
  }

  /* ===== HERO ===== */
  .hero{
    position:relative;
    min-height: 78vh;
    display:flex;
    align-items:flex-end;
    justify-content:center;
    padding: 22px 18px;
    overflow:hidden;
    border-bottom: 1px solid rgba(0,0,0,.10);
    background: #fff;
  }
  .heroBg{
    position:absolute;
    inset:0;
    background-size: cover;
    background-position: center;
    transform: scale(1.03);
    /* ✅ más viva y con contraste */
    filter: saturate(1.08) contrast(1.10);
  }

  /* ✅ Overlay ajustado para NO lavar la foto */
  .heroOverlay{
    position:absolute;
    inset:0;
    background:
      radial-gradient(900px 620px at 22% 12%, rgba(255,255,255,.22), rgba(255,255,255,0) 55%),
      linear-gradient(to top,
        rgba(255,255,255,.92) 0%,
        rgba(255,255,255,.30) 42%,
        rgba(255,255,255,.08) 70%,
        rgba(255,255,255,0) 100%
      );
  }

  .heroInner{
    position:relative;
    z-index: 6;
    width: min(880px, 100%);
    text-align:center;
    padding: 14px 12px 6px;
  }
  .heroCard{
    display:inline-block;
    width:min(760px, 100%);
    border-radius: 24px;
    background: rgba(255,255,255,.78);
    border: 1px solid rgba(0,0,0,.10);
    box-shadow: 0 18px 36px rgba(0,0,0,.08);
    backdrop-filter: blur(10px);
    padding: 14px 14px 12px;
  }
  .heroGuest{
    font-weight:950;
    color: color-mix(in oklab, var(--accent2) 65%, #000);
    letter-spacing:.06em;
    font-size:.92rem;
  }
  .heroSub{
    margin: 8px auto 0;
    max-width: 62ch;
    color: rgba(0,0,0,.62);
    font-weight:800;
    line-height:1.65;
  }

  /* ===== Secciones ===== */
  .sec{
    position:relative;
    padding: 22px 18px;
    text-align:center;
    z-index: 4;
  }

  .kicker{
    letter-spacing:.28em;
    font-weight:900;
    color: color-mix(in oklab, var(--accent2) 65%, #000);
    font-size:.88rem;
  }

  .title{
    margin:10px 0 0;
    font-weight:950;
    font-size: clamp(2.2rem, 6vw, 3.0rem);
    line-height:1.02;
  }
  .sub{
    margin: 10px auto 0;
    max-width: 62ch;
    color: var(--muted);
    font-weight:700;
    line-height:1.75;
  }

  /* Nombre como imagen */
  .nameImg{
    margin: 12px auto 0;
    max-width:min(640px, 92%);
    height:auto;
    display:block;
  }

  .dividerImg{
    display:block;
    width:min(520px, 90%);
    margin: 10px auto 0;
    opacity:.95;
  }

  /* Botones */
  .btnRow{ margin-top:14px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
  .btn{
    display:inline-flex; align-items:center; justify-content:center;
    padding: 12px 18px;
    border-radius: 999px;
    font-weight:950;
    border: 1px solid rgba(0,0,0,.14);
    background:#fff;
    cursor:pointer;
    transition: transform .12s ease, box-shadow .12s ease;
    user-select:none;
  }
  .btn:hover{ transform: translateY(-1px); box-shadow: 0 12px 24px rgba(0,0,0,.10); }
  .btn.gold{
    border-color: color-mix(in oklab, var(--accent) 55%, rgba(0,0,0,.12));
    background: linear-gradient(180deg, color-mix(in oklab, var(--accent) 30%, #fff), #fff);
  }

  /* Padres / padrinos */
  .grid2{
    margin-top:14px;
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:12px;
    max-width: 860px;
    margin-left:auto;
    margin-right:auto;
  }
  @media (max-width:860px){ .grid2{ grid-template-columns:1fr; } }

  .box{
    border:1px solid var(--line);
    border-radius: 18px;
    background: var(--card);
    padding: 14px 14px;
    text-align:left;
  }
  .box .lbl{
    font-weight:950;
    color: color-mix(in oklab, var(--accent2) 65%, #000);
    margin-bottom:6px;
  }
  .box .val{
    font-weight:800;
    color:#2d2d2d;
    line-height:1.65;
    white-space:pre-line;
  }

  /* Fecha / countdown */
  .dateBadge{
    width: 120px;
    height: 120px;
    margin: 10px auto 8px;
    border-radius: 24px;
    border:1px solid rgba(0,0,0,.12);
    background:#fff;
    display:grid;
    place-items:center;
    box-shadow: 0 18px 30px rgba(0,0,0,.08);
  }
  .dateBadge .d{ font-weight:950; font-size:2.2rem; line-height:1; }
  .dateBadge .m{ font-weight:950; letter-spacing:.18em; color: color-mix(in oklab, var(--accent2) 65%, #000); }
  .dateBadge .y{ font-weight:900; color: var(--muted); }

  .countdown{
    margin-top:12px;
    display:flex;
    gap:10px;
    justify-content:center;
    flex-wrap:wrap;
  }
  .cd{
    width: 96px;
    border:1px solid var(--line);
    border-radius: 18px;
    background:#fff;
    padding: 12px 10px;
  }
  .cd .n{ font-weight:950; font-size:1.7rem; line-height:1; }
  .cd .l{ margin-top:6px; font-weight:900; color: var(--muted); font-size:.9rem; }

  /* Cards misa/recepción */
  .placeGrid{
    margin-top:14px;
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:12px;
    max-width: 900px;
    margin-left:auto;
    margin-right:auto;
  }
  @media (max-width:860px){ .placeGrid{ grid-template-columns:1fr; } }

  .placeCard{
    border:1px solid var(--line);
    border-radius: 18px;
    overflow:hidden;
    background:#fff;
    text-align:left;
  }
  .placePhoto{
    height: 170px;
    background-size:cover;
    background-position:center;
  }
  .placeBody{ padding: 14px; }
  .tag{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 12px;
    border-radius:999px;
    border:1px solid rgba(0,0,0,.12);
    font-weight:950;
    font-size:.9rem;
    color: color-mix(in oklab, var(--accent2) 65%, #000);
    background:#fff;
  }
  .placeTitle{ margin:10px 0 4px; font-weight:950; font-size:1.1rem; }
  .addr{ color: var(--muted); font-weight:800; line-height:1.6; }
  .ptime{ margin-top:10px; font-weight:950; letter-spacing:.14em; }

  /* Itinerario */
  .tl{
    margin-top:14px;
    display:grid;
    gap:12px;
    max-width: 860px;
    margin-left:auto;
    margin-right:auto;
  }
  .tlItem{
    display:flex;
    gap:12px;
    align-items:center;
    border:1px solid var(--line);
    border-radius: 18px;
    background:#fff;
    padding: 12px;
    text-align:left;
  }
  .tlIcon{
    width:52px; height:52px;
    border-radius: 16px;
    border:1px solid rgba(0,0,0,.12);
    background: linear-gradient(180deg, color-mix(in oklab, var(--accent) 18%, #fff), #fff);
    display:grid;
    place-items:center;
    overflow:hidden;
    flex:0 0 auto;
  }
  .tlIcon img{ width:32px; height:32px; object-fit:contain; display:block; }
  .tlMain{ flex:1; }
  .tlName{ font-weight:950; }
  .tlTime{ font-weight:950; color: var(--muted); }
  .tlTop{ display:flex; justify-content:space-between; gap:10px; }

  /* Slider */
  .slider{
    margin-top:14px;
    position:relative;
    border-radius: 18px;
    border:1px solid var(--line);
    overflow:hidden;
    background:#fff;
    max-width: 900px;
    margin-left:auto;
    margin-right:auto;
  }
  .slides{ display:flex; transition: transform .25s ease; }
  .slide{ min-width:100%; aspect-ratio: 16/10; }
  .slide img{ width:100%; height:100%; object-fit:cover; display:block; }
  .navBtn{
    position:absolute; top:50%; transform:translateY(-50%);
    width:44px; height:44px;
    border-radius:999px;
    border:1px solid rgba(0,0,0,.12);
    background: rgba(255,255,255,.92);
    cursor:pointer;
    font-weight:950;
    display:grid;
    place-items:center;
  }
  .navBtn.prev{ left:10px; }
  .navBtn.next{ right:10px; }
  @media (max-width:640px){ .navBtn{ display:none; } }

  /* Regalos */
  .giftGrid{
    margin-top:14px;
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:12px;
    max-width: 900px;
    margin-left:auto;
    margin-right:auto;
  }
  @media (max-width:860px){ .giftGrid{ grid-template-columns:1fr; } }

  .gift{
    border:1px solid var(--line);
    border-radius: 18px;
    background:#fff;
    padding: 14px;
    text-align:left;
    display:flex;
    gap:12px;
    align-items:center;
  }
  .giftIcon{
    width:56px; height:56px; border-radius: 18px;
    border:1px solid rgba(0,0,0,.12);
    display:grid; place-items:center;
    background: #fff;
    overflow:hidden;
    flex:0 0 auto;
  }
  .giftIcon img{ width:38px; height:38px; object-fit:contain; }
  .giftTitle{ font-weight:950; margin:0; }
  .giftCode{ color: var(--muted); font-weight:900; margin-top:4px; }

  .hashBox{
    margin-top:14px;
    border:1px solid rgba(0,0,0,.12);
    border-radius: 18px;
    background:#fff;
    padding: 14px;
    max-width: 900px;
    margin-left:auto;
    margin-right:auto;
  }
  .hash{ margin-top:8px; font-weight:950; font-size:1.25rem; }

  /* RSVP */
  .statusBadge{
    display:inline-flex;
    gap:8px;
    align-items:center;
    padding:10px 14px;
    border-radius:999px;
    border:1px solid rgba(0,0,0,.12);
    background:#fff;
    font-weight:950;
    margin-top:10px;
  }
  .statusBadge.ok{ border-color: rgba(60, 170, 110, .35); background: rgba(60,170,110,.08); }
  .statusBadge.no{ border-color: rgba(235, 80, 100, .35); background: rgba(235,80,100,.08); }

  /* Vestimenta */
  .dressImg{
    margin: 10px auto 0;
    max-width: 280px;
    width: 92%;
    height:auto;
    display:block;
  }
  .warn{
    margin-top:12px;
    display:inline-block;
    padding:10px 14px;
    border-radius:999px;
    border:1px solid rgba(0,0,0,.12);
    background: rgba(62,107,91,.08);
    font-weight:950;
  }

  .passWrap{
    position:relative;
    margin-top:14px;
    max-width: 440px;
    margin-left:auto;
    margin-right:auto;
    border-radius: 22px;
    overflow:hidden;
    background:#fff;

    /* borde + sombra premium */
    border:1px solid rgba(0,0,0,.12);
    box-shadow: 0 18px 40px rgba(0,0,0,.10);
    }

    /* marco dorado tipo foil */
    .passWrap::before{
    content:"";
    position:absolute;
    inset:0;
    border-radius:22px;
    padding:2px;
    background: linear-gradient(135deg,
        color-mix(in oklab, var(--accent) 55%, #fff),
        color-mix(in oklab, var(--accent2) 28%, #fff),
        color-mix(in oklab, var(--accent) 55%, #fff)
    );
    -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity:.75;
    pointer-events:none;
    z-index:3;
    }

    /* sombra interna suave */
    .passWrap::after{
    content:"";
    position:absolute;
    inset:0;
    border-radius:22px;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.55),
                inset 0 -30px 60px rgba(0,0,0,.12);
    pointer-events:none;
    z-index:2;
    }

    .passBg{
    position:relative;
    height: 340px;
    background-size: contain;        /* ✅ se ve completa */
    background-position: center;
    background-repeat: no-repeat;
    background-color: #fff;
    }

    /* brillo diagonal tipo “glass” */
    .passBg::before{
    content:"";
    position:absolute;
    inset:0;
    background:
        radial-gradient(420px 260px at 20% 12%, rgba(255,255,255,.55), rgba(255,255,255,0) 65%),
        linear-gradient(135deg, rgba(255,255,255,.0) 40%, rgba(255,255,255,.22) 55%, rgba(255,255,255,0) 70%);
    opacity:.55;
    pointer-events:none;
    }

    /* viñeta suave para enmarcar la foto */
    .passBg::after{
    content:"";
    position:absolute;
    inset:0;
    background: radial-gradient(120% 85% at 50% 30%, rgba(0,0,0,0) 55%, rgba(0,0,0,.14) 100%);
    opacity:.55;
    pointer-events:none;
    }

    /* badge “TU PASE” */
    .passBadge{
    position:absolute;
    top:12px;
    left:50%;
    transform:translateX(-50%);
    z-index:4;

    display:inline-flex;
    gap:8px;
    align-items:center;
    padding:8px 14px;
    border-radius:999px;
    background: rgba(255,255,255,.90);
    border:1px solid rgba(0,0,0,.12);
    box-shadow: 0 10px 20px rgba(0,0,0,.10);

    font-weight:950;
    letter-spacing:.18em;
    font-size:.82rem;
    color: color-mix(in oklab, var(--accent2) 65%, #000);
    }

    /* mini adorno esquinas (sutil) */
    .passCorner{
    position:absolute;
    width:28px;
    height:28px;
    border-radius:10px;
    border:1px solid rgba(181,155,106,.35);
    background: linear-gradient(180deg, rgba(181,155,106,.14), rgba(255,255,255,.0));
    z-index:4;
    opacity:.9;
    }
    .passCorner.tl{ top:12px; left:12px; }
    .passCorner.tr{ top:12px; right:12px; }
    .passCorner.bl{ bottom:12px; left:12px; }
    .passCorner.br{ bottom:12px; right:12px; }


  .passBody{ padding: 14px; }
  .passWho{ font-weight:950; font-size:1.08rem; }
  .passSeats{ margin-top:6px; font-weight:900; color: var(--muted); }
  .passDate{ margin-top:10px; font-weight:950; letter-spacing:.12em; }
  .passQuote{ margin-top:10px; color: var(--muted); font-weight:800; line-height:1.6; }

  /* Reveal */
  .reveal{ opacity:0; transform: translateY(14px); transition: opacity .45s ease, transform .45s ease; }
  .reveal.on{ opacity:1; transform: translateY(0); }

  .footer{
    padding: 16px 0 20px;
    text-align:center;
    color: rgba(0,0,0,.55);
    font-weight:900;
  }
</style>
@endsection

@section('content')
@php
  // ✅ MISMAS variables base
  $quince = $event->celebrant_name ?? 'Mis XV Años';
  $subtitle = $event->theme['subtitle'] ?? 'Prepárate para una celebración inolvidable...';
  $message = $event->message ?? 'Junto con mis padres, quiero compartir una noche llena de sueños, alegría y gratitud.';

  $datePretty = optional($event->starts_at)->locale('es')->translatedFormat('j \\d\\e F \\d\\e Y');
  $yearPretty = optional($event->starts_at)->format('Y');
  $timeText = optional($event->starts_at)->format('g:i A');
  $venue = $event->venue ?? '—';

  $mapsUrl = $event->maps_url ?? null;
  $whatsUrl = $event->theme['whatsapp_url'] ?? null;

  $eventoISO = optional($event->starts_at)->toIso8601String();

  $status = $invite->status; // ACTIVE | CONFIRMED | DECLINED
  $publicHash = request()->route('hash') ?? ($invite->hash_id ?? $invite->hash ?? null);
  $rsvpUrl = $publicHash ? route('invite.rsvp', ['hash' => $publicHash]) : null;

  // ✅ Assets xv_358
  $assetBase = $event->theme['asset_base_url'] ?? asset('storage/templates/xv_358/img');

  $imgLatIzq = $assetBase.'/latizquierdo.png';
  $imgDiv    = $assetBase.'/division.png';
  $imgDivC   = $assetBase.'/divisionclaro.png';
  $imgNombre = $assetBase.'/nombre.png';
  $imgMisa   = $assetBase.'/misa.jpg';
  $imgRecep  = $assetBase.'/recepcion.jpg';
  $imgVest   = $assetBase.'/vestimenta.png';

  // ✅ HERO fuente
  $heroFromTheme = $event->theme['hero_image_url'] ?? null;
  $heroFromEvent = $event->hero_image_path ? asset('storage/'.$event->hero_image_path) : null;
  $heroImg = $heroFromTheme ?: $heroFromEvent;

  $parents = $event->theme['parents'] ?? "Texto Texto\nTexto Texto";
  $godparents = $event->theme['godparents'] ?? "Texto Texto\nTexto Texto";

  $dateDay = optional($event->starts_at)->format('d') ?? '--';
  $dateMon = strtoupper(optional($event->starts_at)->locale('es')->translatedFormat('M') ?? '---');

  $ceremony = [
    'title' => $event->theme['ceremony_title'] ?? 'Ceremonia Religiosa',
    'place' => $event->theme['ceremony_place'] ?? 'Parroquia',
    'addr'  => $event->theme['ceremony_address'] ?? $venue,
    'time'  => $event->theme['ceremony_time'] ?? $timeText,
    'maps'  => $event->theme['ceremony_maps_url'] ?? $mapsUrl,
    'photo' => $event->theme['ceremony_photo_url'] ?? $imgMisa,
  ];

  $reception = [
    'title' => $event->theme['reception_title'] ?? 'Recepción',
    'place' => $event->theme['reception_place'] ?? 'Salón',
    'addr'  => $event->theme['reception_address'] ?? $venue,
    'time'  => $event->theme['reception_time'] ?? $timeText,
    'maps'  => $event->theme['reception_maps_url'] ?? $mapsUrl,
    'photo' => $event->theme['reception_photo_url'] ?? $imgRecep,
  ];

  $quote1 = $event->theme['quote_1'] ?? 'Cada amanecer trae consigo una promesa...';
  $quote2 = $event->theme['quote_2'] ?? 'Y hoy es el inicio de una hermosa historia por escribir...';

  $hashtag = $event->theme['hashtag'] ?? '#MISQUINCE';

  $dressTitle = $event->theme['dress_title'] ?? ($event->dress_code ?? 'Guapos y Guapas');
  $dressNote  = $event->theme['dress_note'] ?? 'Trae tu antifaz';
  $dressWarn  = $event->theme['reserved_color_note'] ?? 'COLOR VERDE RESERVADO PARA LA QUINCEAÑERA';
  $dressImg   = $event->theme['dress_image_url'] ?? $imgVest;

  // ✅ Pase: usa hero si existe, si no fallback a pass_bg_url o a vestimenta
  $passBg = $heroImg ?: ($event->theme['pass_bg_url'] ?? $imgVest);

  $passQuote = $event->theme['pass_quote'] ?? '"El futuro pertenece a quienes creen en la belleza de sus sueños"';

  $itinerary = $event->theme['itinerary'] ?? null;
  if(is_string($itinerary)){ $tmp = json_decode($itinerary, true); if(json_last_error()===JSON_ERROR_NONE) $itinerary = $tmp; }
  if(!is_array($itinerary)){
    $itinerary = [
      ['time'=>'6:00 PM','name'=>'Ceremonia Religiosa','icon_url'=>($event->theme['ico_ceremony_url'] ?? null)],
      ['time'=>'7:35 PM','name'=>'Recepción Bienvenida','icon_url'=>($event->theme['ico_cocktail_url'] ?? null)],
      ['time'=>'20:30 hrs','name'=>'Fotos divertidas','icon_url'=>($event->theme['ico_social_url'] ?? null)],
      ['time'=>'21:00 hrs','name'=>'Cena a 3 Tiempos','icon_url'=>($event->theme['ico_dinner_url'] ?? null)],
      ['time'=>'22:00 hrs','name'=>'Baile sorpresa','icon_url'=>($event->theme['ico_social2_url'] ?? null)],
      ['time'=>'22:30 hrs','name'=>'¡Queremos pastel!','icon_url'=>($event->theme['ico_cake_url'] ?? null)],
      ['time'=>'23:00 hrs','name'=>'¡A bailar!','icon_url'=>($event->theme['ico_party_url'] ?? null)],
    ];
  }

  $gifts = $event->theme['gifts'] ?? null;
  if(is_string($gifts)){ $tmp = json_decode($gifts, true); if(json_last_error()===JSON_ERROR_NONE) $gifts = $tmp; }
  if(!is_array($gifts)){
    $gifts = [
      ['title'=>'Mesa de Regalos','code'=>'Num. 00000','url'=>null,'icon_url'=>($event->theme['gift1_icon_url'] ?? null),'btn'=>'VER'],
      ['title'=>'Lluvia de Sobres','code'=>'El día del evento','url'=>null,'icon_url'=>($event->theme['gift2_icon_url'] ?? null),'btn'=>'VER'],
    ];
  }
@endphp

<div class="wrap">
  <div class="page">

    {{-- Flores --}}
    <img class="decor lt" src="{{ $imgLatIzq }}" alt="" aria-hidden="true">
    <img class="decor rt" src="{{ $imgLatIzq }}" alt="" aria-hidden="true">
    <img class="decor lb" src="{{ $imgLatIzq }}" alt="" aria-hidden="true">
    <img class="decor rb" src="{{ $imgLatIzq }}" alt="" aria-hidden="true">

    <div class="frame"></div>

    {{-- HERO --}}
    <header class="hero" id="top">
      <div class="heroBg"
           style="{{ $heroImg
              ? "background-image:url('".$heroImg."')"
              : "background: radial-gradient(800px 520px at 20% 10%, rgba(181,155,106,.18), transparent 55%), radial-gradient(680px 520px at 85% 20%, rgba(62,107,91,.14), transparent 55%), linear-gradient(180deg, #fff, #fbfaf7);" }}"></div>
      <div class="heroOverlay"></div>

      <div class="heroInner reveal">
        <div class="heroCard">
          <div class="kicker">XV AÑOS</div>

          <img class="nameImg" src="{{ $imgNombre }}" alt="{{ $event->title ?? 'XV Años' }}">

          <img class="dividerImg" src="{{ $imgDiv }}" alt="" aria-hidden="true">

          <div class="heroGuest">INVITACIÓN PERSONAL · {{ $invite->guest_name }}</div>
          <div class="heroSub">{{ $subtitle }}</div>

          <div class="btnRow">
            <a class="btn gold" href="#invitan">¡MIRA AQUÍ!</a>
            <a class="btn" href="#rsvp">CONFIRMAR</a>
          </div>
        </div>
      </div>
    </header>

    {{-- INVITAN --}}
    <section class="sec reveal" id="invitan">
      <h2 class="title" style="font-size:1.6rem;">¡Te invitamos a celebrar!</h2>

      <img class="dividerImg" src="{{ $imgDiv }}" alt="" aria-hidden="true">

      <div class="sub">{{ $message }}</div>

      <div class="grid2">
        <div class="box">
          <div class="lbl">Mis Padres:</div>
          <div class="val">{!! nl2br(e($parents)) !!}</div>
        </div>
        <div class="box">
          <div class="lbl">Mis Padrinos:</div>
          <div class="val">{!! nl2br(e($godparents)) !!}</div>
        </div>
      </div>
    </section>

    {{-- APARTA LA FECHA --}}
    <section class="sec reveal" id="fecha">
      <h2 class="title" style="font-size:1.6rem;">Aparta la fecha</h2>

      <img class="dividerImg" src="{{ $imgDiv }}" alt="" aria-hidden="true">

      <div class="dateBadge">
        <div class="d">{{ $dateDay }}</div>
        <div class="m">{{ $dateMon }}</div>
        <div class="y">{{ $yearPretty }}</div>
      </div>

      <div style="font-weight:950;">¡Estoy emocionada!</div>
      <div class="sub" style="margin-top:2px;">faltan solo:</div>

      <div class="countdown">
        <div class="cd"><div class="n" id="d">--</div><div class="l">Días</div></div>
        <div class="cd"><div class="n" id="h">--</div><div class="l">Horas</div></div>
        <div class="cd"><div class="n" id="m">--</div><div class="l">Minutos</div></div>
        <div class="cd"><div class="n" id="s">--</div><div class="l">Segundos</div></div>
      </div>
    </section>

    {{-- CUANDO Y DONDE --}}
    <section class="sec reveal" id="cuando-donde">
      <h2 class="title" style="font-size:1.6rem;">¿Cuándo y dónde?</h2>

      <img class="dividerImg" src="{{ $imgDiv }}" alt="" aria-hidden="true">

      <div class="placeGrid">
        <div class="placeCard">
          <div class="placePhoto" style="background-image:url('{{ $ceremony['photo'] }}');"></div>
          <div class="placeBody">
            <div class="tag">INICIAMOS CON LA</div>
            <div class="placeTitle">{{ $ceremony['title'] }}</div>
            <div class="addr"><strong>{{ $ceremony['place'] }}</strong><br>{{ $ceremony['addr'] }}</div>
            <div class="ptime">- {{ $ceremony['time'] }} -</div>
            @if($ceremony['maps'])
              <div style="margin-top:12px;">
                <a class="btn gold" target="_blank" rel="noopener" href="{{ $ceremony['maps'] }}">VER UBICACIÓN</a>
              </div>
            @endif
          </div>
        </div>

        <div class="placeCard">
          <div class="placePhoto" style="background-image:url('{{ $reception['photo'] }}');"></div>
          <div class="placeBody">
            <div class="tag">CONTINUAMOS CON LA</div>
            <div class="placeTitle">{{ $reception['title'] }}</div>
            <div class="addr"><strong>{{ $reception['place'] }}</strong><br>{{ $reception['addr'] }}</div>
            <div class="ptime">- {{ $reception['time'] }} -</div>
            @if($reception['maps'])
              <div style="margin-top:12px;">
                <a class="btn gold" target="_blank" rel="noopener" href="{{ $reception['maps'] }}">VER UBICACIÓN</a>
              </div>
            @endif
          </div>
        </div>
      </div>

      <div class="sub" style="margin-top:16px; font-weight:950;">
        {{ $quote1 }}<br>
        <span style="color:#222;">{{ $quote2 }}</span>
      </div>
    </section>

    {{-- ITINERARIO --}}
    <section class="sec reveal" id="itinerario">
      <h2 class="title" style="font-size:1.6rem;">Itinerario</h2>

      <img class="dividerImg" src="{{ $imgDivC }}" alt="" aria-hidden="true">

      <div class="tl">
        @foreach($itinerary as $i)
          <div class="tlItem">
            <div class="tlIcon">
              @if(!empty($i['icon_url']))
                <img src="{{ $i['icon_url'] }}" alt="" aria-hidden="true">
              @else
                <span style="font-weight:950;">✨</span>
              @endif
            </div>
            <div class="tlMain">
              <div class="tlTop">
                <div class="tlName">{{ $i['name'] ?? 'Actividad' }}</div>
                <div class="tlTime">{{ $i['time'] ?? '--:--' }}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </section>

    {{-- GALERÍA --}}
    @if($event->photos && $event->photos->count())
    <section class="sec reveal" id="fotos">
      <h2 class="title" style="font-size:1.6rem;">Fotos</h2>

      <div class="slider" data-slider>
        <div class="slides" data-slides>
          @foreach($event->photos->take(12) as $p)
            <div class="slide">
              <img src="{{ asset('storage/'.$p->path) }}" alt="Foto" loading="lazy">
            </div>
          @endforeach
        </div>
        <button class="navBtn prev" type="button" data-prev aria-label="Anterior">‹</button>
        <button class="navBtn next" type="button" data-next aria-label="Siguiente">›</button>
      </div>
    </section>
    @endif

    {{-- REGALOS + HASHTAG --}}
    <section class="sec reveal" id="regalos">
      <h2 class="title" style="font-size:1.6rem;">Mesa de regalos</h2>

      <img class="dividerImg" src="{{ $imgDiv }}" alt="" aria-hidden="true">

      <div class="giftGrid">
        @foreach($gifts as $g)
          <div class="gift">
            <div class="giftIcon">
              @if(!empty($g['icon_url']))
                <img src="{{ $g['icon_url'] }}" alt="" aria-hidden="true">
              @else
                🎁
              @endif
            </div>
            <div style="flex:1;">
              <p class="giftTitle">{{ $g['title'] ?? 'Regalo' }}</p>
              @if(!empty($g['code']))<div class="giftCode">{{ $g['code'] }}</div>@endif
              <div style="margin-top:10px;">
                @if(!empty($g['url']))
                  <a class="btn gold" target="_blank" rel="noopener" href="{{ $g['url'] }}">{{ $g['btn'] ?? 'VER' }}</a>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="hashBox">
        COMPARTE CONMIGO, PUBLICA TUS MOMENTOS DE LA FIESTA EN REDES SOCIALES<br>
        con el hashtag
        <div class="hash">{{ $hashtag }}</div>
      </div>
    </section>

    {{-- RSVP --}}
    <section class="sec reveal" id="rsvp">
      <h2 class="title" style="font-size:1.6rem;">¿Asistirás?</h2>

      <div class="sub">
        Tu presencia hará de este día un recuerdo inolvidable.<br>
        Por favor, confirma tu asistencia a este día tan especial para mi.<br>
        ¡Tu respuesta es muy importante!
      </div>

      <div class="sub" style="margin-top:12px; font-weight:950;">
        Te hemos asignado pases para:<br>
        <span style="color:#222;">{{ $invite->seats }} Personas</span>
      </div>

      @if($status === 'CONFIRMED')
        <div class="statusBadge ok">✅ Asistencia confirmada</div>
      @elseif($status === 'DECLINED')
        <div class="statusBadge no">❌ No podrá asistir</div>
      @else
        <div style="margin-top:14px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
          <button class="btn gold" id="btnYes" type="button">CONFIRMAR</button>
          <button class="btn" id="btnNo" type="button">NO PODRÉ ASISTIR</button>
        </div>

        @if($whatsUrl)
          <div style="margin-top:12px;">
            <a class="btn gold" target="_blank" rel="noopener" href="{{ $whatsUrl }}">CONFIRMAR POR WHATSAPP</a>
          </div>
        @endif
      @endif
    </section>

    {{-- VESTIMENTA --}}
    <section class="sec reveal" id="vestimenta">
      <h2 class="title" style="font-size:1.6rem;">Vestimenta</h2>

      <img class="dressImg" src="{{ $dressImg }}" alt="" aria-hidden="true">

      <div style="margin-top:10px; font-weight:950; font-size:1.15rem;">{{ $dressTitle }}</div>
      <div class="sub" style="margin-top:4px;">{{ $dressNote }}</div>

      @if($dressWarn)
        <div class="warn">{{ $dressWarn }}</div>
      @endif
    </section>

    {{-- PASE --}}
    <section class="sec reveal" id="pase">
      <h2 class="title" style="font-size:1.6rem;">¡Tu pase!</h2>

      <div class="passWrap">
        <div class="passBg" style="background-image:url('{{ $passBg }}');"></div>
        <div class="passBody">
          <div class="passWho">{{ $invite->guest_name }}</div>
          <div class="passSeats">Pase para <strong>{{ $invite->seats }}</strong> Personas</div>
          <div class="passDate">{{ strtoupper($datePretty ?? '') }}</div>
          <div class="passQuote">{{ $passQuote }}</div>
        </div>
      </div>
    </section>

    <div class="footer">
      Con cariño, <strong>{{ $quince }}</strong> · © {{ date('Y') }}
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
  // ✅ Evitar duplicidad: fallback solo si el layout NO lo trae.
  if (typeof window.qs !== 'function') {
    window.qs = function(id){ return document.getElementById(id); };
  }
  if (!window.RSVP_URL) {
    window.RSVP_URL = @json($rsvpUrl);
  }

  // ===== Countdown =====
  const EVENTO_ISO = @json($eventoISO);
  (function startCountdown(){
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
    tick(); setInterval(tick, 1000);
  })();

  // ===== Reveal =====
  (function(){
    const els = Array.from(document.querySelectorAll('.reveal'));
    if(!els.length) return;
    const io = new IntersectionObserver((entries)=>{
      entries.forEach(e=>{
        if(e.isIntersecting){
          e.target.classList.add('on');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.12 });
    els.forEach(el=>io.observe(el));
  })();

  // ===== Slider =====
  (function(){
    const slider = document.querySelector('[data-slider]');
    if(!slider) return;
    const track = slider.querySelector('[data-slides]');
    if(!track) return;
    const slides = Array.from(track.children);
    if(slides.length <= 1) return;

    const prev = slider.querySelector('[data-prev]');
    const next = slider.querySelector('[data-next]');
    let index = 0;

    function render(){ track.style.transform = `translateX(${-index * 100}%)`; }
    function go(dir){ index = (index + dir + slides.length) % slides.length; render(); }

    if(prev) prev.addEventListener('click', ()=>go(-1));
    if(next) next.addEventListener('click', ()=>go(1));

    let startX=0;
    slider.addEventListener('touchstart', (e)=>{ startX = e.touches[0].clientX; }, {passive:true});
    slider.addEventListener('touchend', (e)=>{
      const dx = e.changedTouches[0].clientX - startX;
      if(Math.abs(dx)>40) go(dx < 0 ? 1 : -1);
    }, {passive:true});

    render();
  })();

  // ===== RSVP =====
  const csrf = @json(csrf_token());

  if (!window.RSVP_URL) {
    console.warn('RSVP_URL missing');
    const yes = document.getElementById('btnYes');
    const no  = document.getElementById('btnNo');
    if (yes) yes.disabled = true;
    if (no)  no.disabled = true;
  }

  async function postRSVP(resp){
    const res = await fetch(window.RSVP_URL, {
      method: 'POST',
      credentials: 'same-origin',
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
    if(!res.ok) throw new Error('HTTP ' + res.status);
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
