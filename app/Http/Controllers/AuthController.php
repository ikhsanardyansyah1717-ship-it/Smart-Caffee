<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CUSTOMER LOGIN PAGE
    |--------------------------------------------------------------------------
    */

    public function showLogin(): View
    {
        return view('auth.login');
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER LOGIN PROCESS
    |--------------------------------------------------------------------------
    */

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cari user berdasarkan email
        | dan pastikan role-nya CUSTOMER
        |--------------------------------------------------------------------------
        */

        $user = User::where('email', $credentials['email'])
            ->where('role', 'customer')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Cek user dan password
        |--------------------------------------------------------------------------
        */

        if (
            !$user ||
            !Hash::check($credentials['password'], $user->password)
        ) {
            return back()
                ->withErrors([
                    'email' => 'Email atau kata sandi salah.'
                ])
                ->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | Login user
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        /*
        |--------------------------------------------------------------------------
        | Regenerate session untuk keamanan
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Redirect ke halaman customer
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('customer.home');
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER REGISTER PAGE
    |--------------------------------------------------------------------------
    */

    public function showRegister(): View
    {
        return view('auth.register');
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER REGISTER PROCESS
    |--------------------------------------------------------------------------
    */

    public function register(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi data register
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Buat user customer
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => $data['name'],

            'email' => $data['email'],

            'password' => Hash::make($data['password']),

            /*
            | Semua user yang daftar melalui halaman customer
            | otomatis memiliki role customer
            */
            'role' => 'customer',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Otomatis login setelah register
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Masuk ke halaman customer
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('customer.home')
            ->with(
                'success',
                'Akun berhasil dibuat. Selamat datang!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        Auth::logout();


        /*
        |--------------------------------------------------------------------------
        | Hapus session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();


        /*
        |--------------------------------------------------------------------------
        | Buat CSRF token baru
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerateToken();


        /*
        |--------------------------------------------------------------------------
        | Kembali ke LOGIN CUSTOMER
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('customer.login')
            ->with(
                'success',
                'Anda telah berhasil logout.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | REDIRECT BERDASARKAN ROLE
    |--------------------------------------------------------------------------
    |
    | Fungsi ini bisa tetap dipakai jika nantinya kamu membutuhkan
    | redirect berdasarkan role.
    |
    */

    public function redirectByRole(): RedirectResponse
    {
        return match (Auth::user()->role) {

            'customer' =>
                redirect()->route('customer.home'),

            'owner' =>
                redirect()->route('owner.dashboard'),

            'kitchen' =>
                redirect()->route('kitchen.dashboard'),

            'kasir' =>
                redirect()->route('kasir.dashboard'),

            default =>
                redirect()
                    ->route('customer.login')
                    ->withErrors([
                        'email' => 'Role akun belum terdaftar.'
                    ]),
        };
    }
}