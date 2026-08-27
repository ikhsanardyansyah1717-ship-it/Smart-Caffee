<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
</head>
<body>
<div class="app-container">
    <div class="empty-state" style="padding-top:30vh">
        <i class="fa-solid fa-lock"></i>
        <p>Akses ditolak.<br>Role akun kamu tidak memiliki akses ke halaman ini.</p>
        <br>
        <a class="btn-primary" href="{{ route('role.dashboard') }}">Kembali</a>
    </div>
</div>
</body>
</html>
