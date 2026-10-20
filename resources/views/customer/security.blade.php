@extends('layouts.customer')

@section('title', 'Keamanan & Password - Quattro Coffee')

@section('content')

<div class="app-container security-page">

    {{-- HEADER --}}
    <div class="security-header">

        <a
            href="{{ route('customer.profile') }}"
            class="security-back"
        >
            <i class="fa-solid fa-chevron-left"></i>
        </a>

        <div>
            <h3>Keamanan & Password</h3>
            <p>Kelola keamanan akun kamu.</p>
        </div>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="security-alert success">

            <i class="fa-solid fa-circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERROR --}}
    @if($errors->any())

        <div class="security-alert error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <span>
                {{ $errors->first() }}
            </span>

        </div>

    @endif


    {{-- PASSWORD CARD --}}
    <div class="security-card">

        <div class="security-card-title">

            <div class="security-icon">
                <i class="fa-solid fa-lock"></i>
            </div>

            <div>
                <strong>Ubah Password</strong>

                <span>
                    Gunakan password yang kuat dan mudah kamu ingat.
                </span>
            </div>

        </div>


        <form
            method="POST"
            action="{{ route('customer.security.password') }}"
        >

            @csrf
            @method('PUT')


            {{-- CURRENT PASSWORD --}}
            <div class="security-field">

                <label for="current_password">
                    Password Saat Ini
                </label>

                <div class="security-input">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="Masukkan password saat ini"
                        required
                        autocomplete="current-password"
                    >

                    <button
                        type="button"
                        class="security-eye"
                        onclick="togglePassword('current_password', this)"
                    >
                        <i class="fa-solid fa-eye"></i>
                    </button>

                </div>

            </div>


            {{-- NEW PASSWORD --}}
            <div class="security-field">

                <label for="password">
                    Password Baru
                </label>

                <div class="security-input">

                    <i class="fa-solid fa-key"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 8 karakter"
                        required
                        autocomplete="new-password"
                    >

                    <button
                        type="button"
                        class="security-eye"
                        onclick="togglePassword('password', this)"
                    >
                        <i class="fa-solid fa-eye"></i>
                    </button>

                </div>

            </div>


            {{-- CONFIRM --}}
            <div class="security-field">

                <label for="password_confirmation">
                    Konfirmasi Password Baru
                </label>

                <div class="security-input">

                    <i class="fa-solid fa-shield-halved"></i>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password baru"
                        required
                        autocomplete="new-password"
                    >

                    <button
                        type="button"
                        class="security-eye"
                        onclick="togglePassword('password_confirmation', this)"
                    >
                        <i class="fa-solid fa-eye"></i>
                    </button>

                </div>

            </div>


            {{-- INFO --}}
            <div class="password-info">

                <i class="fa-solid fa-circle-info"></i>

                <span>
                    Password baru minimal 8 karakter.
                </span>

            </div>


            {{-- SUBMIT --}}
            <button
                type="submit"
                class="security-save-btn"
            >

                <i class="fa-solid fa-check"></i>

                Simpan Password

            </button>

        </form>

    </div>

</div>


<script>

function togglePassword(id, button)
{
    const input = document.getElementById(id);

    if (!input) {
        return;
    }

    const icon = button.querySelector('i');

    if (input.type === 'password') {

        input.type = 'text';

        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');

    } else {

        input.type = 'password';

        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');

    }
}

</script>

@endsection