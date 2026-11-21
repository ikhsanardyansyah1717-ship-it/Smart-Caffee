@extends('layouts.kitchen')

@section('title', 'Siap Diambil - Quattro Coffee')

@push('styles')
<style>
.kitchen-page .status-pill.status-ready{display:inline-flex;align-items:center;gap:6px;border:1px solid #d5e1f7;border-radius:999px;background:#edf3ff;color:#315b9d;font-weight:800}
.kitchen-page .pickup-actions .done-text{display:flex;align-items:center;justify-content:center;gap:9px;padding:13px 15px;border:1px solid #dce7f8;border-radius:12px;background:linear-gradient(135deg,#f3f7ff,#eaf1fc);color:#315b9d;font-size:12px;font-weight:750}
.kitchen-page .pickup-actions .done-text i{color:#4978c6}
.kitchen-page .pickup-actions .action-btn.btn-success{min-height:44px;border:1px solid rgba(32,132,82,.16);border-radius:12px;background:linear-gradient(135deg,rgba(229,244,235,.96),rgba(216,239,225,.88));color:#19804d;font-size:12px;font-weight:750;box-shadow:0 5px 13px rgba(23,109,67,.08);backdrop-filter:blur(8px)}
.kitchen-page .pickup-actions .action-btn.btn-success:hover{background:linear-gradient(135deg,#e3f3e9,#d4ecdd);box-shadow:0 8px 16px rgba(23,109,67,.12)}
@media(prefers-reduced-motion:reduce){.kitchen-page .pickup-actions .action-btn.btn-success{transition:none}}
</style>
@endpush

@section('content')

<div class="kitchen-page">

<header class="topbar">

    <div>

        <span class="eyebrow">
            QUATTRO COFFEE • KITCHEN
        </span>

        <h1>Siap Diambil</h1>

        <p>
            Kelola pesanan pelanggan dengan cepat dan rapi.
        </p>

    </div>

    <div class="top-actions">

        <span class="live">
            <i class="fa-solid fa-circle"></i>
            Live
        </span>

        <button class="icon-btn" onclick="refreshKitchen()">
            <i class="fa-solid fa-rotate"></i>
        </button>

    </div>

</header>


<div class="filter-bar">

    <div class="search">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            id="kitchen-search"
            type="text"
            placeholder="Cari ID pesanan atau nama pelanggan..."
        >

    </div>

    <select id="priority-filter">

        <option value="all">
            Semua Prioritas
        </option>

        <option value="priority">
            Prioritas
        </option>

        <option value="normal">
            Normal
        </option>

    </select>

</div>


<section class="panel">

    <div class="panel-head">

        <div>

            <h2>Siap Diambil</h2>

            <p>
                Daftar pesanan yang sudah selesai dibuat.
            </p>

        </div>

        <span class="order-count" id="page-count">
            {{ $orders->count() }} pesanan
        </span>

    </div>


    <div id="order-list" class="order-grid">

        @forelse($orders as $order)

            <div
                class="order-card kitchen-order-card"
                data-priority="{{ $order->priority ?? 'normal' }}"
            >

                <div class="order-top">

                    <div>

                        <div class="order-id">
                            #{{ $order->order_number }}
                        </div>

                        <div class="order-meta">

                            {{ $order->customer_name }}

                            @if($order->table_number)
                                • Meja {{ $order->table_number }}
                            @endif

                        </div>

                    </div>

                    <span class="status-pill status-ready">
                        <i class="fa-solid fa-bell" aria-hidden="true"></i>
                        Siap Diambil
                    </span>

                </div>


                <div class="items">

                    @foreach($order->items as $item)

                        <div class="item-line">

                            <span>
                                {{ $item->product_name }}
                                x{{ $item->quantity }}
                                @if($item->customization_summary)
                                    <small class="order-item-customization">{{ $item->customization_summary }}</small>
                                @endif
                            </span>

                            <span>
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>

                        </div>

                    @endforeach

                </div>


                <div class="order-actions kitchen-order-actions pickup-actions">

                    <div class="done-text">

                        <i class="fa-solid fa-circle-check"></i>

                        Status pesanan: siap diambil

                    </div>

                    <form
                        action="{{ route('kitchen.orders.pickup', $order->id) }}"
                        method="POST"
                        onsubmit="return openPickupModal(this)"
                    >
                        @csrf
                        <button type="submit" class="action-btn btn-success">
                            <i class="fa-solid fa-hand-holding-heart"></i>
                            Konfirmasi Sudah Diambil
                        </button>
                    </form>

                </div>

            </div>

        @empty

            <div class="empty-state">

                <i class="fa-solid fa-circle-check"></i>

                <p>
                    Belum ada pesanan yang siap diambil.
                </p>

            </div>

        @endforelse

    </div>

</section>

</div>


{{-- MODAL KONFIRMASI PICKUP --}}
<div class="pickup-modal" id="pickupModal" aria-hidden="true">
    <div class="pickup-modal-backdrop" onclick="closePickupModal()"></div>
    <div class="pickup-modal-card" role="dialog" aria-modal="true" aria-labelledby="pickupModalTitle">
        <div class="pickup-modal-icon">
            <i class="fa-solid fa-hand-holding-heart"></i>
        </div>
        <div class="pickup-modal-content">
            <span class="pickup-modal-label">KONFIRMASI PESANAN</span>
            <h3 id="pickupModalTitle">Pesanan sudah diambil?</h3>
            <p>Pastikan pesanan benar-benar sudah diterima pelanggan sebelum mengubah statusnya menjadi <strong>Sudah Diambil</strong>.</p>
        </div>
        <div class="pickup-modal-actions">
            <button type="button" class="pickup-modal-cancel" onclick="closePickupModal()">
                Batal
            </button>
            <button type="button" class="pickup-modal-confirm" onclick="submitPickupForm()">
                <i class="fa-solid fa-check"></i>
                Ya, Sudah Diambil
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let pickupFormTarget = null;

    function openPickupModal(form) {
        pickupFormTarget = form;
        const modal = document.getElementById('pickupModal');
        if (!modal) return true;
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('pickup-modal-open');
        return false;
    }

    function closePickupModal() {
        const modal = document.getElementById('pickupModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('pickup-modal-open');
        pickupFormTarget = null;
    }

    function submitPickupForm() {
        if (!pickupFormTarget) return;
        const form = pickupFormTarget;
        pickupFormTarget = null;
        form.removeAttribute('onsubmit');
        form.submit();
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closePickupModal();
    });
</script>
@endpush

@endsection
