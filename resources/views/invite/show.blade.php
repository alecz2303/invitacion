<!doctype html>
<html lang="es-MX">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>{{ $invite->event->title ?? 'Invitación' }}</title>
  <meta name="description" content="Invitación digital" />
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Arial;margin:0;background:#0b0b12;color:#fff;}
    .wrap{max-width:900px;margin:0 auto;padding:24px;}
    .card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:18px;padding:18px}
    .muted{color:rgba(255,255,255,.75)}
    .row{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px}
    .btn{padding:12px 14px;border-radius:14px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);color:#fff;font-weight:800;cursor:pointer}
    .btnPrimary{background:#d9b06c;color:#1b1202;border-color:rgba(217,176,108,.6)}
    .btnDanger{background:rgba(255,90,106,.18);border-color:rgba(255,90,106,.35)}
    .status{margin-top:12px;padding:12px;border-radius:14px;border:1px solid rgba(255,255,255,.18);display:none}
    .status.ok{display:block;border-color:rgba(76,209,124,.45);background:rgba(76,209,124,.12)}
    .status.bad{display:block;border-color:rgba(255,90,106,.45);background:rgba(255,90,106,.12)}
    input{width:100%;padding:12px;border-radius:14px;border:1px solid rgba(255,255,255,.2);background:rgba(0,0,0,.2);color:#fff}
  </style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h1 style="margin:0 0 8px">{{ $invite->event->celebrant_name ?? 'Evento' }}</h1>
    <div class="muted">
      {{ optional($invite->event->starts_at)->format('d/m/Y h:i a') }}
      @if($invite->event->venue) · {{ $invite->event->venue }} @endif
    </div>
    <p class="muted" style="margin-top:10px">{{ $invite->event->message }}</p>

    <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:14px 0">

    <h2 style="margin:0 0 6px">Confirmación</h2>
    <p class="muted" id="note">
      Hemos reservado <b>{{ $invite->seats }}</b> lugares para <b>{{ $invite->guest_name }}</b>.
    </p>

    <div style="margin-top:10px;">
      <div class="muted" style="font-size:.9rem">Nombre</div>
      <input id="guestName" value="{{ $invite->guest_name }}" />
    </div>

    <div class="row">
      <button class="btn btnPrimary" id="btnYes">Confirmo asistencia</button>
      <button class="btn btnDanger" id="btnNo">No podré asistir</button>
    </div>

    <div class="status" id="statusBox"></div>
  </div>
</div>

<script>
  const RSVP_URL = @json(route('invite.rsvp', ['hash' => $hash]));

  const statusBox = document.getElementById('statusBox');
  const btnYes = document.getElementById('btnYes');
  const btnNo = document.getElementById('btnNo');
  const guestName = document.getElementById('guestName');

  function setStatus(type, msg){
    statusBox.className = 'status ' + type;
    statusBox.textContent = msg;
  }

  async function send(response){
    const name = guestName.value.trim();
    if(!name){
      setStatus('bad','Por favor escribe tu nombre.');
      return;
    }

    setStatus('ok','Enviando...');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const res = await fetch(RSVP_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ response, name })
    });

    const data = await res.json().catch(()=>null);

    if(!res.ok || !data){
      setStatus('bad','No se pudo enviar. Intenta de nuevo.');
      return;
    }

    if(data.estado === 'YA_CONFIRMADO'){
      btnYes.style.display = 'none';
      btnNo.style.display = 'none';
      guestName.setAttribute('disabled','disabled');
      setStatus('ok','✅ Tu confirmación ya fue registrada.');
      return;
    }

    btnYes.style.display = 'none';
    btnNo.style.display = 'none';
    guestName.setAttribute('disabled','disabled');

    setStatus('ok', response === 'SI'
      ? '¡Gracias por confirmar! ✨'
      : 'Gracias por avisarnos. 💖'
    );
  }

  btnYes.addEventListener('click', () => send('SI'));
  btnNo.addEventListener('click', () => send('NO'));
</script>
</body>
</html>
