@extends('layouts.app-simple')

@section('title','Panel · Evento')
@section('h1','Panel')
@section('subtitle','Editar evento · Foto hero · Música · Galería')

@section('content')
@php
  // ====== Itinerary desde theme (array) ======
  $itinerary = $theme['itinerary'] ?? [];
  if (is_string($itinerary)) {
    $tmp = json_decode($itinerary, true);
    if (json_last_error() === JSON_ERROR_NONE) $itinerary = $tmp;
  }
  if (!is_array($itinerary)) $itinerary = [];

  if (count($itinerary) === 0) {
    $itinerary = [
      ['time'=>'6:00 PM','name'=>'Ceremonia Religiosa','icon_url'=>null],
      ['time'=>'7:35 PM','name'=>'Recepción / Bienvenida','icon_url'=>null],
    ];
  }

  $gifts = $theme['gifts'] ?? [];
  if (is_string($gifts)) {
    $tmp = json_decode($gifts, true);
    if (json_last_error() === JSON_ERROR_NONE) $gifts = $tmp;
  }
  if (!is_array($gifts)) $gifts = [];

  if (count($gifts) === 0) {
    $gifts = [
      ['title'=>'Mesa de Regalos','code'=>'Num. 00000','url'=>'','icon_url'=>'','btn'=>'VER'],
      ['title'=>'Lluvia de Sobres','code'=>'El día del evento','url'=>'','icon_url'=>'','btn'=>'VER'],
    ];
  }

  // keys que no se renderizan desde schema
  $skipSchemaKeys = ['itinerary','gifts'];

  // Helper: valor del field (old() primero)
  $fieldValue = function($key) use ($theme){
    return old('theme.'.$key, $theme[$key] ?? null);
  };

  $asJsonPretty = function($val){
    if (is_string($val)) return $val;
    if (is_array($val) || is_object($val)) {
      return json_encode($val, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }
    return '';
  };

  // ===============================
  // Tabs del schema: tab -> group -> fields
  // ===============================
  $tabs = [];
  foreach(($schema ?? []) as $key => $meta){
    if (in_array($key, $skipSchemaKeys, true)) continue;

    $tab = $meta['tab'] ?? 'Tema';
    $group = $meta['group'] ?? 'General';

    if (!isset($tabs[$tab])) $tabs[$tab] = [];
    if (!isset($tabs[$tab][$group])) $tabs[$tab][$group] = [];

    $tabs[$tab][$group][$key] = $meta;
  }

  // Ordenar fields dentro de cada group por order y luego label
  foreach($tabs as $tName => $tGroups){
    foreach($tGroups as $gName => $fields){
      uasort($fields, function($a, $b){
        $oa = $a['order'] ?? 999;
        $ob = $b['order'] ?? 999;
        if($oa !== $ob) return $oa <=> $ob;
        $la = $a['label'] ?? '';
        $lb = $b['label'] ?? '';
        return strcmp($la, $lb);
      });
      $tabs[$tName][$gName] = $fields;
    }
  }

  // Tabs principales del panel
  $mainTabs = [
    'Evento'     => 'evento',
    'Itinerario' => 'itinerary',
    'Tema'       => 'theme',
    'Regalos'    => 'gifts',
    'Hero'       => 'hero',
    'Música'     => 'music',
    'Galería'    => 'gallery',
  ];

  $activeTab = request('tab', 'evento');
  $themeTab = request('theme_tab', array_key_first($tabs) ?? 'Básico');

  // Para que al POST conserve el tab y theme_tab
  $activeTabFromOld = old('_tab');
  if ($activeTabFromOld) $activeTab = $activeTabFromOld;

  $themeTabFromOld = old('_theme_tab');
  if ($themeTabFromOld) $themeTab = $themeTabFromOld;
@endphp

<div class="mx-auto max-w-6xl">

  {{-- Tabs principales --}}
  <div class="mb-4 overflow-x-auto">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <a
        href="{{ route('panel.invites.index') }}"
        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-extrabold text-slate-900 shadow-sm hover:bg-slate-50"
      >
        ← Volver a invitados
      </a>

      <div class="overflow-x-auto">
        <div class="inline-flex gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
          @foreach($mainTabs as $label => $slug)
            @php $isActive = ($activeTab === $slug); @endphp
            <a
              href="{{ request()->fullUrlWithQuery(['tab' => $slug] + ($slug === 'theme' ? ['theme_tab' => $themeTab] : [])) }}"
              class="whitespace-nowrap rounded-xl px-3 py-2 text-sm font-extrabold transition
                {{ $isActive ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 hover:bg-slate-50' }}"
            >
              {{ $label }}
            </a>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- FORM PRINCIPAL (todo menos galería) --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <form id="eventMainForm" method="POST" action="{{ route('panel.event.update') }}" enctype="multipart/form-data" class="space-y-6">
      @csrf

      {{-- Preservar tab actual al guardar --}}
      <input type="hidden" name="_tab" value="{{ $activeTab }}">
      <input type="hidden" name="_theme_tab" value="{{ $themeTab }}">

      {{-- Si NO estamos en el tab Evento, manda los campos base ocultos para que pasen validación y no se borren --}}
      @if($activeTab !== 'evento')
        <input type="hidden" name="title" value="{{ old('title', $event->title) }}">
        <input type="hidden" name="celebrant_name" value="{{ old('celebrant_name', $event->celebrant_name) }}">
        <input type="hidden" name="starts_at" value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\TH:i')) }}">
        <input type="hidden" name="venue" value="{{ old('venue', $event->venue) }}">
        <input type="hidden" name="maps_url" value="{{ old('maps_url', $event->maps_url) }}">
        <input type="hidden" name="dress_code" value="{{ old('dress_code', $event->dress_code) }}">
        <input type="hidden" name="message" value="{{ old('message', $event->message) }}">
      @endif

      {{-- =====================
           TAB: Evento
      ====================== --}}
      @if($activeTab === 'evento')
        <section class="rounded-2xl border border-slate-200 bg-white p-4">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <h2 class="text-base font-black text-slate-900">Datos del evento</h2>
              <p class="mt-1 text-sm text-slate-600">Información básica del evento.</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700">
              Tipo: <span class="font-black">{{ $type ?? 'xv' }}</span>
            </span>
          </div>

          <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
            @php
              $inputBase = 'mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100';
              $labelBase = 'block text-sm font-extrabold text-slate-800';
            @endphp

            <div>
              <label class="{{ $labelBase }}">Título</label>
              <input name="title" value="{{ old('title', $event->title) }}" class="{{ $inputBase }}" />
            </div>

            <div>
              <label class="{{ $labelBase }}">Nombre celebrante</label>
              <input name="celebrant_name" value="{{ old('celebrant_name', $event->celebrant_name) }}" class="{{ $inputBase }}" />
            </div>

            <div>
              <label class="{{ $labelBase }}">Fecha y hora</label>
              <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\TH:i')) }}" class="{{ $inputBase }}" />
              <p class="mt-1 text-xs text-slate-500">Hora local (México).</p>
            </div>

            <div>
              <label class="{{ $labelBase }}">Lugar</label>
              <input name="venue" value="{{ old('venue', $event->venue) }}" class="{{ $inputBase }}" />
            </div>

            <div class="lg:col-span-2">
              <label class="{{ $labelBase }}">Mapa (link de Google Maps)</label>
              <input
                name="maps_url"
                value="{{ old('maps_url', $event->maps_url) }}"
                placeholder="https://maps.app.goo.gl/... o https://www.google.com/maps?q=..."
                class="{{ $inputBase }}"
              />
              <p class="mt-1 text-xs text-slate-500">Pega el link directo del lugar.</p>
            </div>

            <div>
              <label class="{{ $labelBase }}">Vestimenta</label>
              <input name="dress_code" value="{{ old('dress_code', $event->dress_code) }}" class="{{ $inputBase }}" />
            </div>

            <div>
              <label class="{{ $labelBase }}">Mensaje</label>
              <input name="message" value="{{ old('message', $event->message) }}" class="{{ $inputBase }}" />
            </div>
          </div>
        </section>

        <div class="flex justify-end">
          <button class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-100 px-4 py-2.5 text-sm font-black text-amber-900 shadow-sm hover:bg-amber-200" type="submit">
            Guardar cambios
          </button>
        </div>
      @endif

      {{-- =====================
           TAB: Itinerario (Drag & Drop + Ordenar por hora)
      ====================== --}}
      @if($activeTab === 'itinerary')
        <section class="rounded-2xl border border-slate-200 bg-white p-4">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <h2 class="text-base font-black text-slate-900">Itinerario</h2>
              <p class="mt-1 text-sm text-slate-600">Agrega actividades. Puedes arrastrar para reordenar.</p>
            </div>

            <div class="flex flex-wrap gap-2">
              <button
                type="button"
                id="sortItineraryBtn"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-extrabold text-slate-900 shadow-sm hover:bg-slate-50"
                title="Ordena por hora (si detecta una hora válida)"
              >
                ⏱️ Ordenar por hora
              </button>

              <button
                type="button"
                id="addItineraryBtn"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-extrabold text-slate-900 shadow-sm hover:bg-slate-50"
              >
                + Agregar actividad
              </button>
            </div>
          </div>

          <div id="itineraryWrap" class="mt-4 space-y-3">
            @foreach($itinerary as $idx => $row)
              <div class="it-row rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" draggable="true">
                <div class="mb-3 flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="drag-handle cursor-move select-none rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-black text-slate-700">☰</span>
                    <span class="text-xs font-semibold text-slate-500">Arrastra para reordenar</span>
                  </div>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                  <div>
                    <label class="block text-sm font-extrabold text-slate-800">Hora</label>
                    <input name="theme[itinerary][{{ $idx }}][time]" value="{{ $row['time'] ?? '' }}" placeholder="8:30 PM"
                      class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                  </div>

                  <div>
                    <label class="block text-sm font-extrabold text-slate-800">Actividad</label>
                    <input name="theme[itinerary][{{ $idx }}][name]" value="{{ $row['name'] ?? '' }}" placeholder="Cena / Vals / Baile sorpresa"
                      class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                  </div>

                  <div class="lg:col-span-2">
                    <label class="block text-sm font-extrabold text-slate-800">Ícono (URL opcional)</label>
                    <input name="theme[itinerary][{{ $idx }}][icon_url]" value="{{ $row['icon_url'] ?? '' }}" placeholder="https://.../icon.png"
                      class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                  </div>
                </div>

                <div class="mt-3 flex justify-end">
                  <button type="button"
                    class="inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-extrabold text-rose-700 hover:bg-rose-100"
                    onclick="this.closest('.it-row').remove()">
                    Quitar
                  </button>
                </div>
              </div>
            @endforeach
          </div>
        </section>

        <div class="flex justify-end">
          <button class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-100 px-4 py-2.5 text-sm font-black text-amber-900 shadow-sm hover:bg-amber-200" type="submit">
            Guardar cambios
          </button>
        </div>
      @endif

      {{-- =====================
           TAB: Tema (sub-tabs desde config) + Acordeón por group
      ====================== --}}
      @if($activeTab === 'theme')
        <section class="rounded-2xl border border-slate-200 bg-white p-4">
          <div>
            <h2 class="text-base font-black text-slate-900">Tema / Plantilla</h2>
            <p class="mt-1 text-sm text-slate-600">
              Estos campos se guardan en <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">events.theme</code>.
            </p>
          </div>

          {{-- Sub-tabs (desde schema: meta.tab) --}}
          <div class="mt-4 overflow-x-auto">
            <div class="inline-flex gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
              @foreach(array_keys($tabs) as $tName)
                @php $isActive = ($themeTab === $tName); @endphp
                <a
                  href="{{ request()->fullUrlWithQuery(['tab'=>'theme','theme_tab'=>$tName]) }}"
                  class="whitespace-nowrap rounded-xl px-3 py-2 text-sm font-extrabold transition
                    {{ $isActive ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 hover:bg-slate-50' }}"
                >
                  {{ $tName }}
                </a>
              @endforeach
            </div>
          </div>

          {{-- Acordeón de grupos/fields del themeTab activo --}}
          <div class="mt-4 space-y-3">
            @foreach(($tabs[$themeTab] ?? []) as $groupName => $fields)
              @php
                $accId = 'acc_' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($themeTab.'_'.$groupName));
                $openDefault = true;
              @endphp

              <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <button
                  type="button"
                  class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-slate-50"
                  data-acc-btn="{{ $accId }}"
                  aria-expanded="{{ $openDefault ? 'true' : 'false' }}"
                >
                  <div>
                    <div class="text-sm font-black text-slate-900">{{ $groupName }}</div>
                    <div class="text-xs text-slate-500">Sección de {{ $themeTab }}</div>
                  </div>
                  <span class="text-slate-500 font-black" data-acc-ico="{{ $accId }}">−</span>
                </button>

                <div class="{{ $openDefault ? '' : 'hidden' }} px-4 pb-4" data-acc-panel="{{ $accId }}">
                  <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                    @foreach($fields as $key => $meta)
                      @php
                        $label = $meta['label'] ?? $key;
                        $typeField = $meta['type'] ?? 'text';
                        $ph = $meta['placeholder'] ?? '';
                        $help = $meta['help'] ?? null;
                        $val = $fieldValue($key);

                        $isTextarea = $typeField === 'textarea';
                        $isJson = $typeField === 'json';
                        $isImage = $typeField === 'image';
                        $isColor = $typeField === 'color';
                        $inputType = in_array($typeField, ['text','url','number','color']) ? $typeField : 'text';
                      @endphp

                      <div class="{{ $isImage ? 'lg:col-span-2' : '' }}">
                        <label class="block text-sm font-extrabold text-slate-800">{{ $label }}</label>

                        @if($isImage)
                          <div class="mt-2 grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div class="space-y-2">
                              <input
                                type="file"
                                name="theme_files[{{ $key }}]"
                                accept="image/*"
                                class="block w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-extrabold hover:bg-slate-50"
                              />

                              <p class="text-xs text-slate-500">
                                Se guarda en <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">events.theme.{{ $key }}</code> como ruta del archivo.
                              </p>

                              @if($help)
                                <p class="text-xs text-slate-500">{{ $help }}</p>
                              @endif

                              @if(!empty($val))
                                <label class="mt-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                  <input type="checkbox" name="theme_remove[{{ $key }}]" value="1" class="h-4 w-4 rounded border-slate-300">
                                  Quitar imagen
                                </label>
                              @endif
                            </div>

                            <div>
                              @if(!empty($val))
                                <p class="text-xs text-slate-500">Actual:</p>
                                <img
                                  src="{{ str_starts_with($val, 'http') ? $val : asset('storage/'.$val) }}"
                                  class="mt-2 w-full max-w-sm rounded-2xl border border-slate-200 object-cover shadow-sm"
                                  alt=""
                                >
                              @else
                                <p class="mt-2 text-xs text-slate-500">No hay imagen cargada.</p>
                              @endif
                            </div>
                          </div>

                        @elseif($isTextarea)
                          <textarea
                            name="theme[{{ $key }}]"
                            rows="3"
                            placeholder="{{ $ph }}"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                          >{{ is_array($val) ? '' : (string)$val }}</textarea>

                          @if($help)
                            <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
                          @endif

                        @elseif($isJson)
                          <textarea
                            name="theme[{{ $key }}]"
                            rows="8"
                            placeholder='{{ $ph ?: "[{...}]" }}'
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 font-mono text-sm text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                          >{{ $asJsonPretty($val) }}</textarea>

                          @if($help)
                            <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
                          @endif

                        @else
                          <div class="mt-2 flex items-center gap-3">
                            <input
                              name="theme[{{ $key }}]"
                              type="{{ $inputType }}"
                              value="{{ is_array($val) ? '' : (string)$val }}"
                              placeholder="{{ $ph }}"
                              class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100 {{ $isColor ? 'max-w-[220px]' : '' }}"
                            />

                            @if($isColor)
                              <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
                                {{ is_array($val) ? '' : ($val ?: '') }}
                              </span>
                            @endif
                          </div>

                          @if($help)
                            <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
                          @endif
                        @endif
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </section>

        <div class="flex justify-end">
          <button class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-100 px-4 py-2.5 text-sm font-black text-amber-900 shadow-sm hover:bg-amber-200" type="submit">
            Guardar cambios
          </button>
        </div>
      @endif

      {{-- =====================
           TAB: Regalos (Drag & Drop)
      ====================== --}}
      @if($activeTab === 'gifts')
        <section class="rounded-2xl border border-slate-200 bg-white p-4">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <h2 class="text-base font-black text-slate-900">Regalos / Mesa de regalos</h2>
              <p class="mt-1 text-sm text-slate-600">Puedes arrastrar para reordenar.</p>
            </div>

            <button
              type="button"
              id="addGiftBtn"
              class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-extrabold text-slate-900 shadow-sm hover:bg-slate-50"
            >
              + Agregar regalo
            </button>
          </div>

          <div id="giftsWrap" class="mt-4 space-y-3">
            @foreach($gifts as $idx => $g)
              <div class="gift-row rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" draggable="true">
                <div class="mb-3 flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="drag-handle cursor-move select-none rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-black text-slate-700">☰</span>
                    <span class="text-xs font-semibold text-slate-500">Arrastra para reordenar</span>
                  </div>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                  <div>
                    <label class="block text-sm font-extrabold text-slate-800">Título</label>
                    <input name="theme[gifts][{{ $idx }}][title]" value="{{ $g['title'] ?? '' }}" placeholder="Mesa de regalos"
                      class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                  </div>

                  <div>
                    <label class="block text-sm font-extrabold text-slate-800">Código / Nota</label>
                    <input name="theme[gifts][{{ $idx }}][code]" value="{{ $g['code'] ?? '' }}" placeholder="Num. 12345 o 'El día del evento'"
                      class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                  </div>

                  <div class="lg:col-span-2">
                    <label class="block text-sm font-extrabold text-slate-800">URL (opcional)</label>
                    <input name="theme[gifts][{{ $idx }}][url]" value="{{ $g['url'] ?? '' }}" placeholder="https://..."
                      class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                  </div>

                  <div>
                    <label class="block text-sm font-extrabold text-slate-800">Texto del botón</label>
                    <input name="theme[gifts][{{ $idx }}][btn]" value="{{ $g['btn'] ?? 'VER' }}" placeholder="VER"
                      class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                  </div>

                  <div>
                    <label class="block text-sm font-extrabold text-slate-800">Ícono URL (opcional)</label>
                    <input name="theme[gifts][{{ $idx }}][icon_url]" value="{{ $g['icon_url'] ?? '' }}" placeholder="https://.../gift.png"
                      class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                  </div>
                </div>

                <div class="mt-3 flex justify-end">
                  <button type="button"
                    class="inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-extrabold text-rose-700 hover:bg-rose-100"
                    onclick="this.closest('.gift-row').remove()">
                    Quitar
                  </button>
                </div>
              </div>
            @endforeach
          </div>
        </section>

        <div class="flex justify-end">
          <button class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-100 px-4 py-2.5 text-sm font-black text-amber-900 shadow-sm hover:bg-amber-200" type="submit">
            Guardar cambios
          </button>
        </div>
      @endif

      {{-- =====================
           TAB: Hero
      ====================== --}}
      @if($activeTab === 'hero')
        <section class="rounded-2xl border border-slate-200 bg-white p-4">
          <div>
            <h2 class="text-base font-black text-slate-900">Hero (foto principal)</h2>
            <p class="mt-1 text-sm text-slate-600">Foto de portada del evento.</p>
          </div>

          @if($event->hero_image_path)
            <div class="mt-4">
              <p class="text-xs text-slate-500">Actual:</p>
              <img src="{{ asset('storage/'.$event->hero_image_path) }}" class="mt-2 w-full max-w-sm rounded-2xl border border-slate-200 object-cover shadow-sm" alt="" />
              <label class="mt-3 flex items-center gap-2 text-sm font-semibold text-slate-700">
                <input type="checkbox" name="remove_hero" value="1" class="h-4 w-4 rounded border-slate-300">
                Quitar foto hero
              </label>
            </div>
          @endif

          <div class="mt-4">
            <label class="block text-sm font-extrabold text-slate-800">Subir nueva foto (JPG/PNG, máx 4MB)</label>
            <input type="file" name="hero_image" accept="image/*"
              class="mt-2 block w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-extrabold hover:bg-slate-50" />
          </div>
        </section>

        <div class="flex justify-end">
          <button class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-100 px-4 py-2.5 text-sm font-black text-amber-900 shadow-sm hover:bg-amber-200" type="submit">
            Guardar cambios
          </button>
        </div>
      @endif

      {{-- =====================
           TAB: Música
      ====================== --}}
      @if($activeTab === 'music')
        <section class="rounded-2xl border border-slate-200 bg-white p-4">
          <div>
            <h2 class="text-base font-black text-slate-900">Música (opcional)</h2>
            <p class="mt-1 text-sm text-slate-600">Puedes subir una canción para la invitación.</p>
          </div>

          <div class="mt-4">
            <label class="block text-sm font-extrabold text-slate-800">Título de la canción</label>
            <input name="music_title" value="{{ old('music_title', $event->music_title) }}" placeholder="Vals / Canción especial"
              class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
          </div>

          @if($event->music_path)
            <div class="mt-4">
              <audio controls class="w-full">
                <source src="{{ asset('storage/'.$event->music_path) }}">
              </audio>
              <label class="mt-3 flex items-center gap-2 text-sm font-semibold text-slate-700">
                <input type="checkbox" name="remove_music" value="1" class="h-4 w-4 rounded border-slate-300">
                Quitar música
              </label>
            </div>
          @endif

          <div class="mt-4">
            <label class="block text-sm font-extrabold text-slate-800">Subir música (mp3/wav/ogg, máx 12MB)</label>
            <input type="file" name="music" accept="audio/*"
              class="mt-2 block w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-extrabold hover:bg-slate-50" />
          </div>
        </section>

        <div class="flex justify-end">
          <button class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-100 px-4 py-2.5 text-sm font-black text-amber-900 shadow-sm hover:bg-amber-200" type="submit">
            Guardar cambios
          </button>
        </div>
      @endif
    </form>
  </div>

  {{-- =====================
       TAB: Galería (fuera del form principal)
  ====================== --}}
  @if($activeTab === 'gallery')
    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <div>
        <h2 class="text-base font-black text-slate-900">Galería (máx 12 fotos)</h2>
        <p class="mt-1 text-sm text-slate-600">Sube entre 6 y 12 fotos para que se vea premium.</p>
      </div>

      <form method="POST" action="{{ route('panel.event.gallery.add') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
        @csrf

        <div>
          <label class="block text-sm font-extrabold text-slate-800">Agregar fotos (puedes seleccionar varias)</label>
          <input
            type="file"
            name="photos[]"
            accept="image/*"
            multiple
            class="mt-2 block w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-extrabold hover:bg-slate-50"
          />
        </div>

        <div class="flex justify-end">
          <button class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-100 px-4 py-2.5 text-sm font-black text-amber-900 shadow-sm hover:bg-amber-200" type="submit">
            Subir a galería
          </button>
        </div>
      </form>

      @if($event->photos->count())
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
          @foreach($event->photos as $p)
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
              <img src="{{ asset('storage/'.$p->path) }}" class="h-40 w-full object-cover" alt="">
              <form method="POST" action="{{ route('panel.event.gallery.delete', $p) }}" onsubmit="return confirm('¿Eliminar esta foto?')">
                @csrf
                @method('DELETE')
                <button class="w-full border-t border-rose-200 bg-rose-50 px-3 py-2 text-sm font-extrabold text-rose-700 hover:bg-rose-100" type="submit">
                  Eliminar
                </button>
              </form>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @endif

  {{-- Sticky Save --}}
  @if($activeTab !== 'gallery')
    <div class="fixed bottom-5 right-5 z-50 flex items-center gap-2">
      <a
        href="{{ route('panel.invites.index') }}"
        class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-900 shadow-lg shadow-slate-900/10 hover:bg-slate-50"
      >
        ← Invitados
      </a>

      <button
        type="submit"
        form="eventMainForm"
        class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-black text-white shadow-lg shadow-slate-900/20 hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300"
      >
        💾 Guardar
      </button>
    </div>
  @endif

</div>
@endsection

@section('scripts')
<script>
  // ============= Acordeón (Theme groups) =============
  (function () {
    function toggleAcc(id) {
      const panel = document.querySelector(`[data-acc-panel="${id}"]`);
      const ico = document.querySelector(`[data-acc-ico="${id}"]`);
      const btn = document.querySelector(`[data-acc-btn="${id}"]`);
      if (!panel || !ico || !btn) return;

      const isHidden = panel.classList.toggle('hidden');
      ico.textContent = isHidden ? '+' : '−';
      btn.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
    }

    document.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-acc-btn]');
      if (!btn) return;
      toggleAcc(btn.getAttribute('data-acc-btn'));
    });
  })();
</script>

<script>
  // ============= Itinerary add =============
  (function(){
    const wrap = document.getElementById('itineraryWrap');
    const btn  = document.getElementById('addItineraryBtn');
    if(!wrap || !btn) return;

    function nextIndex(){
      const inputs = wrap.querySelectorAll('input[name^="theme[itinerary]"]');
      let max = -1;
      inputs.forEach(i=>{
        const m = i.name.match(/theme\[itinerary]\[(\d+)\]/);
        if(m) max = Math.max(max, parseInt(m[1],10));
      });
      return max + 1;
    }

    btn.addEventListener('click', ()=>{
      const i = nextIndex();
      const div = document.createElement('div');
      div.className = 'it-row rounded-2xl border border-slate-200 bg-white p-4 shadow-sm';
      div.setAttribute('draggable','true');
      div.innerHTML = `
        <div class="mb-3 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="drag-handle cursor-move select-none rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-black text-slate-700">☰</span>
            <span class="text-xs font-semibold text-slate-500">Arrastra para reordenar</span>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <label class="block text-sm font-extrabold text-slate-800">Hora</label>
            <input name="theme[itinerary][${i}][time]" placeholder="8:30 PM"
              class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
          </div>
          <div>
            <label class="block text-sm font-extrabold text-slate-800">Actividad</label>
            <input name="theme[itinerary][${i}][name]" placeholder="Cena / Vals / Baile sorpresa"
              class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
          </div>
          <div class="lg:col-span-2">
            <label class="block text-sm font-extrabold text-slate-800">Ícono (URL opcional)</label>
            <input name="theme[itinerary][${i}][icon_url]" placeholder="https://.../icon.png"
              class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
          </div>
        </div>

        <div class="mt-3 flex justify-end">
          <button type="button"
            class="inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-extrabold text-rose-700 hover:bg-rose-100"
            onclick="this.closest('.it-row').remove()">
            Quitar
          </button>
        </div>
      `;
      wrap.appendChild(div);
    });
  })();
</script>

<script>
  // ============= Gifts add =============
  (function(){
    const wrap = document.getElementById('giftsWrap');
    const btn  = document.getElementById('addGiftBtn');
    if(!wrap || !btn) return;

    function nextIndex(){
      const inputs = wrap.querySelectorAll('input[name^="theme[gifts]"]');
      let max = -1;
      inputs.forEach(i=>{
        const m = i.name.match(/theme\[gifts]\[(\d+)\]/);
        if(m) max = Math.max(max, parseInt(m[1],10));
      });
      return max + 1;
    }

    btn.addEventListener('click', ()=>{
      const i = nextIndex();
      const div = document.createElement('div');
      div.className = 'gift-row rounded-2xl border border-slate-200 bg-white p-4 shadow-sm';
      div.setAttribute('draggable','true');
      div.innerHTML = `
        <div class="mb-3 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="drag-handle cursor-move select-none rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-black text-slate-700">☰</span>
            <span class="text-xs font-semibold text-slate-500">Arrastra para reordenar</span>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <label class="block text-sm font-extrabold text-slate-800">Título</label>
            <input name="theme[gifts][${i}][title]" placeholder="Mesa de regalos"
              class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
          </div>
          <div>
            <label class="block text-sm font-extrabold text-slate-800">Código / Nota</label>
            <input name="theme[gifts][${i}][code]" placeholder="Num. 12345 o 'El día del evento'"
              class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
          </div>
          <div class="lg:col-span-2">
            <label class="block text-sm font-extrabold text-slate-800">URL (opcional)</label>
            <input name="theme[gifts][${i}][url]" placeholder="https://..."
              class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
          </div>
          <div>
            <label class="block text-sm font-extrabold text-slate-800">Texto del botón</label>
            <input name="theme[gifts][${i}][btn]" value="VER" placeholder="VER"
              class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
          </div>
          <div>
            <label class="block text-sm font-extrabold text-slate-800">Ícono URL (opcional)</label>
            <input name="theme[gifts][${i}][icon_url]" placeholder="https://.../gift.png"
              class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
          </div>
        </div>

        <div class="mt-3 flex justify-end">
          <button type="button"
            class="inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-extrabold text-rose-700 hover:bg-rose-100"
            onclick="this.closest('.gift-row').remove()">
            Quitar
          </button>
        </div>
      `;
      wrap.appendChild(div);
    });
  })();
</script>

<script>
  // ============= Sortable + reindex (Itinerary/Gifts) =============
  (function () {
    function makeSortable(containerId, itemSelector, nameRoot) {
      const wrap = document.getElementById(containerId);
      if (!wrap) return;

      let dragEl = null;

      function renumber() {
        const items = Array.from(wrap.querySelectorAll(itemSelector));
        items.forEach((item, idx) => {
          const inputs = item.querySelectorAll('input, textarea, select');
          inputs.forEach((el) => {
            if (!el.name) return;
            el.name = el.name.replace(new RegExp(`(${nameRoot}\\[)\\d+(\\])`), `$1${idx}$2`);
          });
        });
      }

      function onDragStart(e) {
        const item = e.target.closest(itemSelector);
        if (!item) return;
        dragEl = item;
        item.classList.add('opacity-60');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', 'drag');
      }

      function onDragEnd() {
        if (dragEl) dragEl.classList.remove('opacity-60');
        dragEl = null;
        renumber();
      }

      function onDragOver(e) {
        e.preventDefault();
        const overItem = e.target.closest(itemSelector);
        if (!overItem || !dragEl || overItem === dragEl) return;

        const rect = overItem.getBoundingClientRect();
        const after = (e.clientY - rect.top) > rect.height / 2;

        if (after) overItem.after(dragEl);
        else overItem.before(dragEl);
      }

      wrap.addEventListener('dragstart', onDragStart);
      wrap.addEventListener('dragend', onDragEnd);
      wrap.addEventListener('dragover', onDragOver);

      return { renumber };
    }

    const it = makeSortable('itineraryWrap', '.it-row', 'theme[itinerary]');
    makeSortable('giftsWrap', '.gift-row', 'theme[gifts]');

    // Bonus: Ordenar por hora (itinerary)
    const sortBtn = document.getElementById('sortItineraryBtn');
    const itWrap = document.getElementById('itineraryWrap');

    function parseTimeToMinutes(str) {
      if (!str) return null;
      let s = String(str).trim().toLowerCase();
      if (!s) return null;

      // Normaliza separadores
      s = s.replace(/\./g, '').replace(/\s+/g, ' ');

      // Casos: "6:00 PM", "6 PM", "18:30", "18:30 hrs", "6pm"
      // 24h con :
      let m = s.match(/^(\d{1,2})\s*:\s*(\d{2})/);
      if (m) {
        const hh = parseInt(m[1], 10);
        const mm = parseInt(m[2], 10);
        if (hh >= 0 && hh <= 23 && mm >= 0 && mm <= 59) return hh * 60 + mm;
      }

      // 12h con AM/PM
      let m2 = s.match(/^(\d{1,2})(?:\s*:\s*(\d{2}))?\s*(am|pm)\b/);
      if (m2) {
        let hh = parseInt(m2[1], 10);
        const mm = m2[2] ? parseInt(m2[2], 10) : 0;
        const ap = m2[3];
        if (hh < 1 || hh > 12 || mm < 0 || mm > 59) return null;
        if (ap === 'pm' && hh !== 12) hh += 12;
        if (ap === 'am' && hh === 12) hh = 0;
        return hh * 60 + mm;
      }

      // 12h sin espacios "6pm"
      let m3 = s.match(/^(\d{1,2})(am|pm)\b/);
      if (m3) {
        let hh = parseInt(m3[1], 10);
        const ap = m3[2];
        if (hh < 1 || hh > 12) return null;
        if (ap === 'pm' && hh !== 12) hh += 12;
        if (ap === 'am' && hh === 12) hh = 0;
        return hh * 60;
      }

      return null;
    }

    function getRowTimeMinutes(row) {
      const input = row.querySelector('input[name*="[time]"]');
      const v = input ? input.value : '';
      const min = parseTimeToMinutes(v);
      return min;
    }

    if (sortBtn && itWrap) {
      sortBtn.addEventListener('click', () => {
        const rows = Array.from(itWrap.querySelectorAll('.it-row'));
        if (rows.length < 2) return;

        // Orden: primero los que tienen hora válida, luego los que no
        rows.sort((a, b) => {
          const ta = getRowTimeMinutes(a);
          const tb = getRowTimeMinutes(b);

          const aValid = (ta !== null);
          const bValid = (tb !== null);

          if (aValid && bValid) return ta - tb;
          if (aValid && !bValid) return -1;
          if (!aValid && bValid) return 1;

          // ambos inválidos => mantener el orden lo más posible
          return 0;
        });

        // Re-append en orden
        rows.forEach(r => itWrap.appendChild(r));

        // Reindex names
        if (it && typeof it.renumber === 'function') it.renumber();
      });
    }
  })();
</script>
@endsection
