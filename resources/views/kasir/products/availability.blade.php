@extends('layouts.kasir')

@section('title', 'Manajemen Produk - Quattro Coffee')

@section('content')
<header class="topbar">
    <div><span class="eyebrow">CASHIER CONTROL</span><h1>Manajemen Produk</h1><p>Kelola menu, gambar, harga, dan stok dengan tampilan ringkas.</p></div>
    <div class="top-actions"><a href="{{ route('kasir.dashboard') }}" class="outline-btn">Kembali ke Dashboard</a></div>
</header>

@if(session('success'))<div class="notice notice-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="notice notice-error">{{ session('error') }}</div>@endif
@if($errors->any())<div class="notice notice-error"><strong>Periksa kembali data:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<style>
.product-stats{margin-top:16px}.notice{margin:14px 0;padding:12px 16px;border-radius:10px}.notice-success{background:#e8f7ed;color:#176534}.notice-error{background:#fdeaea;color:#9d2020}.notice ul{margin:6px 0 0 18px}
.product-details{margin-top:18px;background:#fff;border:1px solid #eee2d8;border-radius:14px;overflow:hidden}.product-details summary{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;cursor:pointer;list-style:none;font-weight:700;color:#4d3829}.product-details summary::-webkit-details-marker{display:none}.product-details summary .summary-sub{display:block;font-size:12px;font-weight:400;color:#88796d;margin-top:4px}.summary-plus{width:32px;height:32px;border-radius:10px;background:#f5eee8;display:grid;place-items:center;color:#795638}.product-form-grid{padding:0 18px 18px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px 14px}.product-field{display:flex;flex-direction:column;gap:6px;font-size:13px;font-weight:600;color:#514237}.product-field input,.product-field select,.product-field textarea{box-sizing:border-box;width:100%;padding:10px 11px;border:1px solid #e5dbd1;border-radius:9px;background:#fff;color:#302820;font:inherit;font-weight:400;outline:none}.product-field input:focus,.product-field select:focus,.product-field textarea:focus{border-color:#98704f;box-shadow:0 0 0 3px #98704f1c}.product-span{grid-column:1/-1}.product-image-preview{display:flex;align-items:center;gap:10px;margin-top:5px;color:#7b6c60;font-size:12px;font-weight:400}.product-image-preview img{width:56px;height:56px;object-fit:cover;border-radius:9px;border:1px solid #eee2d8}.product-table-image{width:48px;height:48px;object-fit:cover;border-radius:9px;border:1px solid #eee2d8;display:block}.product-fallback{width:48px;height:48px;display:grid;place-items:center;background:#f8f2ed;color:#8b715c;border-radius:9px}.product-action-row{display:flex;gap:7px;flex-wrap:wrap}.product-action{border:1px solid #e4d8cd;background:#fff;color:#62472f;border-radius:8px;padding:7px 10px;cursor:pointer;font:inherit;font-size:12px}.product-action:hover{background:#f8f1eb}.product-action-danger{border-color:#e4aaaa;color:#b42318}.product-action-danger:hover{background:#fff0f0}.product-modal-backdrop{position:fixed;inset:0;background:rgba(28,20,15,.48);display:flex;align-items:center;justify-content:center;padding:18px;z-index:9999;opacity:0;visibility:hidden;transition:opacity .22s ease,visibility .22s ease}.product-modal-backdrop.is-open{opacity:1;visibility:visible}.product-modal{width:min(620px,100%);max-height:90vh;overflow:auto;background:#fff;border-radius:18px;box-shadow:0 24px 70px rgba(0,0,0,.24);padding:22px;transform:translateY(18px) scale(.97);transition:transform .22s ease}.product-modal-backdrop.is-open .product-modal{transform:translateY(0) scale(1)}.product-modal-small{width:min(410px,100%);text-align:center}.modal-symbol{width:54px;height:54px;margin:0 auto 12px;border-radius:50%;display:grid;place-items:center;background:#fff0f0;color:#b42318;font-size:23px}.product-modal h3{margin:0 0 6px;color:#3d2d22}.product-modal p{margin:0;color:#817368;font-size:13px}.modal-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:20px}.modal-actions .primary-btn,.modal-actions .outline-btn{cursor:pointer}.modal-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px}.modal-span{grid-column:1/-1}.modal-current-image{width:70px;height:70px;object-fit:cover;border-radius:10px;border:1px solid #eee2d8}.modal-head{display:flex;justify-content:space-between;align-items:center;gap:12px}.modal-close{border:0;background:#f5eee8;color:#62472f;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:18px}.table-product-name{font-weight:700;color:#3f3025}.table-product-desc{display:block;max-width:220px;color:#88796d;font-size:12px;margin-top:4px}
@media(max-width:700px){.product-form-grid,.modal-form-grid{grid-template-columns:1fr}.product-span,.modal-span{grid-column:auto}.product-modal{padding:17px}.product-details summary{padding:14px}}
@media(prefers-reduced-motion:reduce){.product-modal-backdrop,.product-modal{transition:none}}
</style>

<section class="stats-grid product-stats">
    <div class="stat-card"><div class="stat-icon brown"><i class="fa-solid fa-boxes-stacked"></i></div><div><span>Total Produk</span><strong>{{ $totalProducts }}</strong><small>Semua produk terdaftar</small></div></div>
    <div class="stat-card"><div class="stat-icon orange"><i class="fa-solid fa-triangle-exclamation"></i></div><div><span>Stok Menipis</span><strong>{{ $lowStockCount }}</strong><small>Stok 1 sampai 5</small></div></div>
    <div class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-circle-xmark"></i></div><div><span>Stok Habis</span><strong>{{ $outOfStockCount }}</strong><small>Stok 0</small></div></div>
</section>

<details class="product-details" {{ $errors->any() && !old('_editing_product') ? 'open' : '' }}>
    <summary><span>Tambah Menu<span class="summary-sub">Buka bagian ini hanya saat ingin menambahkan produk baru.</span></span><span class="summary-plus"><i class="fa-solid fa-plus"></i></span></summary>
    <form method="POST" action="{{ route('kasir.products.store') }}" enctype="multipart/form-data" class="product-form-grid">
        @csrf
        <label class="product-field">Nama menu<input name="name" value="{{ old('name') }}" required maxlength="255" placeholder="Contoh: Cappuccino"></label>
        <label class="product-field">Kategori menu
            <select name="category" class="category-icon-select" required><option value="">Pilih kategori</option><option value="Coffee" data-icon="fa-solid fa-mug-hot" @selected(old('category') === 'Coffee')>☕ Coffee</option><option value="Non Coffee" data-icon="fa-solid fa-glass-water" @selected(old('category') === 'Non Coffee')>🥤 Non Coffee</option><option value="Snack" data-icon="fa-solid fa-cookie-bite" @selected(old('category') === 'Snack')>🍪 Snack</option><option value="Makanan Berat" data-icon="fa-solid fa-utensils" @selected(old('category') === 'Makanan Berat')>🍽️ Makanan Berat</option></select>
            <input type="hidden" name="icon" class="category-icon-value" value="{{ old('icon', 'fa-solid fa-mug-hot') }}">
        </label>
        <label class="product-field">Harga (Rp)<input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01" required placeholder="15000"></label>
        <label class="product-field">Stok awal<input type="number" name="stock" value="{{ old('stock') }}" min="0" step="1" required></label>
        <label class="product-field product-span">Foto menu
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="image-file-input">
            <span style="font-size:11px;color:#82766b;font-weight:400;">JPG, PNG, atau WEBP. Maksimal 2 MB.</span><span class="product-image-preview image-preview" hidden><img alt="Pratinjau foto menu"><span>Pratinjau gambar</span></span>
        </label>
        <label class="product-field product-span">Deskripsi <span style="font-weight:400;color:#82766b;">(opsional)</span><textarea name="description" rows="2" maxlength="5000" placeholder="Deskripsi singkat menu">{{ old('description') }}</textarea></label>
        <div class="product-span"><button type="submit" class="primary-btn"><i class="fa-solid fa-plus"></i> Simpan Menu</button></div>
    </form>
</details>

<section class="panel" style="margin-top:20px;">
    <div class="panel-head"><div><h2>Daftar Menu</h2><p>Tekan Edit untuk mengubah data atau stok. Stok 1–5 akan diberi peringatan, stok 0 berarti habis.</p></div></div>
    <div class="table-wrap"><table>
        <thead><tr><th>Foto</th><th>Menu</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Ketersediaan</th><th>Aksi</th></tr></thead><tbody>
        @forelse($products as $product)
            <tr>
                <td>
                    @if($product->image)
                        @php
                            $storedImagePath = preg_replace(
                                '#^(public/|storage/)#',
                                '',
                                ltrim($product->image, '/')
                            );
                            $productImageUrl = filter_var($product->image, FILTER_VALIDATE_URL)
                                ? $product->image
                                : asset('storage/' . $storedImagePath);
                        @endphp
                        <img
                            src="{{ $productImageUrl }}"
                            alt="{{ $product->name }}"
                            class="product-table-image"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                        >
                        <div class="product-fallback" style="display:none;">
                            <i class="{{ $product->icon ?: 'fa-solid fa-mug-hot' }}"></i>
                        </div>
                    @else
                        <div class="product-fallback">
                            <i class="{{ $product->icon ?: 'fa-solid fa-mug-hot' }}"></i>
                        </div>
                    @endif
                </td>
                <td><span class="table-product-name">{{ $product->name }}</span>@if($product->description)<small class="table-product-desc">{{ $product->description }}</small>@endif</td>
                <td>{{ $product->category }}</td><td>Rp {{ number_format((float)$product->price, 0, ',', '.') }}</td>
                <td><strong style="color:{{ $product->stock <= 0 ? '#b42318' : ($product->stock <= 5 ? '#b76e00' : '#236b3b') }};">{{ $product->stock }}</strong></td>
                <td>@if($product->stock <= 0)<span class="badge red">Stok Habis</span>@elseif($product->stock <= 5)<span class="badge" style="background:#fff0d4;color:#8a5100;">Stok Menipis</span>@else<span class="badge green">Tersedia</span>@endif</td>
                <td><div class="product-action-row"><button type="button" class="product-action" data-open-modal="edit-product-{{ $product->id }}"><i class="fa-solid fa-pen"></i> Edit</button><button type="button" class="product-action product-action-danger" data-delete-url="{{ route('kasir.products.destroy', $product->id) }}" data-product-name="{{ $product->name }}"><i class="fa-solid fa-trash"></i> Hapus</button></div></td>
            </tr>
        @empty<tr><td colspan="7" style="text-align:center;padding:30px;">Belum ada data produk.</td></tr>@endforelse
        </tbody>
    </table></div>
</section>

@foreach($products as $product)
<div class="product-modal-backdrop" id="edit-product-{{ $product->id }}" aria-hidden="true">
    <section class="product-modal" role="dialog" aria-modal="true" aria-labelledby="edit-title-{{ $product->id }}">
        <div class="modal-head"><div><h3 id="edit-title-{{ $product->id }}">Edit Menu</h3><p>Perubahan stok disimpan bersama data menu.</p></div><button type="button" class="modal-close" data-close-modal aria-label="Tutup">&times;</button></div>
        <form method="POST" enctype="multipart/form-data" action="{{ route('kasir.products.update', $product->id) }}">
            @csrf @method('PUT')
            <div class="modal-form-grid">
                <label class="product-field">Nama menu<input name="name" value="{{ $product->name }}" required maxlength="255"></label>
                <label class="product-field">Kategori<select name="category" class="category-icon-select" required><option value="Coffee" data-icon="fa-solid fa-mug-hot" @selected($product->category === 'Coffee')>☕ Coffee</option><option value="Non Coffee" data-icon="fa-solid fa-glass-water" @selected($product->category === 'Non Coffee')>🥤 Non Coffee</option><option value="Snack" data-icon="fa-solid fa-cookie-bite" @selected($product->category === 'Snack')>🍪 Snack</option><option value="Makanan Berat" data-icon="fa-solid fa-utensils" @selected($product->category === 'Makanan Berat')>🍽️ Makanan Berat</option></select><input type="hidden" name="icon" class="category-icon-value" value="{{ $product->icon ?: 'fa-solid fa-mug-hot' }}"></label>
                <label class="product-field">Harga (Rp)<input type="number" name="price" value="{{ $product->price }}" min="0" step="0.01" required></label>
                <label class="product-field">Jumlah stok<input type="number" name="stock" value="{{ $product->stock }}" min="0" step="1" required></label>
                <label class="product-field modal-span">Foto menu baru (opsional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"><span style="font-size:11px;color:#82766b;font-weight:400;">Kosongkan jika ingin mempertahankan foto sekarang. JPG, PNG, WEBP maks. 2 MB.</span></label>
                <label class="product-field modal-span">Deskripsi<textarea name="description" rows="2" maxlength="5000">{{ $product->description }}</textarea></label>
            </div>
            <div class="modal-actions"><button type="button" class="outline-btn" data-close-modal>Batal</button><button type="submit" class="primary-btn"><i class="fa-solid fa-check"></i> Simpan Perubahan</button></div>
        </form>
    </section>
</div>
@endforeach

<div class="product-modal-backdrop" id="delete-product-modal" aria-hidden="true">
    <section class="product-modal product-modal-small" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
        <div class="modal-symbol"><i class="fa-solid fa-trash-can"></i></div><h3 id="delete-modal-title">Hapus produk?</h3><p id="delete-modal-message">Produk ini akan dihapus dari daftar menu.</p>
        <form method="POST" id="delete-product-form" action="">
            @csrf @method('DELETE')
            <div class="modal-actions" style="justify-content:center;"><button type="button" class="outline-btn" data-close-modal>Batal</button><button type="submit" class="primary-btn" style="background:#b42318;border-color:#b42318;"><i class="fa-solid fa-trash"></i> Ya, Hapus</button></div>
        </form>
    </section>
</div>

<script>
(function () {
    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden';
    }
    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.product-modal-backdrop.is-open')) document.body.style.overflow = '';
    }
    document.querySelectorAll('[data-open-modal]').forEach(function (button) {
        button.addEventListener('click', function () { openModal(document.getElementById(button.dataset.openModal)); });
    });
    document.querySelectorAll('[data-close-modal]').forEach(function (button) {
        button.addEventListener('click', function () { closeModal(button.closest('.product-modal-backdrop')); });
    });
    document.querySelectorAll('.product-modal-backdrop').forEach(function (modal) {
        modal.addEventListener('click', function (event) { if (event.target === modal) closeModal(modal); });
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') document.querySelectorAll('.product-modal-backdrop.is-open').forEach(closeModal);
    });
    const deleteModal = document.getElementById('delete-product-modal');
    document.querySelectorAll('[data-delete-url]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('delete-product-form').action = button.dataset.deleteUrl;
            document.getElementById('delete-modal-message').textContent = 'Produk "' + button.dataset.productName + '" akan dihapus. Tindakan ini tidak bisa dibatalkan.';
            openModal(deleteModal);
        });
    });
    document.querySelectorAll('.category-icon-select').forEach(function (select) {
        function syncIcon() { const option = select.options[select.selectedIndex]; const form = select.closest('form'); const input = form ? form.querySelector('.category-icon-value') : null; if (input && option) input.value = option.dataset.icon || 'fa-solid fa-mug-hot'; }
        select.addEventListener('change', syncIcon); syncIcon();
    });
    document.querySelectorAll('.image-file-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const preview = input.parentElement.querySelector('.image-preview');
            if (!preview || !input.files || !input.files[0]) { if (preview) preview.hidden = true; return; }
            preview.querySelector('img').src = URL.createObjectURL(input.files[0]); preview.hidden = false;
        });
    });
})();
</script>
@endsection
