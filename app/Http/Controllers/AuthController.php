<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'customer') {
            return redirect()->route('customer.profile');
        }

        return view('customer.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])
            ->where('role', 'customer')
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])
                ->withInput($request->only('email'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('customer.profile'));
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
