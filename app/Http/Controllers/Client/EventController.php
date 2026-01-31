<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventPhoto;
use Illuminate\Support\Facades\Storage;
use App\Support\Theme;
use Illuminate\Support\Arr;

class EventController extends Controller
{
    private function tenant()
    {
        $t = app('tenant');
        abort_if(!$t, 404);
        return $t;
    }

    private function currentEvent($tenant): Event
    {
        return Event::with('photos')
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->firstOrFail();
    }

    /**
     * Normaliza WhatsApp:
     * - Si llega un teléfono: "52961..." => https://wa.me/52961...
     * - Si llega wa.me/... => https://wa.me/...
     * - Si llega https://wa.me/... o https://api.whatsapp.com/send?... => se deja
     */
    private function normalizeWhatsapp(?string $value): ?string
    {
        if ($value === null) return null;

        $v = trim($value);
        if ($v === '') return '';

        // Si ya es URL válida, la regresamos tal cual
        if (filter_var($v, FILTER_VALIDATE_URL)) {
            return $v;
        }

        // Quitar espacios
        $vNoSpaces = preg_replace('/\s+/', '', $v);

        // wa.me sin esquema
        if (preg_match('#^wa\.me/#i', $vNoSpaces)) {
            return 'https://' . $vNoSpaces;
        }

        // api.whatsapp.com sin esquema (raro, pero por si acaso)
        if (preg_match('#^api\.whatsapp\.com/#i', $vNoSpaces)) {
            return 'https://' . $vNoSpaces;
        }

        // Si parece teléfono (solo dígitos, 7-16 dígitos)
        $digits = preg_replace('/\D+/', '', $vNoSpaces);
        if ($digits !== '' && preg_match('/^\d{7,16}$/', $digits)) {
            return 'https://wa.me/' . $digits;
        }

        // Si no pudo normalizar, lo dejamos igual (para que el validador lo marque)
        return $v;
    }

    public function edit()
    {
        $tenant = $this->tenant();
        $event  = $this->currentEvent($tenant);

        $type   = $event->type ?? 'xv';
        $schema = Theme::schema($type);
        $theme  = Theme::resolve($type, $event->theme);

        return view('panel.event.edit', compact('tenant', 'event', 'schema', 'theme', 'type'));
    }

    private function isUrl(string $v): bool
    {
        return (bool) filter_var($v, FILTER_VALIDATE_URL);
    }

    public function update(Request $request)
    {
        $tenant = $this->tenant();
        $event  = $this->currentEvent($tenant);

        $type    = $event->type ?? 'xv';
        $schema  = Theme::schema($type);
        $allowed = Theme::allowedKeys($type); // Asegúrate que existe en Theme.php

        $baseDir = 'tenants/' . $tenant->id . '/events/' . $event->id;

        /**
         * =========================================================
         * 0) Construir reglas dinámicas para theme (incluye uploads)
         * =========================================================
         */
        $rules = [
            'theme' => 'nullable|array',

            // Itinerary (UI sin JSON)
            'theme.itinerary' => 'nullable|array|max:30',
            'theme.itinerary.*.time' => 'nullable|string|max:30',
            'theme.itinerary.*.name' => 'nullable|string|max:120',
            'theme.itinerary.*.icon_url' => 'nullable|string|max:600',

            // Gifts (UI sin JSON)
            'theme.gifts' => 'nullable|array|max:30',
            'theme.gifts.*.title' => 'nullable|string|max:120',
            'theme.gifts.*.code' => 'nullable|string|max:120',
            'theme.gifts.*.url' => 'nullable|string|max:600',
            'theme.gifts.*.btn' => 'nullable|string|max:40',
            'theme.gifts.*.icon_url' => 'nullable|string|max:600',

            // Uploads de theme
            'theme_files' => 'nullable|array',
            'theme_remove' => 'nullable|array',
        ];

        // Para cada key tipo image en el schema, permite file + remove
        foreach ($schema as $key => $meta) {
            $t = $meta['type'] ?? 'string';
            if ($t === 'image') {
                $rules["theme_files.$key"]  = 'nullable|image|max:4096'; // 4MB
                $rules["theme_remove.$key"] = 'nullable|boolean';
            }
        }

        // Validamos todo lo del theme en un solo validate
        $validatedTheme = $request->validate($rules);

        /**
         * =========================================================
         * 1) Incoming theme (solo keys permitidas)
         * =========================================================
         */
        $incomingTheme = (array) $request->input('theme', []);
        $incomingTheme = Arr::only($incomingTheme, $allowed);

        // Trim de strings (sin tocar arrays)
        foreach ($incomingTheme as $k => $v) {
            if (is_string($v)) $incomingTheme[$k] = trim($v);
        }

        /**
         * =========================================================
         * 2) Limpieza de arrays (itinerary / gifts)
         * =========================================================
         */
        if (!empty($incomingTheme['itinerary']) && is_array($incomingTheme['itinerary'])) {
            $incomingTheme['itinerary'] = array_values(array_filter($incomingTheme['itinerary'], function ($row) {
                $name = trim((string)($row['name'] ?? ''));
                $time = trim((string)($row['time'] ?? ''));
                $icon = trim((string)($row['icon_url'] ?? ''));
                return ($name !== '' || $time !== '' || $icon !== '');
            }));
        }

        if (!empty($incomingTheme['gifts']) && is_array($incomingTheme['gifts'])) {
            $incomingTheme['gifts'] = array_values(array_filter($incomingTheme['gifts'], function ($row) {
                $title = trim((string)($row['title'] ?? ''));
                $code  = trim((string)($row['code'] ?? ''));
                $url   = trim((string)($row['url'] ?? ''));
                $icon  = trim((string)($row['icon_url'] ?? ''));
                return ($title !== '' || $code !== '' || $url !== '' || $icon !== '');
            }));
        }

        /**
         * =========================================================
         * 3) Normaliza WhatsApp (si viene)
         * =========================================================
         */
        if (array_key_exists('whatsapp_url', $incomingTheme)) {
            $incomingTheme['whatsapp_url'] = $this->normalizeWhatsapp($incomingTheme['whatsapp_url']);
        }

        /**
         * =========================================================
         * 4) Validaciones por schema (color/json/url)
         *     - URL: solo si schema dice 'url' (o si quieres forzar algunos string)
         *     - JSON: solo si schema dice 'json' y el valor ES string (aquí ya casi no lo usas)
         * =========================================================
         */
        foreach ($incomingTheme as $k => $v) {
            $t = $schema[$k]['type'] ?? 'string';

            // color
            if ($t === 'color' && is_string($v) && $v !== '') {
                if (!preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $v)) {
                    return back()->withErrors(["theme.$k" => "Color inválido en $k (usa #RGB o #RRGGBB)"])->withInput();
                }
            }

            // json (solo si te siguen llegando strings json en algún campo)
            if ($t === 'json' && is_string($v) && $v !== '') {
                json_decode($v, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return back()->withErrors(["theme.$k" => "JSON inválido en $k"])->withInput();
                }
            }

            // url (si todavía tienes campos url en schema)
            if ($t === 'url' && is_string($v) && $v !== '') {
                if (!filter_var($v, FILTER_VALIDATE_URL)) {
                    return back()->withErrors(["theme.$k" => "URL inválida en $k"])->withInput();
                }
            }
        }

        /**
         * =========================================================
         * 5) Procesar uploads y removals de imágenes del theme
         *    Inputs esperados:
         *      - theme_files[key] (file)
         *      - theme_remove[key] (1)
         * =========================================================
         */
        $themeFiles  = (array) $request->file('theme_files', []);
        $themeRemove = (array) $request->input('theme_remove', []);

        // Partimos del theme actual
        $resolvedTheme = $event->theme ?? [];

        // helper local para detectar URL
        $isUrl = function ($val) {
            return is_string($val) && (bool) filter_var($val, FILTER_VALIDATE_URL);
        };

        $themeDir = $baseDir . '/theme';

        foreach ($schema as $key => $meta) {
            $t = $meta['type'] ?? 'string';
            if ($t !== 'image') continue;

            // remove?
            if (!empty($themeRemove[$key])) {
                if (!empty($resolvedTheme[$key]) && !$isUrl($resolvedTheme[$key])) {
                    Storage::disk('public')->delete($resolvedTheme[$key]);
                }
                $resolvedTheme[$key] = null;
            }

            // upload?
            if (!empty($themeFiles[$key])) {
                // borra anterior si era local
                if (!empty($resolvedTheme[$key]) && !$isUrl($resolvedTheme[$key])) {
                    Storage::disk('public')->delete($resolvedTheme[$key]);
                }

                $path = $themeFiles[$key]->store($themeDir, 'public');
                $resolvedTheme[$key] = $path;
            }
        }

        /**
         * =========================================================
         * 6) Merge final del theme (una sola vez)
         * =========================================================
         */
        $event->theme = array_replace_recursive($resolvedTheme, $incomingTheme);

        /**
         * =========================================================
         * 7) Validación y guardado de campos normales del evento
         * =========================================================
         */
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'celebrant_name' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'venue' => 'nullable|string|max:255',
            'dress_code' => 'nullable|string|max:80',
            'message' => 'nullable|string|max:2000',
            'maps_url' => 'nullable|string|max:600',

            'hero_image' => 'nullable|image|max:4096', // 4MB
            'music' => 'nullable|mimetypes:audio/mpeg,audio/mp4,audio/wav,audio/x-wav,audio/ogg|max:12288', // 12MB
            'music_title' => 'nullable|string|max:255',
            'remove_music' => 'nullable|boolean',
            'remove_hero' => 'nullable|boolean',
        ]);

        $event->fill([
            'title' => $data['title'],
            'celebrant_name' => $data['celebrant_name'] ?? null,
            'starts_at' => $data['starts_at'],
            'venue' => $data['venue'] ?? null,
            'dress_code' => $data['dress_code'] ?? null,
            'message' => $data['message'] ?? null,
            'music_title' => $data['music_title'] ?? $event->music_title,
            'maps_url' => $data['maps_url'] ?? $event->maps_url,
        ]);

        /**
         * =========================================================
         * 8) Hero: quitar / subir
         * =========================================================
         */
        if ($request->boolean('remove_hero') && $event->hero_image_path) {
            Storage::disk('public')->delete($event->hero_image_path);
            $event->hero_image_path = null;
        }

        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');

            if (!$file->isValid()) {
                return back()->withErrors(['hero_image' => 'Archivo inválido (upload). Intenta otra vez.'])->withInput();
            }

            $heroDir = $baseDir . '/hero';
            $filename = 'hero_' . time() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs($heroDir, $filename, 'public');
            if (!$path) {
                return back()->withErrors(['hero_image' => 'No se pudo guardar la imagen. Revisa permisos de storage/app/public.'])->withInput();
            }

            if ($event->hero_image_path) {
                Storage::disk('public')->delete($event->hero_image_path);
            }

            $event->hero_image_path = $path;
        }

        /**
         * =========================================================
         * 9) Música: quitar / subir
         * =========================================================
         */
        if ($request->boolean('remove_music') && $event->music_path) {
            Storage::disk('public')->delete($event->music_path);
            $event->music_path = null;
            $event->music_title = null;
        }

        if ($request->hasFile('music')) {
            if ($event->music_path) {
                Storage::disk('public')->delete($event->music_path);
            }
            $path = $request->file('music')->store($baseDir . '/music', 'public');
            $event->music_path = $path;
        }

        /**
         * =========================================================
         * 10) Save final (UNO)
         * =========================================================
         */
        $event->save();

        return back()->with('status', 'Evento actualizado.');
    }


    public function addPhotos(Request $request)
    {
        $tenant = $this->tenant();
        $event  = $this->currentEvent($tenant);

        $request->validate([
            'photos' => 'required',
            'photos.*' => 'image|max:4096',
        ]);

        // Limitar a 12
        $currentCount = $event->photos()->count();
        $incoming = count($request->file('photos', []));
        abort_if($currentCount + $incoming > 12, 422, 'Máximo 12 fotos en la galería.');

        $baseDir = "tenants/{$tenant->id}/events/{$event->id}/gallery";

        foreach ($request->file('photos') as $file) {
            $path = $file->store($baseDir, 'public');
            EventPhoto::create([
                'tenant_id' => $tenant->id,
                'event_id'  => $event->id,
                'path'      => $path,
                'sort'      => 0,
            ]);
        }

        return back()->with('status', 'Fotos agregadas a la galería.');
    }

    public function deletePhoto(Request $request, EventPhoto $photo)
    {
        $tenant = $this->tenant();

        abort_if($photo->tenant_id !== $tenant->id, 403);

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back()->with('status', 'Foto eliminada.');
    }
}
