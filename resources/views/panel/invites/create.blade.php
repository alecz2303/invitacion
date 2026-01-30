@extends('layouts.app-simple')

@section('title','Panel · Nuevo invitado')
@section('h1','Panel')
@section('subtitle','Crear invitado')

@section('content')
  <div class="card">
    <form method="POST" action="{{ route('panel.invites.store') }}">
      @csrf

      <label>Evento</label>
      <select name="event_id">
        @foreach($events as $ev)
          <option value="{{ $ev->id }}" @selected(old('event_id')==$ev->id)>
            {{ $ev->title }} ({{ $ev->starts_at?->format('Y-m-d H:i') }})
          </option>
        @endforeach
      </select>

      <label>Nombre del invitado</label>
      <input name="guest_name" value="{{ old('guest_name') }}" placeholder="Familia Rueda" />

      <label>Lugares asignados</label>
      <input type="number" name="seats" min="1" max="20" value="{{ old('seats', 2) }}" />

      <div class="row" style="margin-top:14px;justify-content:space-between">
        <a class="btn" href="{{ route('panel.invites.index') }}">← Volver</a>
        <button class="btn btnPrimary" type="submit">Guardar</button>
      </div>
    </form>
  </div>
@endsection
