@extends('layouts.app-simple')

@section('title','Admin · Tenants')
@section('h1','Admin')
@section('subtitle','Tenants (clientes) · Crea y gestiona cuentas')

@section('content')
  <div class="card">
    <div class="row" style="justify-content:space-between;align-items:center">
      <div>
        <div style="font-weight:900">Tenants</div>
        <div class="muted small">Cada tenant corresponde a un cliente (subdominio).</div>
      </div>
      <a class="btn btnPrimary" href="{{ route('admin.tenants.create') }}">+ Crear tenant</a>
    </div>

    <div style="margin-top:12px;overflow:auto">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Slug</th>
            <th>Activo</th>
            <th>Creado</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tenants as $t)
            <tr>
              <td>{{ $t->id }}</td>
              <td><b>{{ $t->name }}</b></td>
              <td>{{ $t->slug }}.events.partyx.com.mx</td>
              <td>{{ $t->is_active ? '✅' : '⛔' }}</td>
              <td class="muted">{{ $t->created_at?->format('Y-m-d H:i') }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="muted">No hay tenants aún.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:12px">
      {{ $tenants->links() }}
    </div>
  </div>
@endsection
