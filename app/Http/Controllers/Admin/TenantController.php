<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::latest()->paginate(20);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tenant_name' => 'required|string|max:255',
            'tenant_slug' => 'required|string|max:60|alpha_dash|unique:tenants,slug',

            'client_name' => 'required|string|max:255',
            'client_email'=> 'required|email|max:255|unique:users,email',
            'client_password' => 'required|string|min:8',

            'event_type' => 'required|string|max:30',
            'event_title'=> 'required|string|max:255',
            'celebrant_name' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'venue' => 'nullable|string|max:255',
        ]);

        $tenant = Tenant::create([
            'name' => $data['tenant_name'],
            'slug' => Str::lower($data['tenant_slug']),
            'is_active' => true,
        ]);

        User::create([
            'name' => $data['client_name'],
            'email'=> $data['client_email'],
            'password' => Hash::make($data['client_password']),
            'role' => 'client',
            'tenant_id' => $tenant->id,
        ]);

        Event::create([
            'tenant_id' => $tenant->id,
            'type' => $data['event_type'],
            'title'=> $data['event_title'],
            'celebrant_name' => $data['celebrant_name'] ?? null,
            'starts_at' => $data['starts_at'],
            'venue' => $data['venue'] ?? null,
        ]);

        return redirect()->route('admin.tenants.index')
            ->with('status', 'Tenant + usuario + evento creados.');
    }

    public function edit(Tenant $tenant)
    {
        $clientUser = User::where('tenant_id', $tenant->id)
            ->where('role', 'client')
            ->first();

        $event = Event::where('tenant_id', $tenant->id)->latest()->first();

        return view('admin.tenants.edit', compact('tenant', 'clientUser', 'event'));
    }

    private function normalizeSlug(string $slug): string
    {
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/\-+/', '-', $slug);
        return trim($slug, '-');
    }

    public function update(Request $request, Tenant $tenant)
    {
        // Ubica al cliente (si existe)
        $clientUser = User::where('tenant_id', $tenant->id)
            ->where('role', 'client')
            ->first();

        // Ubica el evento base (si existe)
        $event = Event::where('tenant_id', $tenant->id)->latest()->first();

        $rules = [
            // Tenant
            'name' => 'required|string|max:255',
            'slug' => [
                'required','string','max:60',
                Rule::unique('tenants', 'slug')->ignore($tenant->id),
            ],
            'is_active' => 'nullable|boolean',
            'tier' => 'nullable|string|max:40',

            // Cliente (opcionales si existe)
            'client_name' => 'nullable|string|max:255',
            'client_email' => [
                'nullable','email','max:255',
                // único en users excepto el clientUser actual si existe
                $clientUser
                    ? Rule::unique('users', 'email')->ignore($clientUser->id)
                    : Rule::unique('users', 'email'),
            ],
            'client_password' => 'nullable|string|min:8',

            // Evento base
            'event_type' => 'nullable|string|max:30',
            'event_title'=> 'nullable|string|max:255',
            'celebrant_name' => 'nullable|string|max:255',
            'starts_at' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
        ];

        $data = $request->validate($rules);

        // ===== 1) Tenant =====
        $tenant->name = $data['name'];
        $tenant->slug = $this->normalizeSlug($data['slug']);
        $tenant->is_active = $request->boolean('is_active');

        if (array_key_exists('tier', $data) && Schema::hasColumn('tenants', 'tier')) {
            $tenant->tier = $data['tier'] ?: null;
        }

        $tenant->save();

        // ===== 2) Usuario cliente =====
        // Si no existe, NO lo creamos aquí (para evitar crear users por error),
        // pero si quieres, te lo armo luego.
        if ($clientUser) {
            if (isset($data['client_name'])) {
                $clientUser->name = $data['client_name'];
            }

            if (isset($data['client_email'])) {
                $clientUser->email = $data['client_email'];
            }

            if (!empty($data['client_password'])) {
                $clientUser->password = Hash::make($data['client_password']);
            }

            $clientUser->save();
        }

        // ===== 3) Evento base =====
        // Si no existe, lo creamos mínimo con type/title/starts_at por seguridad.
        if (!$event) {
            $event = Event::create([
                'tenant_id' => $tenant->id,
                'type' => $data['event_type'] ?? 'xv',
                'title' => $data['event_title'] ?? 'Evento',
                'celebrant_name' => $data['celebrant_name'] ?? null,
                'starts_at' => $data['starts_at'] ?? now(),
                'venue' => $data['venue'] ?? null,
            ]);
        } else {
            if (isset($data['event_type']) && $data['event_type'] !== '') $event->type = $data['event_type'];
            if (isset($data['event_title']) && $data['event_title'] !== '') $event->title = $data['event_title'];
            if (array_key_exists('celebrant_name', $data)) $event->celebrant_name = $data['celebrant_name'] ?: null;
            if (isset($data['starts_at']) && $data['starts_at'] !== '') $event->starts_at = $data['starts_at'];
            if (array_key_exists('venue', $data)) $event->venue = $data['venue'] ?: null;

            $event->save();
        }

        return redirect()->route('admin.tenants.index')->with('status', 'Tenant actualizado.');
    }

    public function toggle(Tenant $tenant)
    {
        $tenant->is_active = !$tenant->is_active;
        $tenant->save();

        return back()->with('status', 'Estado actualizado.');
    }
}
