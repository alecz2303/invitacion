@extends('layouts.app-simple')

@section('title','Panel · Evento')
@section('h1','Panel')
@section('subtitle','Editar evento · Foto hero · Música · Galería')

@section('content')
  <div class="card">
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
