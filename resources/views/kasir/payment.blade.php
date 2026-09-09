<form
    action="{{ route('kasir.payment.confirm', $order->id) }}"
    method="POST"
    onsubmit="return confirm('Konfirmasi pembayaran pesanan {{ $order->order_number }}?')"
>
    @csrf

    <button type="submit">
        <i class="fa-solid fa-check"></i>
        Konfirmasi Pembayaran
    </button>
</form>