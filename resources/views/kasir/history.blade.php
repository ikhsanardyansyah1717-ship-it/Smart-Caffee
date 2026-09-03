@extends('layouts.kasir')

@section('title', 'Riwayat - Quattro Coffee')

@section('content')

<div class="grid">

    {{-- HEADER --}}
    <header class="topbar">

        <div>

            <span class="eyebrow">
                CASHIER CONTROL
            </span>

            <h1>
                Riwayat Transaksi
            </h1>

            <p>
                Lihat transaksi yang sudah selesai.
            </p>

        </div>

    </header>


    {{-- PANEL --}}
    <section class="panel history-panel">

        <div class="panel-head">

            <div>

                <h2>
                    Transaksi Selesai
                </h2>

                <p>
                    Riwayat transaksi kasir.
                </p>

            </div>


            {{-- SEARCH --}}
            <div class="history-search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="historySearch"
                    placeholder="Cari transaksi..."
                    onkeyup="searchHistory()"
                >

            </div>

        </div>


        {{-- TABEL --}}
        <div class="history-table-wrapper">

            <table class="history-table">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Pelanggan
                        </th>

                        <th>
                            Pesanan
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Pembayaran
                        </th>

                    </tr>

                </thead>


                <tbody id="historyTableBody">

                    @forelse($orders as $order)

                        <tr class="history-row">

                            {{-- ID --}}
                            <td>

                                <strong>
                                    {{ $order->id }}
                                </strong>

                            </td>


                            {{-- PELANGGAN --}}
                            <td>

                                <div class="customer-info">

                                    <strong>
                                        {{ $order->customer_name }}
                                    </strong>

                                    <small>
                                        Meja:
                                        {{ $order->table_number ?? 'Take Away' }}
                                    </small>

                                </div>

                            </td>


                            {{-- PESANAN --}}
                            <td>

                                <div class="history-items">

                                    @forelse($order->items as $item)

                                        <div class="history-item">

                                            <span class="item-name">
                                                {{ $item->product_name }}
                                            </span>

                                            <span class="item-qty">
                                                × {{ $item->quantity }}
                                            </span>

                                        </div>

                                    @empty

                                        <span class="empty-item">
                                            Tidak ada produk
                                        </span>

                                    @endforelse

                                </div>

                            </td>


                            {{-- TOTAL --}}
                            <td>

                                <strong class="history-total">

                                    Rp {{ number_format($order->total, 0, ',', '.') }}

                                </strong>

                            </td>


                            {{-- STATUS --}}
                            <td>

                                <span class="history-badge success">

                                    <i class="fa-solid fa-circle-check"></i>

                                    {{ $order->status }}

                                </span>

                            </td>


                            {{-- PEMBAYARAN --}}
                            <td>

                                <div class="payment-info">

                                    <span class="history-badge paid">

                                        <i class="fa-solid fa-check"></i>

                                        {{ $order->payment_status }}

                                    </span>


                                    @if($order->payment)

                                        <small>

                                            {{ $order->payment->payment_method }}

                                        </small>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="history-empty"
                            >

                                <i class="fa-solid fa-receipt"></i>

                                <h3>
                                    Belum Ada Riwayat
                                </h3>

                                <p>
                                    Transaksi yang sudah selesai akan muncul di sini.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>



<script>

function searchHistory()
{

    const input =
        document.getElementById('historySearch')
            .value
            .toLowerCase();


    const rows =
        document.querySelectorAll('.history-row');


    rows.forEach(function(row)
    {

        const text =
            row.innerText.toLowerCase();


        if (text.includes(input))
        {

            row.style.display = '';

        }
        else
        {

            row.style.display = 'none';

        }

    });

}

</script>

@endsection