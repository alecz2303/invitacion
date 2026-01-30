<!doctype html>
<html lang="es-MX">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', $event->title ?? 'Invitación')</title>
  <meta name="description" content="Invitación digital" />

  @yield('head')
</head>
<body>
  @yield('content')

  <script>
    // Endpoints
    const RSVP_URL = @json(route('invite.rsvp', ['hash' => $hash]));
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Helpers
    function qs(id){ return document.getElementById(id); }
    function setStatus(type, msg){
      const box = qs('statusBox');
      if(!box) return;
      box.className = 'status ' + type;
      box.textContent = msg;
    }

    async function sendRSVP(response){
      const nameEl = qs('guestName');
      const name = (nameEl ? nameEl.value : '').trim();
      if(!name){
        setStatus('bad', 'Por favor escribe tu nombre para confirmar.');
        return;
      }

      setStatus('ok','Enviando...');
      const res = await fetch(RSVP_URL, {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': CSRF,
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
        // bloquea UI
        if(qs('btnYes')) qs('btnYes').style.display = 'none';
        if(qs('btnNo')) qs('btnNo').style.display = 'none';
        if(nameEl) nameEl.setAttribute('disabled','disabled');
        setStatus('ok','✅ Tu respuesta ya fue registrada.');
        return;
      }

      if(qs('btnYes')) qs('btnYes').style.display = 'none';
      if(qs('btnNo')) qs('btnNo').style.display = 'none';
      if(nameEl) nameEl.setAttribute('disabled','disabled');

      setStatus('ok', response === 'SI'
        ? '¡Gracias por confirmar! Nos alegra contar con tu presencia ✨'
        : 'Gracias por avisarnos. Esperamos verte en otra ocasión 💖'
      );
    }

    // binds si existen
    document.addEventListener('click', (e) => {
      if(e.target && e.target.id === 'btnYes') sendRSVP('SI');
      if(e.target && e.target.id === 'btnNo') sendRSVP('NO');
    });
  </script>

  @yield('scripts')
</body>
</html>
