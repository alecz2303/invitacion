@extends('layouts.app-simple')

@section('title','Panel · Evento')
@section('h1','Panel')
@section('subtitle','Editar evento · Foto hero · Música · Galería')

@section('content')
  <div class="card">
    @php
      // ====== Itinerary desde theme (array) ======
      $itinerary = $theme['itinerary'] ?? [];
      if (is_string($itinerary)) {
        $tmp = json_decode($itinerary, true);
        if (json_last_error() === JSON_ERROR_NONE) $itinerary = $tmp;
      }
      if (!is_array($itinerary)) $itinerary = [];

      // defaults si está vacío
      if (count($itinerary) === 0) {
        $itinerary = [
          ['time'=>'6:00 PM','name'=>'Ceremonia Religiosa','icon_url'=>null],
          ['time'=>'7:35 PM','name'=>'Recepción / Bienvenida','icon_url'=>null],
        ];
      }

      $gifts = $theme['gifts'] ?? [];
      if (is_string($gifts)) {
        $tmp = json_decode($gifts, true);
        if (json_last_error() === JSON_ERROR_NONE) $gifts = $tmp;
      }
      if (!is_array($gifts)) $gifts = [];

      if (count($gifts) === 0) {
        $gifts = [
          ['title'=>'Mesa de Regalos','code'=>'Num. 00000','url'=>'','icon_url'=>'','btn'=>'VER'],
          ['title'=>'Lluvia de Sobres','code'=>'El día del evento','url'=>'','icon_url'=>'','btn'=>'VER'],
        ];
      }

      // ✅ Keys que NO deben renderizarse en el loop del schema
      // porque ya los manejas con UI (sin JSON)
      $skipSchemaKeys = ['itinerary','gifts'];

      // Agrupar campos por "group"
      $groups = [];
      foreach(($schema ?? []) as $key => $meta){
        if (in_array($key, $skipSchemaKeys, true)) continue; // ✅ skip
        $g = $meta['group'] ?? 'General';
        $groups[$g][$key] = $meta;
      }

      $fieldValue = function($key) use ($theme){
        // old('theme.xxx') toma prioridad, luego theme resuelto
        return old('theme.'.$key, $theme[$key] ?? null);
      };

      $asJsonPretty = function($val){
        if (is_string($val)) return $val;
        if (is_array($val) || is_object($val)) {
          return json_encode($val, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        }
        return '';
      };
    @endphp

    <form method="POST" action="{{ route('panel.event.update') }}" enctype="multipart/form-data">
      @csrf

      <div style="font-weight:900;margin-bottom:6px">Datos del evento</div>

      <label>Título</label>
      <input name="title" value="{{ old('title', $event->title) }}"/>

      <label>Nombre celebrante</label>
      <input name="celebrant_name" value="{{ old('celebrant_name', $event->celebrant_name) }}"/>

      <label>Fecha y hora</label>
      <input type="datetime-local" name="starts_at"
        value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\TH:i')) }}"/>

      <label>Lugar</label>
      <input name="venue" value="{{ old('venue', $event->venue) }}"/>

      <label>Mapa (link de Google Maps)</label>
      <input name="maps_url"
             value="{{ old('maps_url', $event->maps_url) }}"
             placeholder="https://maps.app.goo.gl/... o https://www.google.com/maps?q=..." />

      <label>Vestimenta</label>
      <input name="dress_code" value="{{ old('dress_code', $event->dress_code) }}"/>

      <label>Mensaje</label>
      <input name="message" value="{{ old('message', $event->message) }}"/>

      <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:14px 0">
      <div style="font-weight:900;margin-bottom:6px">Tema / Plantilla ({{ $type ?? 'xv' }})</div>
      <div class="muted small" style="margin-bottom:10px">
        Estos campos se guardan en <code>events.theme</code> y controlan el template.
      </div>

      <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:14px 0">

      <div style="font-weight:900;margin-bottom:6px">Itinerario (sin JSON)</div>
      <div class="muted small">Agrega actividades. Puedes dejar el icono vacío.</div>

      <div id="itineraryWrap" style="display:grid;gap:10px;margin-top:10px">
        @foreach($itinerary as $idx => $row)
          <div class="it-row" style="padding:12px;border:1px solid rgba(255,255,255,.12);border-radius:14px;background:rgba(255,255,255,.04)">
            <div style="display:grid;grid-template-columns: 140px 1fr; gap:10px; align-items:center">
              <div>
                <label>Hora</label>
                <input name="theme[itinerary][{{ $idx }}][time]" value="{{ $row['time'] ?? '' }}" placeholder="8:30 PM">
              </div>
              <div>
                <label>Actividad</label>
                <input name="theme[itinerary][{{ $idx }}][name]" value="{{ $row['name'] ?? '' }}" placeholder="Cena / Vals / Baile sorpresa">
              </div>
            </div>

            <div style="margin-top:10px">
              <label>Icono (URL opcional)</label>
              <input name="theme[itinerary][{{ $idx }}][icon_url]" value="{{ $row['icon_url'] ?? '' }}" placeholder="https://.../icon.png">
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:10px">
              <button type="button" class="btn btnDanger" onclick="this.closest('.it-row').remove()">Quitar</button>
            </div>
          </div>
        @endforeach
      </div>

      <div style="display:flex;justify-content:flex-end;margin-top:10px">
        <button type="button" class="btn" id="addItineraryBtn">+ Agregar actividad</button>
      </div>

      

      {{-- ====== Campos del schema (excepto itinerary/gifts) ====== --}}
      @foreach($groups as $groupName => $fields)
        <div style="margin-top:12px;padding:12px;border:1px solid rgba(255,255,255,.10);border-radius:14px;background:rgba(255,255,255,.04)">
          <div style="font-weight:900;margin-bottom:10px">{{ $groupName }}</div>

          @foreach($fields as $key => $meta)
            @php
              $label = $meta['label'] ?? $key;
              $typeField = $meta['type'] ?? 'text';
              $ph = $meta['placeholder'] ?? '';
              $help = $meta['help'] ?? null;
              $val = $fieldValue($key);

              $isColor = $typeField === 'color';
              $isTextarea = $typeField === 'textarea';
              $isJson = $typeField === 'json';
              $isImage = $typeField === 'image';
              $inputType = in_array($typeField, ['text','url','number','color']) ? $typeField : 'text';
            @endphp

            <label style="margin-top:10px;display:block">{{ $label }}</label>

            @if($isImage)
              <div style="display:grid; gap:10px;">
                @if(!empty($val))
                  <div class="muted small">Actual:</div>
                  <img
                    src="{{ str_starts_with($val, 'http') ? $val : asset('storage/'.$val) }}"
                    style="max-width:260px;border-radius:14px;border:1px solid rgba(255,255,255,.12);"
                  >
                  <label style="display:flex;align-items:center;gap:8px;margin-top:6px">
                    <input type="checkbox" name="theme_remove[{{ $key }}]" value="1" style="width:auto">
                    Quitar imagen
                  </label>
                @endif

                <input
                  type="file"
                  name="theme_files[{{ $key }}]"
                  accept="image/*"
                />

                <div class="muted small">
                  Se guarda en <code>events.theme.{{ $key }}</code> como ruta del archivo.
                </div>
              </div>

            @elseif($isTextarea)
              <textarea name="theme[{{ $key }}]" rows="3"
                style="width:100%;padding:12px;border-radius:14px;border:1px solid rgba(255,255,255,.12);background:rgba(0,0,0,.20);color:#fff"
                placeholder="{{ $ph }}"
              >{{ is_array($val) ? '' : (string)$val }}</textarea>

            @elseif($isJson)
              <textarea name="theme[{{ $key }}]" rows="8"
                style="width:100%;padding:12px;border-radius:14px;border:1px solid rgba(255,255,255,.12);background:rgba(0,0,0,.20);color:#fff;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
                placeholder='{{ $ph ?: "[{...}]" }}'
              >{{ $asJsonPretty($val) }}</textarea>

            @else
              <div style="display:flex;gap:10px;align-items:center">
                <input
                  name="theme[{{ $key }}]"
                  type="{{ $inputType }}"
                  value="{{ is_array($val) ? '' : (string)$val }}"
                  placeholder="{{ $ph }}"
                  style="{{ $isColor ? 'max-width:140px;' : '' }}"
                />
                @if($isColor)
                  <span class="muted small">{{ is_array($val) ? '' : ($val ?: '') }}</span>
                @endif
              </div>
            @endif

            @if($help)
              <div class="muted small" style="margin-top:6px">{{ $help }}</div>
            @endif
          @endforeach
        </div>
      @endforeach

      <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:14px 0">

      <div style="font-weight:900;margin-bottom:6px">Regalos / Mesa de regalos</div>
      <div class="muted small">Puedes dejar URL vacío si no aplica.</div>

      <div id="giftsWrap" style="display:grid;gap:10px;margin-top:10px">
        @foreach($gifts as $idx => $g)
          <div class="card" style="padding:12px;border:1px solid rgba(255,255,255,.12);border-radius:14px">
            <label>Título</label>
            <input name="theme[gifts][{{ $idx }}][title]" value="{{ $g['title'] ?? '' }}" placeholder="Mesa de regalos">

            <label>Código / Nota</label>
            <input name="theme[gifts][{{ $idx }}][code]" value="{{ $g['code'] ?? '' }}" placeholder="Num. 12345 o 'El día del evento'">

            <label>URL (opcional)</label>
            <input name="theme[gifts][{{ $idx }}][url]" value="{{ $g['url'] ?? '' }}" placeholder="https://...">

            <label>Texto del botón (opcional)</label>
            <input name="theme[gifts][{{ $idx }}][btn]" value="{{ $g['btn'] ?? 'VER' }}" placeholder="VER">

            <label>Icono URL (opcional)</label>
            <input name="theme[gifts][{{ $idx }}][icon_url]" value="{{ $g['icon_url'] ?? '' }}" placeholder="https://.../gift.png">

            <div style="display:flex;justify-content:flex-end;margin-top:10px">
              <button type="button" class="btn btnDanger" onclick="this.closest('.card').remove()">Quitar</button>
            </div>
          </div>
        @endforeach
      </div>

      <div style="display:flex;justify-content:flex-end;margin-top:10px">
        <button type="button" class="btn" id="addGiftBtn">+ Agregar regalo</button>
      </div>

      <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:14px 0">

      <div style="font-weight:900;margin-bottom:6px">Hero (foto principal)</div>

      @if($event->hero_image_path)
        <div class="muted small">Actual:</div>
        <img src="{{ asset('storage/'.$event->hero_image_path) }}" style="max-width:260px;border-radius:14px;border:1px solid rgba(255,255,255,.12);margin-top:6px">
        <label style="display:flex;align-items:center;gap:8px;margin-top:10px">
          <input type="checkbox" name="remove_hero" value="1" style="width:auto"> Quitar foto hero
        </label>
      @endif

      <label>Subir nueva foto (JPG/PNG, máx 4MB)</label>
      <input type="file" name="hero_image" accept="image/*"/>

      <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:14px 0">

      <div style="font-weight:900;margin-bottom:6px">Música (opcional)</div>

      <label>Título de la canción (opcional)</label>
      <input name="music_title" value="{{ old('music_title', $event->music_title) }}" placeholder="Vals / Canción especial"/>

      @if($event->music_path)
        <div style="margin-top:8px">
          <audio controls style="width:100%">
            <source src="{{ asset('storage/'.$event->music_path) }}">
          </audio>
        </div>
        <label style="display:flex;align-items:center;gap:8px;margin-top:10px">
          <input type="checkbox" name="remove_music" value="1" style="width:auto"> Quitar música
        </label>
      @endif

      <label>Subir música (mp3/wav/ogg, máx 12MB)</label>
      <input type="file" name="music" accept="audio/*"/>

      <div class="row" style="margin-top:14px;justify-content:flex-end">
        <button class="btn btnPrimary" type="submit">Guardar cambios</button>
      </div>
    </form>
  </div>

  <div class="card" style="margin-top:14px">
    <div style="font-weight:900;margin-bottom:6px">Galería (máx 12 fotos)</div>
    <div class="muted small">Sube entre 6 y 12 fotos para que se vea premium.</div>

    <form method="POST" action="{{ route('panel.event.gallery.add') }}" enctype="multipart/form-data" style="margin-top:10px">
      @csrf
      <label>Agregar fotos (puedes seleccionar varias)</label>
      <input type="file" name="photos[]" accept="image/*" multiple>
      <div class="row" style="margin-top:10px;justify-content:flex-end">
        <button class="btn btnPrimary" type="submit">Subir a galería</button>
      </div>
    </form>

    @if($event->photos->count())
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-top:12px">
        @foreach($event->photos as $p)
          <div style="border:1px solid rgba(255,255,255,.12);border-radius:14px;overflow:hidden">
            <img src="{{ asset('storage/'.$p->path) }}" style="width:100%;height:140px;object-fit:cover;display:block">
            <form method="POST" action="{{ route('panel.event.gallery.delete', $p) }}" onsubmit="return confirm('¿Eliminar esta foto?')">
              @csrf
              @method('DELETE')
              <button class="btn btnDanger" type="submit" style="width:100%;border-radius:0">Eliminar</button>
            </form>
          </div>
        @endforeach
      </div>
    @endif
  </div>
@endsection

@section('scripts')
<script>
  (function(){
    const wrap = document.getElementById('itineraryWrap');
    const btn  = document.getElementById('addItineraryBtn');
    if(!wrap || !btn) return;

    function nextIndex(){
      const inputs = wrap.querySelectorAll('input[name^="theme[itinerary]"]');
      let max = -1;
      inputs.forEach(i=>{
        const m = i.name.match(/theme\[itinerary]\[(\d+)\]/);
        if(m) max = Math.max(max, parseInt(m[1],10));
      });
      return max + 1;
    }

    btn.addEventListener('click', ()=>{
      const i = nextIndex();
      const div = document.createElement('div');
      div.className = 'it-row';
      div.style.cssText = 'padding:12px;border:1px solid rgba(255,255,255,.12);border-radius:14px;background:rgba(255,255,255,.04)';
      div.innerHTML = `
        <div style="display:grid;grid-template-columns: 140px 1fr; gap:10px; align-items:center">
          <div>
            <label>Hora</label>
            <input name="theme[itinerary][${i}][time]" placeholder="8:30 PM">
          </div>
          <div>
            <label>Actividad</label>
            <input name="theme[itinerary][${i}][name]" placeholder="Cena / Vals / Baile sorpresa">
          </div>
        </div>

        <div style="margin-top:10px">
          <label>Icono (URL opcional)</label>
          <input name="theme[itinerary][${i}][icon_url]" placeholder="https://.../icon.png">
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:10px">
          <button type="button" class="btn btnDanger" onclick="this.closest('.it-row').remove()">Quitar</button>
        </div>
      `;
      wrap.appendChild(div);
    });
  })();
</script>

<script>
  (function(){
    const wrap = document.getElementById('giftsWrap');
    const btn  = document.getElementById('addGiftBtn');
    if(!wrap || !btn) return;

    function nextIndex(){
      const inputs = wrap.querySelectorAll('input[name^="theme[gifts]"]');
      let max = -1;
      inputs.forEach(i=>{
        const m = i.name.match(/theme\[gifts]\[(\d+)]/);
        if(m) max = Math.max(max, parseInt(m[1],10));
      });
      return max + 1;
    }

    btn.addEventListener('click', ()=>{
      const i = nextIndex();
      const div = document.createElement('div');
      div.className = 'card';
      div.style.cssText = 'padding:12px;border:1px solid rgba(255,255,255,.12);border-radius:14px';
      div.innerHTML = `
        <label>Título</label>
        <input name="theme[gifts][${i}][title]" placeholder="Mesa de regalos">

        <label>Código / Nota</label>
        <input name="theme[gifts][${i}][code]" placeholder="Num. 12345 o 'El día del evento'">

        <label>URL (opcional)</label>
        <input name="theme[gifts][${i}][url]" placeholder="https://...">

        <label>Texto del botón (opcional)</label>
        <input name="theme[gifts][${i}][btn]" value="VER" placeholder="VER">

        <label>Icono URL (opcional)</label>
        <input name="theme[gifts][${i}][icon_url]" placeholder="https://.../gift.png">

        <div style="display:flex;justify-content:flex-end;margin-top:10px">
          <button type="button" class="btn btnDanger" onclick="this.closest('.card').remove()">Quitar</button>
        </div>
      `;
      wrap.appendChild(div);
    });
  })();
</script>
@endsection
