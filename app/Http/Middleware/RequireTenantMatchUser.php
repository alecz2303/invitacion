<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTenantMatchUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = app('tenant'); // lo pone ResolveTenantFromSubdomain
        $user = $request->user();

        // Si estás en panel, tenant debe existir
        abort_if(!$tenant, 404);

        // El usuario debe pertenecer a ese tenant
        abort_unless($user && (int)$user->tenant_id === (int)$tenant->id, 403);

        return $next($request);
    }
}
