<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Akses Ditolak - Quattro Coffee</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
</head>

<body>

<div class="app-container">

    <div
        style="
            min-height: 100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:30px 20px;
        "
    >

        <div
            style="
                width:100%;
                max-width:430px;
                text-align:center;
            "
        >

            {{-- ICON --}}

            <div
                style="
                    width:90px;
                    height:90px;
                    margin:0 auto 25px;
                    border-radius:50%;
                    background:#fff1e3;
                    color:#9a5409;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:36px;
                "
            >
                <i class="fa-solid fa-lock"></i>
            </div>


            {{-- TITLE --}}

            <h2
                style="
                    margin-bottom:12px;
                    color:#3e2723;
                "
            >
                Akses Ditolak
            </h2>


            {{-- DESCRIPTION --}}

            <p
                style="
                    color:#8c7b70;
                    line-height:1.7;
                    margin-bottom:25px;
                "
            >
                Maaf, akun kamu tidak memiliki hak akses
                ke halaman ini.
            </p>


            {{-- ROLE --}}

            @auth

                <div
                    style="
                        display:inline-flex;
                        align-items:center;
                        gap:8px;
                        background:#f8f3ee;
                        padding:10px 16px;
                        border-radius:10px;
                        color:#6d4c41;
                        font-size:13px;
                        margin-bottom:25px;
                    "
                >

                    <i class="fa-solid fa-user"></i>

                    Role:

                    <strong>
                        {{ auth()->user()->role }}
                    </strong>

                </div>

            @endauth


            {{-- BUTTON --}}

            @auth

                @if(auth()->user()->role === 'customer')

                    <a
                        href="{{ route('customer.home') }}"
                        class="btn-primary"
                        style="
                            display:block;
                            text-decoration:none;
                        "
                    >
                        <i class="fa-solid fa-house"></i>
                        Kembali ke Beranda
                    </a>

                @else

                    <a
                        href="{{ route('welcome') }}"
                        class="btn-primary"
                        style="
                            display:block;
                            text-decoration:none;
                        "
                    >
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>

                @endif

            @else

                <a
                    href="{{ route('customer.login') }}"
                    class="btn-primary"
                    style="
                        display:block;
                        text-decoration:none;
                    "
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Login
                </a>

            @endauth

        </div>

    </div>

</div>

</body>
</html>