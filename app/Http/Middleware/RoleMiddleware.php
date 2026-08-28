<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {

        // Belum login
        if (!$request->user()) {
            return redirect()->route('customer.login');
        }

        // Role user tidak sesuai
        if (!in_array($request->user()->role, $roles)) {

            // Customer → tampilan 403 mobile
            if ($request->user()->role === 'customer') {
                return response()->view(
                    'errors.403',
                    [],
                    403
                );
            }

            // Owner, Kitchen, Kasir → tampilan 403 desktop
            if (in_array($request->user()->role, [
                'owner',
                'kitchen',
                'kasir'
            ])) {
                return response()->view(
                    'errors.403-admin',
                    [],
                    403
                );
            }

            // Role tidak dikenal
            abort(403);
        }

        return $next($request);
    }
}