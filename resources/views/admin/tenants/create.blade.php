@extends('layouts.app-simple')

@section('title','Admin · Crear tenant')
@section('h1','Admin')
@section('subtitle','Crear tenant + usuario cliente + evento base')

@section('content')
@php
  $card = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm';
  $label = 'block text-sm font-extrabold text-slate-800';
  $hint  = 'mt-1 text-xs text-slate-500';
  $input = 'mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100';
  $btnBase = 'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-black shadow-sm transition focus:outline-none focus:ring-4';
  $btnPrimary = $btnBase.' border border-slate-900 bg-slate-900 text-white hover:bg-slate-800 focus:ring-slate-200';
  $btnSoft = $btnBase.' border border-slate-200 bg-white text-slate-900 hover:bg-slate-50 focus:ring-slate-100';

  $domain = 'events.partyx.com.mx';
@endphp

<div class="mx-auto max-w-4xl space-y-4">

  <div class="{{ $card }}">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="text-base font-black text-slate-900">Crear tenant</h2>
        <p class="mt-1 text-sm text-slate-600">Crea cliente, usuario y evento base en un solo paso.</p>
      </div>
      <a class="{{ $btnSoft }}" href="{{ route('admin.tenants.index') }}">← Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.tenants.store') }}" class="mt-5 space-y-6">
      @csrf

      {{-- ======================
          TENANT
      ====================== --}}
      <section class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex items-end justify-between gap-3">
          <div>
            <h3 class="text-sm font-black text-slate-900">Tenant (cliente)</h3>
            <p class="mt-1 text-sm text-slate-600">Información del cliente (subdominio).</p>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <label class="{{ $label }}">Nombre del tenant</label>
            <input name="tenant_name" value="{{ old('tenant_name') }}" placeholder="XV de Mariana / Boda de ..." class="{{ $input }}" />
          </div>

          <div>
            <label class="{{ $label }}">Slug (subdominio)</label>
            <input name="tenant_slug" value="{{ old('tenant_slug') }}" placeholder="mariana (solo letras/números/guiones)" class="{{ $input }}" />
            <p class="{{ $hint }}">Esto creará: <span class="font-black">{slug}.{{ $domain }}</span></p>
          </div>
        </div>
      </section>

      {{-- ======================
          USUARIO CLIENTE
      ====================== --}}
      <section class="rounded-2xl border border-slate-200 bg-white p-4">
        <h3 class="text-sm font-black text-slate-900">Usuario (cliente)</h3>
        <p class="mt-1 text-sm text-slate-600">Cuenta para acceder al panel.</p>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <label class="{{ $label }}">Nombre</label>
            <input name="client_name" value="{{ old('client_name') }}" placeholder="Mariana López" class="{{ $input }}" />
          </div>

          <div>
            <label class="{{ $label }}">Email</label>
            <input type="email" name="client_email" value="{{ old('client_email') }}" placeholder="cliente@email.com" class="{{ $input }}" />
          </div>

          <div class="lg:col-span-2">
            <label class="{{ $label }}">Contraseña</label>
            <input type="password" name="client_password" placeholder="mínimo 8 caracteres" class="{{ $input }}" />
            <p class="{{ $hint }}">Se usará para crear el usuario con rol <b>client</b>.</p>
          </div>
        </div>
      </section>

      {{-- ======================
          EVENTO BASE
      ====================== --}}
      <section class="rounded-2xl border border-slate-200 bg-white p-4">
        <h3 class="text-sm font-black text-slate-900">Evento base</h3>
        <p class="mt-1 text-sm text-slate-600">Se crea un evento inicial para que el cliente edite desde su panel.</p>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <label class="{{ $label }}">Tipo</label>
            @php $t = old('event_type','xv'); @endphp
            <select name="event_type" class="{{ $input }}">
              <option value="xv" @selected($t==='xv')>XV años</option>
              <option value="boda" @selected($t==='boda')>Boda</option>
              <option value="bautizo" @selected($t==='bautizo')>Bautizo</option>
              <option value="cumple" @selected($t==='cumple')>Cumpleaños</option>
            </select>
          </div>

          <div>
            <label class="{{ $label }}">Título (lo que verá en panel)</label>
            <input name="event_title" value="{{ old('event_title','Evento') }}" placeholder="Mis XV Años / Nuestra Boda..." class="{{ $input }}" />
          </div>

          <div>
            <label class="{{ $label }}">Nombre celebrante</label>
            <input name="celebrant_name" value="{{ old('celebrant_name') }}" placeholder="Mariana" class="{{ $input }}" />
          </div>

          <div>
            <label class="{{ $label }}">Fecha y hora</label>
            <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="{{ $input }}" />
          </div>

          <div class="lg:col-span-2">
            <label class="{{ $label }}">Lugar (opcional)</label>
            <input name="venue" value="{{ old('venue') }}" placeholder="Salón / Iglesia / Jardín..." class="{{ $input }}" />
          </div>
        </div>
      </section>

      <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-between">
        <a class="{{ $btnSoft }}" href="{{ route('admin.tenants.index') }}">← Volver</a>
        <button class="{{ $btnPrimary }}" type="submit">✨ Crear</button>
      </div>
    </form>
  </div>

</div>
@endsection
