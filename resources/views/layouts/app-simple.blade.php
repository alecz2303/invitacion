<!doctype html>
<html lang="es-MX">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'PartyX Events')</title>

  {{-- Breeze / Tailwind --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @yield('head')
</head>

<body class="bg-slate-50 text-slate-900">
  <div class="mx-auto max-w-6xl px-4 py-6">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="text-xl font-black">@yield('h1','PartyX Events')</div>
        <div class="text-sm text-slate-600">@yield('subtitle')</div>
      </div>

      <div class="flex items-center gap-2 flex-wrap">
        @auth
          <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
            👤 <span class="font-semibold">{{ auth()->user()->name }}</span>
            <span class="text-slate-500">({{ auth()->user()->role }})</span>
          </span>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
              type="submit"
              class="inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-extrabold text-rose-700 hover:bg-rose-100"
            >
              Salir
            </button>
          </form>
        @endauth
      </div>
    </div>

    @if (session('status'))
      <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
        <b>Revisa:</b>
        <ul class="mt-2 list-disc space-y-1 pl-5">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </div>

  @yield('scripts')
</body>
</html>
