@extends('layouts.app-simple')

@section('title','Panel · Invitados')
@section('h1','Panel')
@section('subtitle','Invitados · Genera links para WhatsApp')

@section('content')
@php
  $card = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm';
  $btnBase = 'inline-flex items-center justify-center rounded-xl px-3 py-2 text-sm font-extrabold shadow-sm transition focus:outline-none focus:ring-4';
  $btnPrimary = $btnBase.' border border-slate-900 bg-slate-900 text-white hover:bg-slate-800 focus:ring-slate-200';
  $btnSoft = $btnBase.' border border-slate-200 bg-white text-slate-900 hover:bg-slate-50 focus:ring-slate-100';
  $btnDanger = $btnBase.' border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 focus:ring-rose-100';

  $pill = 'inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-black';
@endphp

<div class="mx-auto max-w-6xl space-y-4">

  {{-- Widgets --}}
  <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
    <div class="{{ $card }}">
      <div class="text-xs font-black text-slate-500">Invitaciones</div>
      <div class="mt-2 text-3xl font-black text-slate-900">{{ $totalInvites }}</div>
      <div class="mt-1 text-xs text-slate-500">Total registros</div>
    </div>

    <div class="{{ $card }}">
      <div class="text-xs font-black text-slate-500">Asientos</div>
      <div class="mt-2 text-3xl font-black text-slate-900">{{ $totalSeats }}</div>
      <div class="mt-1 text-xs text-slate-500">Sumatoria seats</div>
    </div>

    <div class="{{ $card }}">
      <div class="text-xs font-black text-slate-500">Confirmados</div>
      <div class="mt-2 text-3xl font-black text-emerald-700">{{ $confirmed }}</div>
      <div class="mt-1 text-xs text-slate-500">{{ $seatsConfirmed }} asientos confirmados</div>
    </div>

    <div class="{{ $card }}">
      <div class="text-xs font-black text-slate-500">Pendientes</div>
      <div class="mt-2 text-3xl font-black text-amber-700">{{ $active }}</div>
      <div class="mt-1 text-xs text-slate-500">{{ $seatsPending }} asientos pendientes</div>
    </div>
  </div>

  <div class="{{ $card }}">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="text-base font-black text-slate-900">Invitados</h2>
        <p class="mt-1 text-sm text-slate-600">
          Subdominio: <span class="font-black">{{ $tenant->slug }}.events.partyx.com.mx</span>
        </p>
      </div>

      <div class="flex flex-wrap gap-2">
        <a class="{{ $btnPrimary }}" href="{{ route('panel.invites.create') }}">+ Nuevo invitado</a>
        <a class="{{ $btnSoft }}" href="{{ route('panel.event.edit') }}">⚙️ Editar evento</a>
      </div>
    </div>

    {{-- Buscador / filtros --}}
    <form class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3" method="GET" action="{{ route('panel.invites.index') }}">
      <div class="sm:col-span-2">
        <label class="block text-sm font-extrabold text-slate-800">Buscar invitado</label>
        <input
          name="q"
          value="{{ $q ?? '' }}"
          placeholder="Ej. Familia Rueda…"
          class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
        />
      </div>

      <div>
        <label class="block text-sm font-extrabold text-slate-800">Estado</label>
        <select
          name="status"
          class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
        >
          <option value="" @selected(($status ?? '')==='')>Todos</option>
          <option value="ACTIVE" @selected(($status ?? '')==='ACTIVE')>Pendiente</option>
          <option value="CONFIRMED" @selected(($status ?? '')==='CONFIRMED')>Confirmado</option>
          <option value="DECLINED" @selected(($status ?? '')==='DECLINED')>Declinó</option>
        </select>
      </div>

      <div class="sm:col-span-3 flex justify-end gap-2">
        <a class="{{ $btnSoft }}" href="{{ route('panel.invites.index') }}">Limpiar</a>
        <button class="{{ $btnPrimary }}" type="submit">Aplicar</button>
      </div>
    </form>

    {{-- Tabla --}}
    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">Invitado</th>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">Lugares</th>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">Estado</th>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">Link</th>
            <th class="px-4 py-3 text-right text-xs font-black uppercase tracking-wider text-slate-600">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 bg-white">
          @forelse($invites as $inv)
            @php
              $hash = \App\Services\InviteHash::encode($tenant, $inv->id);
              $url  = $base . "/i/" . $hash;

              $statusPill = match($inv->status){
                'CONFIRMED' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                'DECLINED'  => 'border-rose-200 bg-rose-50 text-rose-800',
                default     => 'border-amber-200 bg-amber-50 text-amber-800',
              };

              $statusText = match($inv->status){
                'CONFIRMED' => '✅ Confirmada',
                'DECLINED'  => '❌ No asistirá',
                default     => '🟡 Pendiente',
              };
            @endphp

            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-4">
                <div class="font-black text-slate-900">{{ $inv->guest_name }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ $inv->event?->title }}</div>
              </td>

              <td class="px-4 py-4 text-sm font-semibold text-slate-700">
                {{ $inv->seats }}
              </td>

              <td class="px-4 py-4">
                <span class="{{ $pill }} {{ $statusPill }}">{{ $statusText }}</span>
              </td>

              <td class="px-4 py-4">
                <input
                  class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 shadow-sm"
                  readonly
                  value="{{ $url }}"
                  onclick="this.select()"
                >
                <div class="mt-2 flex flex-wrap gap-2">
                  <button class="{{ $btnSoft }}" type="button" onclick="navigator.clipboard.writeText(@js($url))">Copiar</button>
                  <a class="{{ $btnSoft }}" target="_blank" rel="noopener"
                     href="https://wa.me/?text={{ urlencode('Hola! Te comparto tu invitación: '.$url) }}">
                    WhatsApp
                  </a>
                  <a class="{{ $btnSoft }}" target="_blank" rel="noopener" href="{{ $url }}">Abrir</a>
                </div>
              </td>

              <td class="px-4 py-4 text-right">
                <div class="flex flex-wrap justify-end gap-2">
                  <a class="{{ $btnSoft }}" href="{{ route('panel.invites.edit', $inv) }}">✏️ Editar</a>

                  <form method="POST" action="{{ route('panel.invites.destroy', $inv) }}"
                        onsubmit="return confirm('¿Eliminar a {{ $inv->guest_name }}?');">
                    @csrf
                    @method('DELETE')
                    <button class="{{ $btnDanger }}" type="submit">🗑️ Borrar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">
                Aún no hay invitados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
      {{ $invites->links() }}
    </div>
  </div>
</div>
@endsection
