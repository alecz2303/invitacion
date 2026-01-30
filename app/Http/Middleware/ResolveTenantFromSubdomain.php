<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class ResolveTenantFromSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost(); // mariana.events.partyx.com.mx
        $parts = explode('.', $host);
        $sub = $parts[0] ?? null;

        // Subdominios reservados (no son tenants)
        $reserved = ['www', 'events', 'admin'];

        if (!$sub || in_array($sub, $reserved, true)) {
            app()->instance('tenant', null);
            return $next($request);
        }

        $tenant = Tenant::where('slug', $sub)->where('is_active', true)->first();

        abort_if(!$tenant, 404, 'Tenant not found');

        app()->instance('tenant', $tenant);
        return $next($request);
    }
}
