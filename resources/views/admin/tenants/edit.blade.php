@extends('layouts.app-simple')

@section('title','Admin · Editar tenant')
@section('h1','Admin')
@section('subtitle','Editar tenant + usuario cliente + evento base')

@section('content')
@php
  $card = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm';
  $label = 'block text-sm font-extrabold text-slate-800';
  $hint  = 'mt-1 text-xs text-slate-500';
  $input = 'mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100';

  $btnBase = 'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-black shadow-sm transition focus:outline-none focus:ring-4';
  $btnPrimary = $btnBase.' border border-slate-900 bg-slate-900 text-white hover:bg-slate-800 focus:ring-slate-200';
  $btnSoft = $btnBase.' border border-slate-200 bg-white text-slate-900 hover:bg-slate-50 focus:ring-slate-100';
  $btnDangerSoft = $btnBase.' border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 focus:ring-rose-100';

  $domain = 'events.partyx.com.mx';
  $subdomain = ($tenant->slug ?? '').'.'.$domain;

  // ⚠️ Estos vienen del controller (te dejo abajo cómo)
  $clientUser = $clientUser ?? null;
  $event = $event ?? null;
@endphp

<div class="mx-auto max-w-5xl space-y-4">

  <div class="{{ $card }}">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="text-base font-black text-slate-900">Editar tenant</h2>
        <p class="mt-1 text-sm text-slate-600">Administra tenant, usuario cliente y el evento base.</p>
      </div>

      <div class="flex flex-wrap gap-2">
        <a class="{{ $btnSoft }}" href="{{ route('admin.tenants.index') }}">← Volver</a>

        <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}" onsubmit="return confirm('¿Seguro?');">
          @csrf
          <button type="submit" class="{{ $tenant->is_active ? $btnDangerSoft : $btnSoft }}">
            {{ $tenant->is_active ? '⛔ Desactivar' : '✅ Activar' }}
          </button>
        </form>

        <button type="button" class="{{ $btnSoft }}" data-copy="{{ $subdomain }}">📋 Copiar subdominio</button>
        <a class="{{ $btnSoft }}" target="_blank" rel="noopener" href="https://{{ $subdomain }}">🔗 Abrir</a>
      </div>
    </div>

    {{-- Subdominio --}}
    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
      <div class="text-sm font-black text-slate-900">Subdominio</div>
      <div class="mt-2 inline-flex items-center rounded-xl bg-white px-3 py-2 text-xs font-black text-slate-700 shadow-sm">
        {{ $subdomain }}
      </div>
      <p class="{{ $hint }}">Cambiar el slug cambia la URL del cliente.</p>
    </div>

    <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" class="mt-5 space-y-6">
      @csrf
      @method('PUT')

      {{-- ======================
          TENANT
      ====================== --}}
      <section class="rounded-2xl border border-slate-200 bg-white p-4">
        <h3 class="text-sm font-black text-slate-900">Tenant (cliente)</h3>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <label class="{{ $label }}">Nombre</label>
            <input name="name" value="{{ old('name', $tenant->name) }}" class="{{ $input }}" />
          </div>

          <div>
            <label class="{{ $label }}">Slug</label>
            <input name="slug" value="{{ old('slug', $tenant->slug) }}" class="{{ $input }}" />
            <p class="{{ $hint }}">Se usará como: <span class="font-black">{slug}.{{ $domain }}</span></p>
          </div>

          <div class="lg:col-span-2">
            <label class="{{ $label }}">Estado</label>
            <label class="mt-2 inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3">
              <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300"
                @checked(old('is_active', (int)$tenant->is_active) == 1)>
              <span class="text-sm font-extrabold text-slate-900">Activo</span>
              <span class="text-xs text-slate-500">(si está apagado, el cliente no debería poder usar panel)</span>
            </label>
          </div>

          <div class="lg:col-span-2">
            <label class="{{ $label }}">Plan (opcional)</label>
            @php $tier = old('tier', $tenant->tier ?? null); @endphp
            <select name="tier" class="{{ $input }}">
              <option value="" @selected(!$tier)>standard</option>
              <option value="trial" @selected($tier==='trial')>trial</option>
              <option value="demo" @selected($tier==='demo')>demo</option>
              <option value="admin" @selected($tier==='admin')>admin</option>
            </select>
            <p class="{{ $hint }}">Si tu tabla no tiene columna <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">tier</code>, este campo se ignora.</p>
          </div>
        </div>
      </section>

      {{-- ======================
          USUARIO CLIENTE
      ====================== --}}
      <section class="rounded-2xl border border-slate-200 bg-white p-4">
        <h3 class="text-sm font-black text-slate-900">Usuario (cliente)</h3>
        <p class="mt-1 text-sm text-slate-600">Editar datos del usuario que usa el panel.</p>

        @if(!$clientUser)
          <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            No encontré el usuario cliente asociado a este tenant. (El controller debe pasar <code class="rounded bg-amber-100 px-1">clientUser</code>).
          </div>
        @else
          <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div>
              <label class="{{ $label }}">Nombre</label>
              <input name="client_name" value="{{ old('client_name', $clientUser->name) }}" class="{{ $input }}" />
            </div>

            <div>
              <label class="{{ $label }}">Email</label>
              <input type="email" name="client_email" value="{{ old('client_email', $clientUser->email) }}" class="{{ $input }}" />
            </div>

            <div class="lg:col-span-2">
              <label class="{{ $label }}">Nueva contraseña (opcional)</label>
              <input type="password" name="client_password" placeholder="mínimo 8 caracteres" class="{{ $input }}" />
              <p class="{{ $hint }}">Si lo dejas vacío, no cambia. Si lo llenas, se resetea.</p>
            </div>
          </div>
        @endif
      </section>

      {{-- ======================
          EVENTO BASE
      ====================== --}}
      <section class="rounded-2xl border border-slate-200 bg-white p-4">
        <h3 class="text-sm font-black text-slate-900">Evento base</h3>
        <p class="mt-1 text-sm text-slate-600">Este es el evento que verá el cliente al entrar.</p>

        @if(!$event)
          <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            No encontré evento para este tenant. (El controller debe pasar <code class="rounded bg-amber-100 px-1">event</code>).
          </div>
        @else
          <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div>
              <label class="{{ $label }}">Tipo</label>
              @php $evType = old('event_type', $event->type ?? 'xv'); @endphp
              <select name="event_type" class="{{ $input }}">
                <option value="xv" @selected($evType==='xv')>XV años</option>
                <option value="boda" @selected($evType==='boda')>Boda</option>
                <option value="bautizo" @selected($evType==='bautizo')>Bautizo</option>
                <option value="cumple" @selected($evType==='cumple')>Cumpleaños</option>
              </select>
            </div>

            <div>
              <label class="{{ $label }}">Título</label>
              <input name="event_title" value="{{ old('event_title', $event->title) }}" class="{{ $input }}" />
            </div>

            <div>
              <label class="{{ $label }}">Nombre celebrante</label>
              <input name="celebrant_name" value="{{ old('celebrant_name', $event->celebrant_name) }}" class="{{ $input }}" />
            </div>

            <div>
              <label class="{{ $label }}">Fecha y hora</label>
              <input type="datetime-local" name="starts_at"
                value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\TH:i')) }}"
                class="{{ $input }}" />
            </div>

            <div class="lg:col-span-2">
              <label class="{{ $label }}">Lugar (opcional)</label>
              <input name="venue" value="{{ old('venue', $event->venue) }}" class="{{ $input }}" />
            </div>
          </div>
        @endif
      </section>

      <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-between">
        <a class="{{ $btnSoft }}" href="{{ route('admin.tenants.index') }}">← Volver</a>
        <button type="submit" class="{{ $btnPrimary }}">💾 Guardar cambios</button>
      </div>
    </form>
  </div>

</div>
@endsection

@section('scripts')
<script>
  (function(){
    function copyText(t){
      if (!t) return;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(t);
      } else {
        const ta = document.createElement('textarea');
        ta.value = t;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
      }
    }

    document.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-copy]');
      if (!btn) return;
      copyText(btn.getAttribute('data-copy'));
      btn.classList.add('opacity-70');
      setTimeout(()=>btn.classList.remove('opacity-70'), 250);
    });
  })();
</script>
@endsection
