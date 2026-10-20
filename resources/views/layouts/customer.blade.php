<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>
        @yield('title', 'Quattro Coffee')
    </title>

    {{-- Font Awesome --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    {{-- CSS Customer --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/customer.css') }}?v=20261020-7"
    >

    {{-- CSS tambahan --}}
    @stack('styles')

</head>

<body>

    {{-- CONTENT --}}
    @yield('content')

    {{-- SCRIPT TAMBAHAN --}}
    @stack('scripts')

    <script src="{{ asset('js/customer-language.js') }}?v=6"></script>

</body>

</html>
