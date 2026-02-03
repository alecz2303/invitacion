@extends('layouts.app-simple')

@section('title','Admin · Tenants')
@section('h1','Admin')
@section('subtitle','Tenants (clientes) · Crea y gestiona cuentas')

@section('content')
@php
  $card = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm';
  $btnBase = 'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-black shadow-sm transition focus:outline-none focus:ring-4';
  $btnPrimary = $btnBase.' border border-slate-900 bg-slate-900 text-white hover:bg-slate-800 focus:ring-slate-200';
  $btnSoft = $btnBase.' border border-slate-200 bg-white text-slate-900 hover:bg-slate-50 focus:ring-slate-100';
  $btnDangerSoft = $btnBase.' border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 focus:ring-rose-100';
  $badge = 'inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-black';

  $domain = 'events.partyx.com.mx';

  $planBadge = function($t) use ($badge){
    $raw = $t->tier ?? $t->plan ?? $t->mode ?? $t->kind ?? $t->type ?? null;
    $val = is_string($raw) ? strtolower(trim($raw)) : null;

    if ($val === 'admin') return '<span class="'.$badge.' border-purple-200 bg-purple-50 text-purple-800">👑 Admin</span>';
    if ($val === 'demo')  return '<span class="'.$badge.' border-sky-200 bg-sky-50 text-sky-800">🧪 Demo</span>';
    if ($val === 'trial') return '<span class="'.$badge.' border-amber-200 bg-amber-50 text-amber-800">⏳ Trial</span>';

    return '<span class="'.$badge.' border-slate-200 bg-slate-50 text-slate-700">🏷️ Standard</span>';
  };
@endphp

<div class="mx-auto max-w-6xl space-y-4">
  <div class="{{ $card }}">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="text-base font-black text-slate-900">Tenants</h2>
        <p class="mt-1 text-sm text-slate-600">Cada tenant corresponde a un cliente (subdominio independiente).</p>
      </div>

      <a class="{{ $btnPrimary }}" href="{{ route('admin.tenants.create') }}">
        + Crear tenant
      </a>
    </div>

    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex flex-wrap gap-2">
        <button type="button" id="copyVisibleBtn" class="{{ $btnSoft }}">
          📋 Copiar subdominios (visibles)
        </button>
      </div>

      <div class="w-full sm:w-96">
        <label class="block text-sm font-extrabold text-slate-800">Buscar</label>
        <input
          id="filterInput"
          class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
          placeholder="Buscar por nombre o slug…"
        />
        <p class="mt-1 text-xs text-slate-500">Filtra solo lo visible (no afecta paginación).</p>
      </div>
    </div>

    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">ID</th>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">Cliente</th>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">Subdominio</th>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">Plan</th>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">Estado</th>
            <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-600">Creado</th>
            <th class="px-4 py-3 text-right text-xs font-black uppercase tracking-wider text-slate-600">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200 bg-white" id="tenantsTbody">
          @forelse($tenants as $t)
            @php
              $subdomain = $t->slug.'.'.$domain;
              $statusClass = $t->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800';
              $statusText  = $t->is_active ? '✅ Activo' : '⛔ Inactivo';
            @endphp

            <tr class="hover:bg-slate-50/60"
                data-row
                data-name="{{ strtolower((string)($t->name ?? '')) }}"
                data-slug="{{ strtolower((string)($t->slug ?? '')) }}"
            >
              <td class="px-4 py-4 text-sm font-semibold text-slate-700">{{ $t->id }}</td>

              <td class="px-4 py-4">
                <div class="font-black text-slate-900">{{ $t->name }}</div>
                <div class="mt-1 text-xs text-slate-500">slug: <span class="font-semibold">{{ $t->slug }}</span></div>
              </td>

              <td class="px-4 py-4">
                <div class="flex flex-col gap-2">
                  <span class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-2 text-xs font-black text-slate-700">
                    {{ $subdomain }}
                  </span>

                  <div class="flex flex-wrap gap-2">
                    <button type="button"
                      class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-900 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                      data-copy="{{ $subdomain }}">
                      📋 Copiar
                    </button>

                    <a class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-900 shadow-sm hover:bg-slate-50"
                      target="_blank" rel="noopener" href="https://{{ $subdomain }}">
                      🔗 Abrir
                    </a>
                  </div>
                </div>
              </td>

              <td class="px-4 py-4">{!! $planBadge($t) !!}</td>

              <td class="px-4 py-4">
                <span class="{{ $badge }} {{ $statusClass }}">{{ $statusText }}</span>
              </td>

              <td class="px-4 py-4 text-sm text-slate-500">
                {{ $t->created_at?->format('Y-m-d H:i') }}
              </td>

              <td class="px-4 py-4 text-right">
                <div class="flex flex-wrap justify-end gap-2">
                  <a class="{{ $btnSoft }}" href="{{ route('admin.tenants.edit', $t) }}">✏️ Editar</a>

                  <form method="POST" action="{{ route('admin.tenants.toggle', $t) }}" onsubmit="return confirm('¿Seguro?');">
                    @csrf
                    <button type="submit" class="{{ $t->is_active ? $btnDangerSoft : $btnSoft }}">
                      {{ $t->is_active ? '⛔ Desactivar' : '✅ Activar' }}
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">
                No hay tenants aún.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $tenants->links() }}
    </div>
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

    const filterInput = document.getElementById('filterInput');
    const rows = Array.from(document.querySelectorAll('#tenantsTbody [data-row]'));

    function applyFilter(){
      const q = (filterInput?.value || '').trim().toLowerCase();
      rows.forEach(r => {
        const name = r.getAttribute('data-name') || '';
        const slug = r.getAttribute('data-slug') || '';
        r.style.display = (!q || name.includes(q) || slug.includes(q)) ? '' : 'none';
      });
    }

    if (filterInput) filterInput.addEventListener('input', applyFilter);

    const copyVisibleBtn = document.getElementById('copyVisibleBtn');
    if (copyVisibleBtn) {
      copyVisibleBtn.addEventListener('click', () => {
        const visible = rows.filter(r => r.style.display !== 'none');
        const subs = visible.map(r => {
          const btn = r.querySelector('[data-copy]');
          return btn ? btn.getAttribute('data-copy') : '';
        }).filter(Boolean);

        if (!subs.length) return;
        copyText(subs.join('\n'));
        copyVisibleBtn.textContent = '✅ Copiado';
        setTimeout(()=>copyVisibleBtn.textContent='📋 Copiar subdominios (visibles)', 900);
      });
    }
  })();
</script>
@endsection
