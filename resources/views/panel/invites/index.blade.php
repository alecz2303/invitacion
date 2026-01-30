@extends('layouts.app-simple')

@section('title','Panel · Invitados')
@section('h1','Panel')
@section('subtitle','Invitados · Genera links para WhatsApp')

@section('content')
  <div class="card">
    <div class="row" style="justify-content:space-between;align-items:center">
      <div>
        <div style="font-weight:900">Invitados</div>
        <div class="muted small">
          Subdominio: <b>{{ $tenant->slug }}.events.partyx.com.mx</b>
        </div>
      </div>
      <a class="btn btnPrimary" href="{{ route('panel.invites.create') }}">+ Nuevo invitado</a>
      <a class="btn" href="{{ route('panel.event.edit') }}">⚙️ Editar evento</a>
    </div>

    <div style="margin-top:12px;overflow:auto">
      <table>
        <thead>
          <tr>
            <th>Invitado</th>
            <th>Lugares</th>
            <th>Estado</th>
            <th>Link</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invites as $inv)
            @php
              $hash = \App\Services\InviteHash::encode($tenant, $inv->id);
              $url = $base . "/i/" . $hash;
            @endphp
            <tr>
              <td>
                <b>{{ $inv->guest_name }}</b>
                <div class="muted small">{{ $inv->event?->title }}</div>
              </td>
              <td>{{ $inv->seats }}</td>
              <td>
                @if($inv->status==='ACTIVE') 🟡 Activa
                @elseif($inv->status==='CONFIRMED') ✅ Confirmada
                @elseif($inv->status==='DECLINED') ❌ No asistirá
                @else {{ $inv->status }}
                @endif
              </td>
              <td>
                <input class="small" readonly value="{{ $url }}" onclick="this.select()" />
                <div class="row" style="margin-top:6px">
                  <button class="btn" type="button" onclick="navigator.clipboard.writeText(@js($url))">Copiar</button>
                  <a class="btn" target="_blank"
                     href="https://wa.me/?text={{ urlencode('Hola! Te comparto tu invitación: '.$url) }}">
                    WhatsApp
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="muted">Aún no hay invitados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:12px">
      {{ $invites->links() }}
    </div>
  </div>
@endsection
