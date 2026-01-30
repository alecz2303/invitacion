<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
            'event_type' => 'required|string|max:30', // xv, boda, etc
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

        $user = User::create([
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
}
