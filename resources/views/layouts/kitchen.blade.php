<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>@yield('title','Quattro Coffee - Kitchen')</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/kitchen.css') }}">
</head>
<body><div class="kitchen-app">@yield('content')</div><script src="{{ asset('js/kitchen.js') }}"></script></body>
</html>