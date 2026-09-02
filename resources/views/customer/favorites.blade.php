@extends('layouts.customer')

@section('title', 'Favorit - Quattro Coffee')

@section('content')

<div class="app-container">

    {{-- HEADER --}}
    <div class="top-nav-bar">

        <h3>
            Favorit Saya
        </h3>

    </div>


    {{-- FAVORITE LIST --}}
    <div
        class="fav-grid"
        id="fav-container"
    ></div>


    {{-- NAV --}}
    @include('customer.partials.nav')

</div>

@endsection


@push('scripts')

<script>

window.quattroUser = @json([
    'id' => auth()->id(),
    'name' => auth()->user()->name,
    'email' => auth()->user()->email
]);

</script>

<script src="{{ asset('js/customer.js') }}"></script>

@endpush