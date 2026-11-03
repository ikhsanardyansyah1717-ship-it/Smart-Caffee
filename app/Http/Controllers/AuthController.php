<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'customer') {
            return redirect()->route('customer.profile');
        }

        return view('customer.login');
    }

    public function startWhatsAppLogin(Request $request)
    {
        abort_unless(config('services.whatsapp.login_number'), 503, 'Login WhatsApp belum dikonfigurasi.');

        $code = strtoupper(Str::random(12));
        $key = 'customer_whatsapp_login:' . $code;
        Cache::put($key, [
            'session_id' => $request->session()->getId(),
            'status' => 'pending',
            'user_id' => null,
        ], now()->addMinutes(5));

        $returnUrl = URL::temporarySignedRoute(
            'customer.login.whatsapp.return',
            now()->addMinutes(5),
            ['code' => $code]
        );
        $message = "Hi! Saya ingin login ke Quattro Coffee.\n\nKode verifikasi: {$code}\n\nSetelah mengirim pesan, kembali ke browser untuk melanjutkan: {$returnUrl}";
        $whatsAppUrl = 'https://api.whatsapp.com/send?phone='
            . urlencode(config('services.whatsapp.login_number'))
            . '&text=' . urlencode($message);

        return response()->json([
            'code' => $code,
            'whatsapp_url' => $whatsAppUrl,
        ]);
    }

    public function pollWhatsAppLogin(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'regex:/^[A-Z0-9]{12}$/'],
        ]);

        $key = 'customer_whatsapp_login:' . $data['code'];
        $challenge = Cache::get($key);

        abort_unless($challenge && hash_equals($challenge['session_id'], $request->session()->getId()), 404);

        if ($challenge['status'] === 'pending') {
            return response()->json(['status' => 'pending'], 202);
        }

        if ($challenge['status'] === 'unlinked') {
            Cache::forget($key);
            return response()->json(['status' => 'unlinked'], 422);
        }

        $user = User::whereKey($challenge['user_id'])->where('role', 'customer')->first();
        Cache::forget($key);
        abort_unless($user, 404);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'status' => 'authenticated',
            'redirect_url' => redirect()->intended(route('customer.profile'))->getTargetUrl(),
        ]);
    }

    public function returnFromWhatsApp(string $code)
    {
        return redirect()->route('customer.login', ['wa_code' => strtoupper($code)]);
    }

    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1);
        if (str_starts_with($digits, '8')) $digits = '62' . $digits;

        return preg_match('/^62[0-9]{8,13}$/', $digits) ? $digits : null;
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.home');
    }

    public function redirectByRole(): RedirectResponse
    {
        return match (Auth::user()->role) {
            'customer' => redirect()->route('customer.home'),
            'owner' => redirect()->route('owner.dashboard'),
            'kitchen' => redirect()->route('kitchen.dashboard'),
            'kasir' => redirect()->route('kasir.dashboard'),
            default => redirect()->route('admin.login'),
        };
    }
}
