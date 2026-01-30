<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventPhoto;
use Illuminate\Support\Facades\Storage;

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
        return Event::with('photos')->where('tenant_id', $tenant->id)->latest()->firstOrFail();
    }

    public function edit()
    {
        $tenant = $this->tenant();
        $event = $this->currentEvent($tenant);

        return view('panel.event.edit', compact('tenant','event'));
    }

    public function update(Request $request)
    {
        $tenant = $this->tenant();
        $event = $this->currentEvent($tenant);

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

        // Texto
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

        $baseDir = 'tenants/'.$tenant->id.'/events/'.$event->id; // <- sin interpolación rara
        $heroDir = $baseDir.'/hero';
        
        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');

            if (!$file->isValid()) {
                return back()->withErrors(['hero_image' => 'Archivo inválido (upload). Intenta otra vez.']);
            }

            $baseDir = 'tenants/'.$tenant->id.'/events/'.$event->id.'/hero';
            $filename = 'hero_' . time() . '.' . $file->getClientOriginalExtension();

            // Guardar primero
            $path = $file->storeAs($baseDir, $filename, 'public');

            if (!$path) {
                return back()->withErrors(['hero_image' => 'No se pudo guardar la imagen. Revisa permisos de storage/app/public.']);
            }

            // Borrar anterior después de guardar
            if ($event->hero_image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($event->hero_image_path);
            }

            $event->hero_image_path = $path;
            $event->save();
        }

        // Quitar música
        if ($request->boolean('remove_music') && $event->music_path) {
            Storage::disk('public')->delete($event->music_path);
            $event->music_path = null;
            $event->music_title = null;
        }

        // Subir música
        if ($request->hasFile('music')) {
            if ($event->music_path) Storage::disk('public')->delete($event->music_path);

            $path = $request->file('music')->store("$baseDir/music", 'public');
            $event->music_path = $path;
        }

        $event->save();

        return back()->with('status', 'Evento actualizado.');
    }

    public function addPhotos(Request $request)
    {
        $tenant = $this->tenant();
        $event = $this->currentEvent($tenant);

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
                'event_id' => $event->id,
                'path' => $path,
                'sort' => 0,
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
