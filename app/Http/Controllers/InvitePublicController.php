<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invite;
use App\Services\InviteHash;
use App\Models\Rsvp;
use Illuminate\Support\Str;
use App\Support\Theme;

class InvitePublicController extends Controller
{
    public function demo(\Illuminate\Http\Request $request)
    {
        $tenant = app('tenant');
        abort_if(!$tenant, 404);

        $event = \App\Models\Event::where('tenant_id', $tenant->id)->latest()->firstOrFail();

        // ✅ Invite "fake" con la misma forma que espera el blade
        $invite = (object) [
            'id' => 0,
            'guest_name' => 'Invitado de Muestra',
            'seats' => 2,
            'status' => 'ACTIVE',

            // 👇 Esto es lo que te faltaba
            'event' => $event,

            // Por si en tu blade usa $invite->event_id
            'event_id' => $event->id,
        ];

        $type  = $event->type ?? 'xv';
        $theme = \App\Support\Theme::resolve($type, $event->theme);
        $hash = 'demo';

        // fallback por si no existe la vista
        $view = view()->exists("invite.templates.$type")
            ? "invite.templates.$type"
            : "invite.templates.xv";

        return view($view, compact('tenant', 'event', 'invite', 'theme', 'type', 'hash'))
            ->with('isDemo', true);
    }


    public function show(Request $request, string $hash)
    {
        $tenant = app('tenant');
        abort_if(!$tenant, 404);

        $inviteId = InviteHash::decode($tenant, $hash);
        abort_if(!$inviteId, 404);

        // 👇 Carga event + photos si tu template las usa
        $invite = Invite::with(['event.photos'])
            ->where('id', $inviteId)
            ->where('tenant_id', $tenant->id)
            ->firstOrFail();

        $event = $invite->event;

        // Tu "type" decide el template (xv, boda, etc.)
        $type = $event->type ?? 'xv';

        // fallback por si no existe la vista
        $view = view()->exists("invite.templates.$type")
            ? "invite.templates.$type"
            : "invite.templates.xv";

        // ✅ RESOLVER THEME: defaults + lo que ya guardaste en events.theme (JSON)
        // OJO: $event->theme debe ser array (casts) o null.
        $theme = Theme::resolve($type, $event->theme);

        return view($view, [
            'tenant' => $tenant,
            'invite' => $invite,
            'event'  => $event,
            'theme'  => $theme,  // ✅ esto es lo importante
            'hash'   => $hash,
        ]);
    }

    public function rsvp(Request $request, string $hash)
    {
        $tenant = app('tenant');
        abort_if(!$tenant, 404);

        $inviteId = InviteHash::decode($tenant, $hash);
        abort_if(!$inviteId, 404);

        $invite = Invite::with('event')
            ->where('id', $inviteId)
            ->where('tenant_id', $tenant->id)
            ->firstOrFail();

        // Idempotente: si ya no está activa, regresamos estado premium
        if ($invite->status !== 'ACTIVE') {
            return response()->json([
                'ok' => true,
                'estado' => 'YA_CONFIRMADO',
                'status' => $invite->status,
                'confirmed_at' => optional($invite->confirmed_at)->toIso8601String(),
            ]);
        }

        $data = $request->validate([
            'response' => 'required|in:SI,NO',
            // Si NO quieres que editen nombre, elimina este campo y ya.
            'name' => 'nullable|string|max:255',
        ]);

        $resp = $data['response'];

        Rsvp::create([
            'tenant_id' => $tenant->id,
            'invite_id' => $invite->id,
            'response'  => $resp,
            'user_agent'=> substr((string)$request->userAgent(), 0, 500),
            'ip'        => $request->ip(),
        ]);

        $invite->status = ($resp === 'SI') ? 'CONFIRMED' : 'DECLINED';
        $invite->confirmed_at = now();
        $invite->save();

        return response()->json([
            'ok' => true,
            'estado' => 'CONFIRMADO',
            'status' => $invite->status,
        ]);
    }

    public function ics(Request $request, string $hash)
    {
        $tenant = app('tenant');
        abort_if(!$tenant, 404);

        $inviteId = InviteHash::decode($tenant, $hash);
        abort_if(!$inviteId, 404);

        $invite = Invite::with('event')->where('id', $inviteId)->firstOrFail();
        $event = $invite->event;

        // Si todavía no hay fecha, regresamos 404 (o un mensaje)
        if (!$event || !$event->starts_at) {
            abort(404, 'Event date not configured.');
        }

        $tz = 'America/Mexico_City';

        // Fechas: si no hay ends_at, dura 4 horas
        $start = $event->starts_at->copy();
        $end   = $event->ends_at ? $event->ends_at->copy() : $start->copy()->addHours(4);

        // Formato ICS (UTC)
        $dtStart = $start->copy()->timezone('UTC')->format('Ymd\THis\Z');
        $dtEnd   = $end->copy()->timezone('UTC')->format('Ymd\THis\Z');

        $title = $event->title ?? 'Evento';
        $location = $event->venue ?? '';
        $details = trim(($event->theme['subtitle'] ?? '')."\n\n".($event->message ?? ''));

        if (!empty($event->maps_url)) {
            $details .= "\n\nUbicación (Maps): ".$event->maps_url;
        }

        // Sanitizar saltos
        $title = $this->icsEscape($title);
        $location = $this->icsEscape($location);
        $details = $this->icsEscape($details);

        $uid = $hash.'@miinvitaciondigital'; // puede ser cualquier dominio/string único
        $now = now()->timezone('UTC')->format('Ymd\THis\Z');

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Mi Invitación Digital//ES\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:{$uid}\r\n";
        $ics .= "DTSTAMP:{$now}\r\n";
        $ics .= "DTSTART:{$dtStart}\r\n";
        $ics .= "DTEND:{$dtEnd}\r\n";
        $ics .= "SUMMARY:{$title}\r\n";
        if ($location !== '') $ics .= "LOCATION:{$location}\r\n";
        if ($details !== '')  $ics .= "DESCRIPTION:{$details}\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        $filename = Str::slug($event->title ?? 'evento').'.ics';

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    private function icsEscape(string $text): string
    {
        // ICS necesita escapar backslashes, comas, punto y coma y saltos de línea
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(',', '\,', $text);
        $text = str_replace(';', '\;', $text);
        $text = str_replace(["\r\n", "\n", "\r"], '\\n', $text);
        return $text;
    }
}
