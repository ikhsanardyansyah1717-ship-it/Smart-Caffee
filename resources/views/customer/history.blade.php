@extends('layouts.customer')

@section('title', 'History Pesanan - Quattro Coffee')

@section('content')

<div class="app-container favorites-page">

    {{-- HEADER --}}
    <header class="favorites-page-header">
        <a class="favorites-back" href="{{ route('customer.profile') }}" aria-label="Kembali ke Profil">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
        </a>
        <h1>History Pesanan</h1>
        <span aria-hidden="true"></span>
    </header>

{{-- =========================================================
    TAB RIWAYAT PESANAN
========================================================= --}}

<div
    id="tab-history"
>
    <div id="history-container">

        @forelse($orders as $order)

            @php

                $statusClass = match($order->status) {

                    'Menunggu' => 'waiting',

                    'Diproses' => 'processing',

                    'Selesai' => 'completed',

                    'Sudah Diambil' => 'picked-up',

                    'Dibatalkan' => 'cancelled',

                    default => 'default',

                };


                $statusIcon = match($order->status) {

                    'Menunggu' => 'fa-clock',

                    'Diproses' => 'fa-spinner',

                    'Selesai' => 'fa-circle-check',

                    'Sudah Diambil' => 'fa-bag-shopping',

                    'Dibatalkan' => 'fa-circle-xmark',

                    default => 'fa-receipt',

                };


                $paymentStatus =
                    $order->payment?->status
                    ?? 'Belum ada pembayaran';


                $paymentMethod =
                    $order->payment?->payment_method
                    ?? 'Belum dipilih';


                $isPaid =
                    $paymentStatus === 'Berhasil';

            @endphp


            <article class="history-order-card">

                {{-- ORDER HEADER --}}
                <div class="history-order-header">

                    <div class="history-order-main">

                        <div class="history-order-icon">

                            <i class="fa-solid fa-receipt"></i>

                        </div>


                        <div>

                            <span class="history-order-number">

                                #{{ $order->order_number }}

                            </span>


                            <span class="history-order-date">

                                <i class="fa-regular fa-calendar"></i>

                                {{ $order->created_at->translatedFormat('d M Y') }}

                                <span class="history-dot">
                                    •
                                </span>

                                <i class="fa-regular fa-clock"></i>

                                {{ $order->created_at->format('H:i') }}

                            </span>

                        </div>

                    </div>


                    <span
                        class="history-order-status {{ $statusClass }}"
                    >

                        <i class="fa-solid {{ $statusIcon }}"></i>

                        {{ $order->status }}

                    </span>

                </div>


                {{-- ITEMS --}}
                <div class="history-order-items">

                    @forelse($order->items as $item)

                        <div class="history-item">

                            <div class="history-item-left">

                                <div class="history-product-icon">

                                    <i class="fa-solid fa-mug-hot"></i>

                                </div>


                                <div class="history-item-info">

                                    <strong class="history-item-name">

                                        {{ $item->product_name }}

                                    </strong>

                                    @if($item->customization_summary)
                                        <small class="cart-customization">{{ $item->customization_summary }}</small>
                                    @endif


                                    <span class="history-item-meta">

                                        {{ $item->quantity }}x

                                        <span>•</span>

                                        Rp
                                        {{ number_format($item->price ?? 0, 0, ',', '.') }}

                                    </span>

                                </div>

                            </div>


                            <strong class="history-item-price">

                                Rp
                                {{ number_format($item->subtotal, 0, ',', '.') }}

                            </strong>

                        </div>

                    @empty

                        <div class="history-no-items">

                            <i class="fa-solid fa-box-open"></i>

                            Detail produk tidak tersedia.

                        </div>

                    @endforelse

                </div>


                {{-- TOTAL --}}
                <div class="history-order-total-row">

                    <div>

                        <span class="history-total-label">

                            Total Pembayaran

                        </span>


                        <strong class="history-order-total">

                            Rp
                            {{ number_format($order->total, 0, ',', '.') }}

                        </strong>

                    </div>


                    @if($order->tax > 0)

                        <div class="history-tax">

                            Termasuk pajak

                            <span>

                                Rp
                                {{ number_format($order->tax, 0, ',', '.') }}

                            </span>

                        </div>

                    @endif

                </div>


                {{-- PAYMENT --}}
                <div class="history-payment">

                    <div class="history-payment-info">

                        <div
                            class="history-payment-icon {{ $isPaid ? 'paid' : '' }}"
                        >

                            <i
                                class="fa-solid {{ $isPaid ? 'fa-check' : 'fa-wallet' }}"
                            ></i>

                        </div>


                        <div>

                            <span class="history-payment-label">

                                Metode Pembayaran

                            </span>


                            <strong class="history-payment-value">

                                {{ $paymentMethod }}

                            </strong>

                        </div>

                    </div>


                    <span
                        class="history-payment-badge {{ $isPaid ? 'paid' : 'pending' }}"
                    >

                        <i
                            class="fa-solid {{ $isPaid ? 'fa-circle-check' : 'fa-clock' }}"
                        ></i>

                        {{ $paymentStatus }}

                    </span>

                </div>

            </article>


        @empty

            <div class="history-empty">

                <div class="history-empty-icon">

                    <i class="fa-solid fa-receipt"></i>

                </div>


                <h4>
                    Belum Ada Riwayat
                </h4>


                <p>
                    Pesanan yang sudah kamu buat akan otomatis muncul di sini.
                </p>

            </div>

        @endforelse

    </div>

</div>


</div>

@endsection
@push('scripts')
<script>
    window.quattroUser = @json([
        'id' => auth()->id(),
        'name' => auth()->user()?->name ?? 'Sahabat Quattro',
        'email' => auth()->user()?->email
    ]);
    window.quattroFavoriteIds = @json($favoriteIds);
    window.quattroProducts = @json($products);
</script>
<script src="{{ asset('js/customer.js') }}?v=14"></script>
@endpush
