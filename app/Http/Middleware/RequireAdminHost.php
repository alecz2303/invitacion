<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminHost
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ajusta esto a tu dominio real (prod) y a local (test)
        $allowedHosts = [
            'admin.events.test',
            'admin.events.partyx.com.mx',
        ];

        abort_unless(in_array($request->getHost(), $allowedHosts, true), 404);

        return $next($request);
    }
}
