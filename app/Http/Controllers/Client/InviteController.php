<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invite;
use App\Models\Event;
use App\Services\InviteHash;

class InviteController extends Controller
{
    private function tenant()
    {
        $t = app('tenant');
        abort_if(!$t, 404);
        return $t;
    }

    public function index()
    {
        $tenant = $this->tenant();

        $invites = Invite::with('event')
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->paginate(30);

        // URL base (subdominio actual)
        $base = 'https://' . request()->getHost();

        return view('panel.invites.index', compact('invites','tenant','base'));
    }

    public function create()
    {
        $tenant = $this->tenant();

        $events = Event::where('tenant_id', $tenant->id)->latest()->get();
        return view('panel.invites.create', compact('tenant','events'));
    }

    public function store(Request $request)
    {
        $tenant = $this->tenant();

        $data = $request->validate([
            'event_id' => 'required|integer',
            'guest_name' => 'required|string|max:255',
            'seats' => 'required|integer|min:1|max:20',
        ]);

        // seguridad: event del tenant
        $event = Event::where('tenant_id', $tenant->id)->where('id', $data['event_id'])->firstOrFail();

        $invite = Invite::create([
            'tenant_id' => $tenant->id,
            'event_id' => $event->id,
            'guest_name' => $data['guest_name'],
            'seats' => (int)$data['seats'],
            'status' => 'ACTIVE',
        ]);

        // Genera hash (para copiar en UI)
        $hash = InviteHash::encode($tenant, $invite->id);

        return redirect()->route('panel.invites.index')
            ->with('status', 'Invitado creado. Link: /i/'.$hash);
    }
}
