@extends('layouts.customer')

@section('title', 'Favorit - Quattro Coffee')

@section('content')

<div class="app-container favorites-page">

    {{-- HEADER --}}
    <header class="favorites-page-header">
        <a class="favorites-back" href="{{ route('customer.profile') }}" aria-label="Kembali ke Profil">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
        </a>
        <h1>Favorit Saya</h1>
        <span aria-hidden="true"></span>
    </header>


    {{-- FAVORITE LIST --}}
    <div
        class="fav-grid"
        id="fav-container"
    ></div>


</div>

@endsection


@push('scripts')

<script>
window.quattroUser = @json([
    'id' => auth()->id(),
    'name' => auth()->user()?->name ?? 'Sahabat Quattro',
    'email' => auth()->user()?->email
]);

window.quattroProducts = @json($products);

window.quattroFavoriteIds = @json($favoriteIds);
</script>

<script src="{{ asset('js/customer.js') }}?v=11"></script>

@endpush
