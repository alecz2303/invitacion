<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invite;
use App\Services\InviteHash;
use App\Models\Rsvp;

class InvitePublicController extends Controller
{
    public function show(Request $request, string $hash)
    {
        $tenant = app('tenant');
        abort_if(!$tenant, 404);

        $inviteId = InviteHash::decode($tenant, $hash);
        abort_if(!$inviteId, 404);

        $invite = Invite::with('event')
            ->where('id', $inviteId)
            ->where('tenant_id', $tenant->id)
            ->firstOrFail();

        $type = $invite->event->type ?? 'xv';

        // fallback por si no existe la vista
        $view = view()->exists("invite.templates.$type")
            ? "invite.templates.$type"
            : "invite.templates.xv";

        return view($view, [
            'tenant' => $tenant,
            'invite' => $invite,
            'event'  => $invite->event,
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
}
