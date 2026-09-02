<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthAdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('admin.loginadmin');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where(function ($query) use ($data) {
            $query->where('username', $data['username'])
                  ->orWhere('email', $data['username']);
        })->whereIn('role', ['owner', 'kitchen', 'kasir'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()
                ->withErrors(['username' => 'Username/email atau password salah.'])
                ->withInput($request->only('username'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectByRole($user->role);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'Berhasil keluar dari akun admin.');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'owner' => redirect()->route('owner.dashboard'),
            'kitchen' => redirect()->route('kitchen.dashboard'),
            'kasir' => redirect()->route('kasir.dashboard'),
            default => redirect()->route('admin.login'),
        };
    }
}
