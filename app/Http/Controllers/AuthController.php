<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;
use Illuminate\Support\Str;

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

        $message = "LOGIN QUATTRO COFFEE\n\nKode verifikasi: {$code}\n\nKirim pesan ini dari nomor WhatsApp yang ingin digunakan untuk akun Quattro Coffee. Setelah terkirim, kembali ke browser dan tunggu konfirmasi login.\n\nJika Anda tidak meminta kode ini, abaikan pesan ini.";
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
            'customer_id' => $user->id,
            'account_created' => (bool) ($challenge['account_created'] ?? false),
            'redirect_url' => redirect()->intended(route('customer.profile'))->getTargetUrl(),
        ]);
    }

    public function redirectToGoogle(Request $request)
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');

        if (!$clientId || !config('services.google.client_secret') || !$redirectUri) {
            return redirect()->route('customer.login')->with('google_login_error', 'Login Google belum disiapkan. Admin perlu mengisi kredensial Google OAuth terlebih dahulu.');
        }

        $state = Str::random(48);
        $request->session()->put('google_oauth_state', $state);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]));
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $expectedState = (string) $request->session()->pull('google_oauth_state', '');
        $receivedState = (string) $request->query('state', '');

        if (!$expectedState || !$receivedState || !hash_equals($expectedState, $receivedState)) {
            return redirect()->route('customer.login')->with('google_login_error', 'Sesi Google tidak cocok atau sudah kedaluwarsa. Silakan coba masuk lagi.');
        }

        if ($request->filled('error') || !$request->filled('code')) {
            return redirect()->route('customer.login')->with('google_login_error', 'Login Google dibatalkan atau tidak berhasil. Silakan coba lagi.');
        }

        try {
            $tokenResponse = Http::asForm()->acceptJson()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'code' => $request->query('code'),
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'grant_type' => 'authorization_code',
            ]);

            if (!$tokenResponse->successful() || !$tokenResponse->json('access_token')) {
                return redirect()->route('customer.login')->with('google_login_error', 'Google belum dapat memverifikasi sesi ini. Coba masuk lagi.');
            }

            $profileResponse = Http::withToken($tokenResponse->json('access_token'))
                ->acceptJson()->timeout(15)
                ->get('https://openidconnect.googleapis.com/v1/userinfo');
            $profile = $profileResponse->json();

            if (!$profileResponse->successful() || empty($profile['email']) || empty($profile['sub']) || ($profile['email_verified'] ?? false) !== true) {
                return redirect()->route('customer.login')->with('google_login_error', 'Gunakan akun Google dengan alamat email yang sudah terverifikasi.');
            }

            $email = mb_strtolower(trim($profile['email']));
            $user = User::where('email', $email)->first();

            if ($user && $user->role !== 'customer') {
                return redirect()->route('customer.login')->with('google_login_error', 'Email ini dipakai untuk akun staf. Gunakan halaman masuk staf.');
            }

            if (!$user) {
                $base = Str::slug(Str::before($email, '@')) ?: 'pelanggan';
                $base = mb_substr($base, 0, 40);
                $username = $base;
                $suffix = 1;
                while (User::where('username', $username)->exists()) {
                    $username = mb_substr($base, 0, 34) . '-' . $suffix++;
                }

                $user = User::create([
                    'name' => trim($profile['name'] ?? '') ?: $email,
                    'username' => $username,
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Str::random(64),
                    'role' => 'customer',
                ]);
            } elseif (!$user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('customer.profile'));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('customer.login')->with('google_login_error', 'Login Google belum berhasil. Periksa koneksi dan pengaturan OAuth, lalu coba lagi.');
        }
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
