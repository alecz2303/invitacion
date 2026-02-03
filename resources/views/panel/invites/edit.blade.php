@extends('layouts.app-simple')

@section('title','Panel · Editar invitado')
@section('h1','Panel')
@section('subtitle','Editar invitado')

@section('content')
@php
  $card = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm';
  $btnBase = 'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-black shadow-sm transition focus:outline-none focus:ring-4';
  $btnPrimary = $btnBase.' border border-slate-900 bg-slate-900 text-white hover:bg-slate-800 focus:ring-slate-200';
  $btnSoft = $btnBase.' border border-slate-200 bg-white text-slate-900 hover:bg-slate-50 focus:ring-slate-100';
  $input = 'mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100';
  $label = 'block text-sm font-extrabold text-slate-800';
@endphp

<div class="mx-auto max-w-3xl">
  <div class="{{ $card }}">
    <form method="POST" action="{{ route('panel.invites.update', $invite) }}" class="space-y-4">
      @csrf
      @method('PUT')

      <div>
        <label class="{{ $label }}">Evento</label>
        <select name="event_id" class="{{ $input }}">
          @foreach($events as $ev)
            <option value="{{ $ev->id }}" @selected(old('event_id', $invite->event_id)==$ev->id)>
              {{ $ev->title }} ({{ $ev->starts_at?->format('Y-m-d H:i') }})
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="{{ $label }}">Nombre del invitado</label>
        <input name="guest_name" value="{{ old('guest_name', $invite->guest_name) }}" class="{{ $input }}" />
      </div>

      <div>
        <label class="{{ $label }}">Lugares asignados</label>
        <input type="number" name="seats" min="1" max="20" value="{{ old('seats', $invite->seats) }}" class="{{ $input }}" />
      </div>

      <div>
        <label class="{{ $label }}">Estado</label>
        <select name="status" class="{{ $input }}">
          <option value="ACTIVE" @selected(old('status',$invite->status)==='ACTIVE')>🟡 Pendiente</option>
          <option value="CONFIRMED" @selected(old('status',$invite->status)==='CONFIRMED')>✅ Confirmado</option>
          <option value="DECLINED" @selected(old('status',$invite->status)==='DECLINED')>❌ Declinó</option>
        </select>
      </div>

      <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center pt-2">
        <a class="{{ $btnSoft }}" href="{{ route('panel.invites.index') }}">← Volver</a>
        <button class="{{ $btnPrimary }}" type="submit">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
@endsection
