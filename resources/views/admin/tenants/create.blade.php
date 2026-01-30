@extends('layouts.app-simple')

@section('title','Admin · Crear tenant')
@section('h1','Admin')
@section('subtitle','Crear tenant + usuario cliente + evento base')

@section('content')
  <div class="card">
    <form method="POST" action="{{ route('admin.tenants.store') }}">
      @csrf

      <div style="font-weight:900;font-size:1.05rem">Tenant (cliente)</div>
      <label>Nombre del tenant</label>
      <input name="tenant_name" value="{{ old('tenant_name') }}" placeholder="XV de Mariana / Boda de ..." />

      <label>Slug (subdominio)</label>
      <input name="tenant_slug" value="{{ old('tenant_slug') }}" placeholder="mariana (solo letras/números/guiones)" />
      <div class="muted small" style="margin-top:6px">
        Esto creará: <b>{slug}.events.partyx.com.mx</b>
      </div>

      <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:14px 0">

      <div style="font-weight:900;font-size:1.05rem">Usuario (cliente)</div>
      <label>Nombre</label>
      <input name="client_name" value="{{ old('client_name') }}" placeholder="Mariana López" />

      <label>Email</label>
      <input name="client_email" value="{{ old('client_email') }}" placeholder="cliente@email.com" />

      <label>Contraseña</label>
      <input type="password" name="client_password" placeholder="mínimo 8 caracteres" />

      <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:14px 0">

      <div style="font-weight:900;font-size:1.05rem">Evento base</div>
      <label>Tipo</label>
      <select name="event_type">
        @php $t = old('event_type','xv'); @endphp
        <option value="xv" @selected($t==='xv')>XV años</option>
        <option value="boda" @selected($t==='boda')>Boda</option>
        <option value="bautizo" @selected($t==='bautizo')>Bautizo</option>
        <option value="cumple" @selected($t==='cumple')>Cumpleaños</option>
      </select>

      <label>Título (lo que verá en panel)</label>
      <input name="event_title" value="{{ old('event_title','Evento') }}" placeholder="Mis XV Años / Nuestra Boda..." />

      <label>Nombre celebrante (quinceañera / novios)</label>
      <input name="celebrant_name" value="{{ old('celebrant_name') }}" placeholder="Mariana" />

      <label>Fecha y hora</label>
      <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" />

      <label>Lugar (opcional)</label>
      <input name="venue" value="{{ old('venue') }}" placeholder="Salón / Iglesia / Jardín..." />

      <div class="row" style="margin-top:14px;justify-content:space-between">
        <a class="btn" href="{{ route('admin.tenants.index') }}">← Volver</a>
        <button class="btn btnPrimary" type="submit">Crear</button>
      </div>
    </form>
  </div>
@endsection
