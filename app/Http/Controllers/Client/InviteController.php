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

    private function baseUrl($tenant): string
    {
        // Si ya lo calculas en otro lado, úsalo
        $host = request()->getHost(); // mariana.events.test
        $scheme = request()->isSecure() ? 'https://' : 'http://';
        return $scheme . $host;
    }

    public function index(Request $request)
    {
        $tenant = $this->tenant();
        $base   = $this->baseUrl($tenant);

        $q = trim((string)$request->query('q', ''));
        $status = trim((string)$request->query('status', ''));

        $query = Invite::with('event')
            ->where('tenant_id', $tenant->id)
            ->latest();

        if ($q !== '') {
            $query->where(function($qq) use ($q){
                $qq->where('guest_name', 'like', '%'.$q.'%');
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        // ✅ Paginación
        $invites = $query->paginate(20)->withQueryString();

        // ✅ Widgets (sobre TODO el tenant, no solo la página)
        $statsBase = Invite::where('tenant_id', $tenant->id);
        $totalInvites = (clone $statsBase)->count();
        $totalSeats   = (clone $statsBase)->sum('seats');
        $confirmed    = (clone $statsBase)->where('status','CONFIRMED')->count();
        $declined     = (clone $statsBase)->where('status','DECLINED')->count();
        $active       = (clone $statsBase)->where('status','ACTIVE')->count();

        // Asientos confirmados / pendientes (opcional)
        $seatsConfirmed = Invite::where('tenant_id',$tenant->id)->where('status','CONFIRMED')->sum('seats');
        $seatsPending   = Invite::where('tenant_id',$tenant->id)->where('status','ACTIVE')->sum('seats');

        return view('panel.invites.index', compact(
            'tenant','base','invites',
            'q','status',
            'totalInvites','totalSeats','confirmed','declined','active',
            'seatsConfirmed','seatsPending'
        ));
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

        // Asegura que el evento sea del tenant
        $event = Event::where('tenant_id',$tenant->id)->where('id',$data['event_id'])->firstOrFail();

        Invite::create([
            'tenant_id' => $tenant->id,
            'event_id' => $event->id,
            'guest_name' => $data['guest_name'],
            'seats' => $data['seats'],
            'status' => 'ACTIVE',
        ]);

        return redirect()->route('panel.invites.index')->with('status','Invitado creado.');
    }

    public function edit(Invite $invite)
    {
        $tenant = $this->tenant();
        abort_if((int)$invite->tenant_id !== (int)$tenant->id, 403);

        $events = Event::where('tenant_id', $tenant->id)->latest()->get();

        return view('panel.invites.edit', compact('tenant','invite','events'));
    }

    public function update(Request $request, Invite $invite)
    {
        $tenant = $this->tenant();
        abort_if((int)$invite->tenant_id !== (int)$tenant->id, 403);

        $data = $request->validate([
            'event_id' => 'required|integer',
            'guest_name' => 'required|string|max:255',
            'seats' => 'required|integer|min:1|max:20',
            'status' => 'required|string|in:ACTIVE,CONFIRMED,DECLINED',
        ]);

        $event = Event::where('tenant_id',$tenant->id)->where('id',$data['event_id'])->firstOrFail();

        $invite->event_id = $event->id;
        $invite->guest_name = $data['guest_name'];
        $invite->seats = $data['seats'];
        $invite->status = $data['status'];
        $invite->save();

        return redirect()->route('panel.invites.index')->with('status','Invitado actualizado.');
    }

    public function destroy(Request $request, Invite $invite)
    {
        $tenant = $this->tenant();
        abort_if((int)$invite->tenant_id !== (int)$tenant->id, 403);

        $invite->delete();

        return back()->with('status', 'Invitado eliminado.');
    }
}
