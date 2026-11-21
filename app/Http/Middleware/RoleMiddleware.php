<?php

namespace App\Http\Middleware;

use App\Models\EmployeeLeaveRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return redirect()->route('customer.home');
        }

        if (in_array($request->user()->role, ['kasir', 'kitchen'], true) && EmployeeLeaveRequest::whereHas('employee', fn ($query) => $query->where('user_id', $request->user()->id))
            ->where('status', 'Disetujui')->whereDate('start_date', '<=', today())->whereDate('end_date', '>=', today())->exists()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('error', 'Akses akun dinonaktifkan sementara selama cuti yang disetujui.');
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
