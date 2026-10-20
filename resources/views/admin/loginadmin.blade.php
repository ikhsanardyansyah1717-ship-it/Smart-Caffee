<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Quattro Coffee</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap"
          rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
</head>
<body>

<div class="login-card">

    <div class="left-panel">
        <div class="logo-box">
            <div class="logo-img">
                <i class="fa-solid fa-mug-hot"></i>
            </div>

            <div class="brand-title">Quattro Coffee</div>
            <div class="admin-title">ADMIN</div>

            <div class="role-list">
                <span><i class="fa-solid fa-crown"></i> Owner</span>
                <span><i class="fa-solid fa-kitchen-set"></i> Kitchen</span>
                <span><i class="fa-solid fa-cash-register"></i> Kasir</span>
            </div>
        </div>
    </div>

    <div class="right-panel">
        <div class="form-container">

            <div class="header-title">
                <h2>LOGIN ADMIN</h2>
                <p>silahkan masuk untuk melanjutkan</p>
            </div>

            @if(session('success'))
                <div class="alert success">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.process') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">Username / Email</label>
                    <div class="input-box">
                        <i class="fa-solid fa-user icon-left"></i>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="masukan username"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <div class="input-box">
                        <i class="fa-solid fa-lock icon-left"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="masukan password"
                            autocomplete="current-password"
                            required
                        >

                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>

                    <div class="forgot-pass">
                        <a href="#">lupa password?</a>
                    </div>
                </div>

                <label class="remember-me">
                    <input type="checkbox" name="remember" value="1">
                    <span>Ingat Saya</span>
                </label>

                <button type="submit" class="btn-submit">
                    MASUK
                </button>
            </form>

            <div class="login-info">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Gunakan akun sesuai role Owner, Kitchen, atau Kasir.</span>
            </div>

        </div>

        <div class="footer-section">
            <div class="footer-line"></div>
            <div class="footer-text">
                © Copyright By Quattro Coffee
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('js/admin-login.js') }}"></script>
</body>
</html>
