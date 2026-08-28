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

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f1eb;

            color: #3e2723;

            min-height: 100vh;

        }


        /* =================================
           HEADER
        ================================= */

        .header {

            height: 94px;

            background: #ffffff;

            border-bottom: 1px solid #e7dfd6;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 45px;

        }


        .brand {

            display: flex;

            flex-direction: column;

        }


        .brand-name {

            font-size: 26px;

            font-weight: 700;

            color: #3e2723;

            line-height: 1.2;

        }


        .brand-subtitle {

            margin-top: 5px;

            font-size: 15px;

            color: #8c7b70;

        }


        .status {

            display: flex;

            align-items: center;

            gap: 9px;

            padding: 11px 18px;

            border-radius: 25px;

            background: #fff2df;

            color: #a35b08;

            font-size: 14px;

            font-weight: 600;

        }


        .status i {

            font-size: 14px;

        }


        /* =================================
           MAIN
        ================================= */

        .main {

            min-height: calc(100vh - 94px);

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 50px;

        }


        /* =================================
           CARD
        ================================= */

        .access-card {

            width: 100%;

            max-width: 810px;

            min-height: 530px;

            background: #ffffff;

            border-radius: 28px;

            border: 1px solid #eee6de;

            box-shadow:
                0 20px 55px rgba(62, 39, 35, 0.08);

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            text-align: center;

            padding: 60px;

        }


        /* =================================
           ICON
        ================================= */

        .access-icon {

            width: 130px;

            height: 130px;

            border-radius: 50%;

            background: #fff0df;

            color: #a35b08;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 30px;

        }


        .access-icon i {

            font-size: 52px;

        }


        /* =================================
           TITLE
        ================================= */

        .access-card h1 {

            font-size: 38px;

            font-weight: 700;

            color: #3e2723;

            margin-bottom: 15px;

        }


        /* =================================
           DESCRIPTION
        ================================= */

        .description {

            max-width: 650px;

            color: #8c7b70;

            font-size: 17px;

            line-height: 1.7;

            margin-bottom: 25px;

        }


        /* =================================
           ROLE
        ================================= */

        .role-badge {

            display: inline-flex;

            align-items: center;

            gap: 10px;

            padding: 13px 22px;

            border-radius: 12px;

            background: #f8f3ee;

            color: #6d4c41;

            font-size: 14px;

            margin-bottom: 30px;

        }


        .role-badge i {

            color: #6d4c41;

        }


        .role-badge strong {

            color: #a35b08;

            text-transform: uppercase;

        }


        /* =================================
           BUTTON
        ================================= */

        .btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 10px;

            min-width: 240px;

            padding: 16px 28px;

            border-radius: 13px;

            background: #a35b08;

            color: #ffffff;

            text-decoration: none;

            font-size: 15px;

            font-weight: 700;

            box-shadow:
                0 8px 22px rgba(163, 91, 8, 0.20);

            transition: all 0.2s ease;

        }


        .btn:hover {

            background: #874a06;

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px rgba(163, 91, 8, 0.25);

        }


        /* =================================
           DESKTOP
        ================================= */

        @media (min-width: 1000px) {

            .main {

                padding: 55px 80px;

            }

            .access-card {

                max-width: 810px;

                min-height: 530px;

            }

        }


        /* =================================
           TABLET
        ================================= */

        @media (max-width: 900px) {

            .header {

                padding: 0 30px;

            }

            .access-card {

                max-width: 700px;

            }

        }


        /* =================================
           MOBILE
        ================================= */

        @media (max-width: 600px) {

            .header {

                height: auto;

                padding: 22px 20px;

            }

            .brand-name {

                font-size: 21px;

            }

            .brand-subtitle {

                font-size: 13px;

            }

            .status {

                display: none;

            }

            .main {

                min-height: calc(100vh - 82px);

                padding: 25px 15px;

            }

            .access-card {

                min-height: 500px;

                padding: 40px 25px;

                border-radius: 22px;

            }

            .access-icon {

                width: 105px;

                height: 105px;

            }

            .access-icon i {

                font-size: 42px;

            }

            .access-card h1 {

                font-size: 28px;

            }

            .description {

                font-size: 15px;

            }

            .btn {

                width: 100%;

                min-width: 0;

            }

        }

    </style>

</head>


<body>


    {{-- HEADER --}}

    <header class="header">

        <div class="brand">

            <div class="brand-name">
                Quattro Coffee
            </div>

            <div class="brand-subtitle">
                Management System
            </div>

        </div>


        <div class="status">

            <i class="fa-solid fa-circle-exclamation"></i>

            Akses Terbatas

        </div>

    </header>



    {{-- MAIN --}}

    <main class="main">


        <div class="access-card">


            {{-- ICON --}}

            <div class="access-icon">

                <i class="fa-solid fa-lock"></i>

            </div>



            {{-- TITLE --}}

            <h1>
                Akses Ditolak
            </h1>



            {{-- DESCRIPTION --}}

            <p class="description">

                Maaf, akun kamu tidak memiliki
                hak akses untuk membuka halaman ini.

                <br>

                Silakan kembali ke dashboard
                sesuai dengan role akun kamu.

            </p>



            @auth

                {{-- ROLE --}}

                <div class="role-badge">

                    <i class="fa-solid fa-user-shield"></i>

                    <span>
                        Role saat ini:
                    </span>

                    <strong>
                        {{ auth()->user()->role }}
                    </strong>

                </div>



                {{-- OWNER --}}

                @if(auth()->user()->role === 'owner')

                    <a
                        href="{{ route('owner.dashboard') }}"
                        class="btn"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Dashboard Owner

                    </a>


                {{-- KASIR --}}

                @elseif(auth()->user()->role === 'kasir')

                    <a
                        href="{{ route('kasir.dashboard') }}"
                        class="btn"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Dashboard Kasir

                    </a>


                {{-- KITCHEN --}}

                @elseif(auth()->user()->role === 'kitchen')

                    <a
                        href="{{ route('kitchen.dashboard') }}"
                        class="btn"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Dashboard Kitchen

                    </a>


                {{-- CUSTOMER --}}

                @elseif(auth()->user()->role === 'customer')

                    <a
                        href="{{ route('customer.home') }}"
                        class="btn"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Kembali ke Beranda

                    </a>


                {{-- ROLE LAIN --}}

                @else

                    <a
                        href="{{ route('admin.login') }}"
                        class="btn"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Kembali

                    </a>

                @endif


            @else

                {{-- BELUM LOGIN --}}

                <a
                    href="{{ route('admin.login') }}"
                    class="btn"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Kembali ke Login Admin

                </a>

            @endauth


        </div>

    </main>


</body>

</html>