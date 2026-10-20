@extends('layouts.owner')
@section('title','Catatan Keuangan - Quattro Coffee')
@section('content')
@php
    $rp = fn ($v) => ($v < 0 ? '-' : '') . 'Rp ' . number_format(abs($v), 0, ',', '.');
    $inputStyle = 'height:42px;padding:0 12px;border:1px solid #e5ddd0;border-radius:10px;background:#fff;font:inherit;width:100%;box-sizing:border-box;';
    $labelStyle = 'display:flex;flex-direction:column;gap:6px;font-size:12px;font-weight:600;color:#8a7a68;';
    $filterLabel = 'display:flex;flex-direction:column;gap:4px;font-size:11px;font-weight:600;color:#8a7a68;text-transform:uppercase;letter-spacing:.04em;';
    $filterInput = 'height:38px;padding:0 10px;border:1px solid #e5ddd0;border-radius:10px;background:#fff;font:inherit;font-size:13px;box-sizing:border-box;';
    $tabBase = 'padding:7px 14px;border-radius:999px;font-size:13px;font-weight:600;text-decoration:none;border:1px solid #e5ddd0;';
    $tab = fn ($active) => $tabBase . ($active ? 'background:#7a4100;color:#fff;border-color:#7a4100;' : 'background:#fff;color:#6b5a48;');
    $periodParams = ['from' => $from, 'to' => $to];
@endphp
<div class="grid">
<header class="page-head">
    <div><span class="eyebrow">FINANCE NOTES</span><h1>Catatan Keuangan</h1><p>Catat pemasukan dan pengeluaran harian di luar penjualan kasir.</p></div>
    <div class="head-actions">
        <a href="{{ route('owner.reports', ['month' => $selectedMonth]) }}" class="btn btn-light"><i class="fa-solid fa-file-lines"></i> Lihat Laporan</a>
    </div>
</header>

@if (session('success'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;background:#e8f5ee;color:#1f6b45;font-size:14px;">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:12px;background:#fdecec;color:#a32121;font-size:14px;">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

{{-- Form tambah catatan --}}
<section class="panel">
    <div class="panel-head"><div><h2>Tambah Catatan</h2><p>Pilih jenis catatan lalu isi detailnya.</p></div></div>
    <form method="POST" action="{{ route('owner.expenses.store') }}" id="expenseForm" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;align-items:start;">
        @csrf
        <label style="{{ $labelStyle }}">Jenis
            <select name="type" id="typeSelect" style="{{ $inputStyle }}" required>
                <option value="pemasukan" @selected(old('type') === 'pemasukan')>Pemasukan</option>
                <option value="pengeluaran" @selected(old('type', 'pengeluaran') === 'pengeluaran')>Pengeluaran</option>
            </select>
        </label>
        <label style="{{ $labelStyle }}">Tanggal
            <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" style="{{ $inputStyle }}" required>
        </label>
        <label style="{{ $labelStyle }}">Kategori
            <input type="text" name="category" id="categoryInput" list="listPengeluaran" value="{{ old('category') }}" placeholder="Contoh: Gaji" style="{{ $inputStyle }}" required>
        </label>
        <label style="{{ $labelStyle }}">Nominal (Rp)
            <input type="text" id="amountDisplay" inputmode="numeric" autocomplete="off" placeholder="0" style="{{ $inputStyle }}" required>
            <input type="hidden" name="amount" id="amountInput" value="{{ old('amount') }}">
        </label>
        <label style="{{ $labelStyle }};grid-column:1 / -1;">Keterangan (opsional)
            <input type="text" name="description" value="{{ old('description') }}" placeholder="Contoh: Gaji barista bulan Oktober" style="{{ $inputStyle }}">
        </label>
        <div style="grid-column:1 / -1;">
            <button type="submit" class="btn btn-primary" style="padding:10px 22px;">Simpan Catatan</button>
        </div>
    </form>

    <datalist id="listPengeluaran">
        <option value="Gaji"><option value="Sewa"><option value="Listrik"><option value="Air">
        <option value="Bahan Baku"><option value="Pemasaran"><option value="Perawatan"><option value="Lainnya">
    </datalist>
    <datalist id="listPemasukan">
        <option value="Catering"><option value="Event"><option value="Penjualan Lain"><option value="Lainnya">
    </datalist>
</section>

{{-- Ringkasan periode --}}
<section class="panel" style="margin-top:22px">
    <div class="panel-head" style="display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;">
        <div><h2>Riwayat Catatan</h2><p>Periode {{ $periodLabel }}.</p></div>

        <form method="GET" action="{{ route('owner.expenses') }}" style="display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;">
            @if ($type)<input type="hidden" name="type" value="{{ $type }}">@endif
            <label style="{{ $filterLabel }}">Bulan
                <input type="month" id="monthInput" name="month" value="{{ $selectedMonth }}" style="{{ $filterInput }}">
            </label>
            <label style="{{ $filterLabel }}">Dari Tanggal
                <input type="date" id="fromInput" name="from" value="{{ $from }}" style="{{ $filterInput }}">
            </label>
            <label style="{{ $filterLabel }}">Sampai Tanggal
                <input type="date" id="toInput" name="to" value="{{ $to }}" style="{{ $filterInput }}">
            </label>
            <button type="submit" class="btn btn-primary" style="height:38px;padding:0 20px;">Terapkan</button>
            <a href="{{ route('owner.expenses', $type ? ['type' => $type] : []) }}" class="btn btn-light" style="height:38px;padding:0 18px;display:inline-flex;align-items:center;">Bulan ini</a>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:18px;">
        <div style="padding:14px 16px;border-radius:14px;background:#e8f5ee;">
            <div style="font-size:12px;color:#1f6b45;">Total Pemasukan</div>
            <div style="font-size:20px;font-weight:700;color:#1f6b45;">{{ $rp($totalPemasukan) }}</div>
        </div>
        <div style="padding:14px 16px;border-radius:14px;background:#fdecec;">
            <div style="font-size:12px;color:#a32121;">Total Pengeluaran</div>
            <div style="font-size:20px;font-weight:700;color:#a32121;">{{ $rp($totalPengeluaran) }}</div>
        </div>
        <div style="padding:14px 16px;border-radius:14px;background:#f4ede2;">
            <div style="font-size:12px;color:#7a4100;">Saldo Catatan</div>
            <div style="font-size:20px;font-weight:700;color:{{ $saldo < 0 ? '#a32121' : '#7a4100' }};">{{ $rp($saldo) }}</div>
        </div>
    </div>

    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
        <a href="{{ route('owner.expenses', $periodParams) }}" style="{{ $tab(! $type) }}">Semua</a>
        <a href="{{ route('owner.expenses', $periodParams + ['type' => 'pemasukan']) }}" style="{{ $tab($type === 'pemasukan') }}">Pemasukan</a>
        <a href="{{ route('owner.expenses', $periodParams + ['type' => 'pengeluaran']) }}" style="{{ $tab($type === 'pengeluaran') }}">Pengeluaran</a>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead>
                <tr style="text-align:left;color:#8a7a68;font-size:12px;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #eadfcf;">
                    <th style="padding:10px 8px;">Tanggal</th>
                    <th style="padding:10px 8px;">Jenis</th>
                    <th style="padding:10px 8px;">Kategori</th>
                    <th style="padding:10px 8px;">Keterangan</th>
                    <th style="padding:10px 8px;text-align:right;">Nominal</th>
                    <th style="padding:10px 8px;"></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($records as $r)
                @php $masuk = $r->type === 'pemasukan'; @endphp
                <tr style="border-bottom:1px solid #f1e9db;">
                    <td style="padding:12px 8px;white-space:nowrap;">{{ $r->expense_date->format('d M Y') }}</td>
                    <td style="padding:12px 8px;">
                        <span style="padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600;{{ $masuk ? 'background:#e8f5ee;color:#1f6b45;' : 'background:#fdecec;color:#a32121;' }}">
                            {{ $masuk ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                    </td>
                    <td style="padding:12px 8px;">{{ $r->category }}</td>
                    <td style="padding:12px 8px;color:#7d6e5c;">{{ $r->description ?: '-' }}</td>
                    <td style="padding:12px 8px;text-align:right;font-weight:700;white-space:nowrap;color:{{ $masuk ? '#1f6b45' : '#a32121' }};">
                        {{ $masuk ? '+' : '-' }} {{ $rp($r->amount) }}
                    </td>
                    <td style="padding:12px 8px;text-align:right;">
                        <button type="button" title="Hapus" class="js-hapus"
                                data-action="{{ route('owner.expenses.destroy', $r) }}"
                                data-kategori="{{ $r->category }}"
                                data-jenis="{{ $masuk ? 'Pemasukan' : 'Pengeluaran' }}"
                                data-nominal="{{ $rp($r->amount) }}"
                                style="background:none;border:none;color:#a32121;cursor:pointer;font-size:15px;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="padding:28px;text-align:center;color:#888;">Belum ada catatan di periode ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
</div>

{{-- Pop up konfirmasi hapus --}}
<div id="hapusModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:20px;background:rgba(30,20,10,.55);">
    <div role="dialog" aria-modal="true" aria-labelledby="hapusJudul" style="width:100%;max-width:400px;background:#fff;border-radius:18px;padding:26px 24px 22px;box-shadow:0 20px 50px rgba(0,0,0,.25);text-align:center;">
        <div style="width:56px;height:56px;margin:0 auto 14px;border-radius:50%;background:#fdecec;color:#a32121;display:flex;align-items:center;justify-content:center;font-size:22px;">
            <i class="fa-solid fa-trash"></i>
        </div>
        <h3 id="hapusJudul" style="margin:0 0 6px;font-size:18px;color:#2b1d0e;">Hapus catatan ini?</h3>
        <p style="margin:0 0 14px;font-size:14px;color:#8a7a68;">Catatan yang dihapus tidak bisa dikembalikan.</p>
        <div style="margin-bottom:20px;padding:12px 14px;border-radius:12px;background:#f7f2ea;font-size:14px;color:#4a3b2a;">
            <div id="hapusInfo" style="font-weight:600;"></div>
            <div id="hapusNominal" style="margin-top:2px;font-weight:700;color:#7a4100;"></div>
        </div>
        <form id="hapusForm" method="POST" action="" style="display:flex;gap:10px;">
            @csrf
            @method('DELETE')
            <button type="button" id="hapusBatal" class="btn btn-light" style="flex:1;height:42px;">Batal</button>
            <button type="submit" style="flex:1;height:42px;border:none;border-radius:10px;background:#a32121;color:#fff;font:inherit;font-weight:700;cursor:pointer;">Ya, Hapus</button>
        </form>
    </div>
</div>

<script>
    // Saran kategori menyesuaikan jenis catatan
    (function () {
        const type = document.getElementById('typeSelect');
        const cat = document.getElementById('categoryInput');
        function sync() {
            const masuk = type.value === 'pemasukan';
            cat.setAttribute('list', masuk ? 'listPemasukan' : 'listPengeluaran');
            cat.placeholder = masuk ? 'Contoh: Catering' : 'Contoh: Gaji';
        }
        type.addEventListener('change', sync);
        sync();
    })();

    // Nominal otomatis berformat ribuan (1000 -> 1.000)
    (function () {
        const display = document.getElementById('amountDisplay');
        const hidden = document.getElementById('amountInput');
        const form = document.getElementById('expenseForm');

        function formatRibuan(digits) {
            return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function update() {
            const digits = display.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '');
            display.value = formatRibuan(digits);
            hidden.value = digits;
        }

        display.addEventListener('input', function () {
            const posDariKanan = display.value.length - display.selectionStart;
            update();
            const posBaru = Math.max(display.value.length - posDariKanan, 0);
            display.setSelectionRange(posBaru, posBaru);
        });

        if (hidden.value) {
            const awal = String(Math.round(parseFloat(hidden.value))).replace(/\D/g, '');
            display.value = formatRibuan(awal);
            hidden.value = awal;
        }

        form.addEventListener('submit', update);
    })();

    // Pilih bulan -> tanggal awal & akhir bulan terisi otomatis
    (function () {
        const month = document.getElementById('monthInput');
        const from = document.getElementById('fromInput');
        const to = document.getElementById('toInput');
        month.addEventListener('change', function () {
            if (!month.value) return;
            const [y, m] = month.value.split('-').map(Number);
            const pad = (n) => String(n).padStart(2, '0');
            const last = new Date(y, m, 0).getDate();
            from.value = y + '-' + pad(m) + '-01';
            to.value = y + '-' + pad(m) + '-' + pad(last);
        });
    })();

    // Pop up konfirmasi hapus
    (function () {
        const modal = document.getElementById('hapusModal');
        const form = document.getElementById('hapusForm');
        const info = document.getElementById('hapusInfo');
        const nominal = document.getElementById('hapusNominal');
        const batal = document.getElementById('hapusBatal');

        function buka(btn) {
            form.action = btn.dataset.action;
            info.textContent = btn.dataset.jenis + ' - ' + btn.dataset.kategori;
            nominal.textContent = btn.dataset.nominal;
            modal.style.display = 'flex';
        }

        function tutup() {
            modal.style.display = 'none';
            form.action = '';
        }

        document.querySelectorAll('.js-hapus').forEach(function (btn) {
            btn.addEventListener('click', function () { buka(btn); });
        });

        batal.addEventListener('click', tutup);

        // Klik area gelap di luar kotak menutup pop up
        modal.addEventListener('click', function (e) {
            if (e.target === modal) tutup();
        });

        // Tombol Esc menutup pop up
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') tutup();
        });
    })();
</script>
@endsection