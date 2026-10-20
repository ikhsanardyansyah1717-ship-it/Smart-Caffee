@extends('layouts.customer')

@section('title', 'Privacy Policy - Quattro Coffee')

@section('content')
<main class="app-container favorites-page privacy-page">
    <header class="favorites-page-header">
        <a class="favorites-back" href="{{ route('customer.profile') }}" aria-label="Kembali ke Profil">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
        </a>
        <h1>Privacy Policy</h1>
        <span aria-hidden="true"></span>
    </header>

    <article class="privacy-content">
        <p>Kebijakan ini menjelaskan cara Quattro Coffee menggunakan informasi saat Anda memakai layanan kami.</p>
        <h2>Informasi yang digunakan</h2>
        <p>Kami menggunakan nama dan informasi akun yang Anda berikan, detail pesanan, serta informasi yang diperlukan untuk pembayaran. Untuk pengunjung, daftar favorit dapat disimpan sementara di browser Anda.</p>
        <h2>Penggunaan informasi</h2>
        <p>Informasi digunakan untuk menerima dan menyiapkan pesanan, memproses pembayaran, menampilkan riwayat pesanan, menyimpan preferensi, dan membantu meningkatkan layanan kafe.</p>
        <h2>Penyimpanan dan keamanan</h2>
        <p>Quattro Coffee menjaga informasi dengan langkah yang wajar dan hanya menyimpannya selama diperlukan untuk layanan, operasional, atau kewajiban yang berlaku.</p>
        <h2>Pembagian informasi</h2>
        <p>Kami tidak menjual informasi pribadi Anda. Informasi hanya dapat dibagikan kepada penyedia layanan yang membantu operasional atau pembayaran, atau jika diwajibkan oleh hukum.</p>
        <h2>Pilihan Anda</h2>
        <p>Anda dapat menghubungi pihak Quattro Coffee untuk meminta bantuan terkait informasi akun Anda. Menghapus data browser dapat menghapus favorit pengunjung yang tersimpan di perangkat tersebut.</p>
        <p>Jika kebijakan ini berubah, versi terbaru akan ditampilkan di halaman ini.</p>
    </article>
</main>
@endsection
