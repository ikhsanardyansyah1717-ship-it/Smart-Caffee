<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showLogin(): View
    {
        if (Auth::check()) {
            return redirect()->route('customer.home');
        }

        return view('auth.customer.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])
                ->withInput($request->only('email'));
        }

        $user = Auth::user();

        // Customer auth hanya menerima akun customer.
        // Jika kolom role belum dibuat, hapus blok ini sementara.
        if (isset($user->role) && $user->role !== 'customer') {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Akun ini bukan akun customer.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('customer.home'));
    }

    public function showRegister(): View
    {
        if (Auth::check()) {
            return redirect()->route('customer.home');
        }

        return view('auth.customer.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Jika tabel users kamu sudah memiliki kolom role,
        // tambahkan:
        // $user->update(['role' => 'customer']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.home')
            ->with('success', 'Akun customer berhasil dibuat.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}
