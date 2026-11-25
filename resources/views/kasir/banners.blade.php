@extends($layout ?? 'layouts.kasir')

@section('title', 'Banner Customer - Quattro Coffee')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/banner-editor.css') }}?v=20261125-full-width-banner-editor">
@endpush

@section('content')
<div class="banner-editor-page">
<header class="topbar">
    <div>
        <span class="eyebrow">CUSTOMER HOME</span>
        <h1>Edit Banner Customer</h1>
        <p>Atur konten banner dan promo yang tampil di Home Customer.</p>
    </div>
</header>

@if(session('success'))
    <div class="banner-notice banner-notice-success"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="banner-notice banner-notice-error"><i class="fa-solid fa-triangle-exclamation"></i>{{ $errors->first() }}</div>
@endif

<form action="{{ route(($routePrefix ?? 'kasir') . '.banners.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="banner-editor-grid">
        @foreach($banners as $index => $banner)
            @php
                $bannerProduct = $products->firstWhere('id', old('banners.'.$index.'.product_id', $banner['product_id'] ?? null));
                $discountPercent = (int) old('banners.'.$index.'.discount_percent', $banner['discount_percent'] ?? 0);
                $imageUrl = str_starts_with($banner['image'], 'site-banners/') ? route('site-banners.image', ['filename' => basename($banner['image'])]) : $banner['image'];
                $hasCustomBannerImage = str_starts_with($banner['image'], 'site-banners/');
                $productImageUrl = $bannerProduct && $bannerProduct->image
                    ? (\Illuminate\Support\Str::startsWith(trim((string) $bannerProduct->image), ['http://', 'https://'])
                        ? $bannerProduct->image
                        : route('products.image', $bannerProduct, false) . '?v=' . substr(sha1(trim((string) $bannerProduct->image)), 0, 12))
                    : '';
                $imageSource = old('banners.'.$index.'.image_source', $banner['image_source'] ?? ($bannerProduct && $productImageUrl ? 'product' : ($hasCustomBannerImage ? 'custom' : 'banner')));
                $previewImageUrl = $bannerProduct && $imageSource === 'product' && $productImageUrl ? $productImageUrl : $imageUrl;
                $bannerTitle = $bannerProduct?->name ?? old('banners.'.$index.'.title', $banner['title']);
            @endphp
            <section class="panel banner-editor-card">
                <div class="banner-editor-card-head">
                    <div><span class="banner-editor-index">0{{ $index + 1 }}</span><h2>Banner {{ $index + 1 }}</h2></div>
                    <span class="banner-editor-live"><i class="fa-solid fa-circle"></i> Tampil di Customer</span>
                </div>

                <div class="banner-preview">
                    <div class="banner-preview-copy">
                        <span data-preview-badge>{{ old('banners.'.$index.'.badge', $banner['badge']) }}</span>
                        <strong data-preview-title>{{ old('banners.'.$index.'.title', $banner['title']) }}</strong>
                        <small data-preview-description>{{ old('banners.'.$index.'.description', $banner['description']) }}</small>
                        <div class="banner-promo-preview" data-promo-preview>
                            @if($bannerProduct && $discountPercent > 0)
                                <del>Rp {{ number_format((float) $bannerProduct->price, 0, ',', '.') }}</del>
                                <strong>Rp {{ number_format(round((float) $bannerProduct->price * (100 - $discountPercent) / 100), 0, ',', '.') }}</strong>
                                <span>-{{ $discountPercent }}%</span>
                            @else
                                <small>Pilih produk dan diskon untuk menampilkan harga promo</small>
                            @endif
                        </div>
                    </div>
                    <img data-preview-image data-default-src="{{ $imageUrl }}" src="{{ $previewImageUrl }}" alt="Pratinjau {{ $bannerTitle }}">
                </div>

                <div class="banner-fields">
                    <div class="banner-field">
                        <label for="banner-badge-{{ $index }}">Label kecil</label>
                        <input id="banner-badge-{{ $index }}" name="banners[{{ $index }}][badge]" type="text" maxlength="40" value="{{ old('banners.'.$index.'.badge', $banner['badge']) }}" data-field="badge" required>
                    </div>
                    <div class="banner-field">
                        <label for="banner-title-{{ $index }}">Judul banner</label>
                        <input id="banner-title-{{ $index }}" name="banners[{{ $index }}][title]" type="text" maxlength="80" value="{{ $bannerTitle }}" data-field="title" @readonly($bannerProduct) required>
                        <small data-title-note @if(!$bannerProduct) hidden @endif>Nama mengikuti produk yang dipilih agar tetap sama dengan katalog owner.</small>
                    </div>
                    <div class="banner-field banner-field-wide">
                        <label for="banner-description-{{ $index }}">Deskripsi</label>
                        <textarea id="banner-description-{{ $index }}" name="banners[{{ $index }}][description]" rows="2" maxlength="180" data-field="description" required>{{ old('banners.'.$index.'.description', $banner['description']) }}</textarea>
                    </div>
                    <div class="banner-field">
                        <label for="banner-product-{{ $index }}">Produk promo</label>
                        <select id="banner-product-{{ $index }}" name="banners[{{ $index }}][product_id]" data-field="product">
                            <option value="" data-price="0">Tidak ada promo produk</option>
                            @foreach($products as $product)
                                @php
                                    $optionImage = $product->image
                                        ? (\Illuminate\Support\Str::startsWith(trim((string) $product->image), ['http://', 'https://'])
                                            ? $product->image
                                            : route('products.image', $product, false) . '?v=' . substr(sha1(trim((string) $product->image)), 0, 12))
                                        : '';
                                @endphp
                                <option value="{{ $product->id }}" data-price="{{ (float) $product->price }}" data-name="{{ $product->name }}" data-image="{{ $optionImage }}" @selected((string) old('banners.'.$index.'.product_id', $banner['product_id'] ?? '') === (string) $product->id)>{{ $product->name }} · Rp {{ number_format((float) $product->price, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="banner-field">
                        <label for="banner-discount-{{ $index }}">Diskon (%)</label>
                        <div class="discount-input-wrap"><input id="banner-discount-{{ $index }}" name="banners[{{ $index }}][discount_percent]" type="number" min="0" max="90" step="1" value="{{ $discountPercent }}" data-field="discount" required><span>%</span></div>
                        <small>Harga promo dihitung otomatis dari harga produk.</small>
                    </div>
                    <div class="banner-field banner-field-wide">
                        <label for="banner-image-{{ $index }}">Gambar banner</label>
                        <div class="banner-upload-control">
                            <input id="banner-image-{{ $index }}" class="banner-file-input" name="banners[{{ $index }}][image]" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-field="image">
                            <input type="hidden" name="banners[{{ $index }}][image_source]" value="{{ $imageSource }}" data-image-source>
                            <label class="banner-upload-button" for="banner-image-{{ $index }}"><i class="fa-solid fa-arrow-up-from-bracket"></i> Pilih foto khusus</label>
                            <span data-upload-name>{{ $bannerProduct && $productImageUrl && $imageSource === 'product' ? 'Foto produk digunakan otomatis' : ($imageSource === 'custom' ? 'Foto khusus sedang digunakan' : 'Foto banner saat ini digunakan') }}</span>
                        </div>
                        <small>Foto produk terpilih digunakan otomatis. Jika ingin foto khusus, unggah JPG, PNG, atau WEBP maksimal 5 MB; bingkai Home memotong foto menjadi bulat.</small>
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    <div class="banner-editor-actions">
        <a href="{{ route(($routePrefix ?? 'kasir') . '.dashboard') }}" class="outline-btn">Kembali</a>
        <button type="submit" class="primary-btn"><i class="fa-solid fa-floppy-disk"></i> Simpan Banner & Promo</button>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.banner-editor-card').forEach(function (card) {
    card.querySelectorAll('[data-field="badge"], [data-field="title"], [data-field="description"]').forEach(function (field) {
        field.addEventListener('input', function () {
            const target = card.querySelector('[data-preview-' + field.dataset.field + ']');
            if (target) target.textContent = field.value || 'Pratinjau banner';
        });
    });

    const productSelect = card.querySelector('[data-field="product"]');
    const discountInput = card.querySelector('[data-field="discount"]');
    const promoPreview = card.querySelector('[data-promo-preview]');
    const titleInput = card.querySelector('[data-field="title"]');
    const titleNote = card.querySelector('[data-title-note]');
    const previewImage = card.querySelector('[data-preview-image]');
    const imageSourceInput = card.querySelector('[data-image-source]');
    const uploadButton = card.querySelector('.banner-upload-button');
    const uploadName = card.querySelector('[data-upload-name]');
    const formatRupiah = amount => 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(amount);
    const updatePromoPreview = function () {
        if (!productSelect || !discountInput || !promoPreview) return;
        const option = productSelect.options[productSelect.selectedIndex];
        const original = Number(option?.dataset.price || 0);
        const percent = Math.min(90, Math.max(0, Number(discountInput.value || 0)));
        promoPreview.replaceChildren();
        if (!productSelect.value || !original || !percent) {
            const note = document.createElement('small');
            note.textContent = 'Pilih produk dan diskon untuk menampilkan harga promo';
            promoPreview.append(note);
            return;
        }
        const oldPrice = document.createElement('del');
        oldPrice.textContent = formatRupiah(original);
        const salePrice = document.createElement('strong');
        salePrice.textContent = formatRupiah(Math.round(original * (100 - percent) / 100));
        const tag = document.createElement('span');
        tag.textContent = '-' + percent + '%';
        promoPreview.append(oldPrice, salePrice, tag);
    };
    productSelect?.addEventListener('change', function () {
        const option = productSelect.options[productSelect.selectedIndex];
        const selectedProduct = Boolean(productSelect.value);
        if (titleInput) {
            titleInput.readOnly = selectedProduct;
            if (selectedProduct) {
                titleInput.value = option.dataset.name || titleInput.value;
                titleInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
        if (titleNote) titleNote.hidden = !selectedProduct;
        if (previewImage && imageSourceInput) {
            if (selectedProduct && option.dataset.image) {
                imageSourceInput.value = 'product';
                const imageInput = card.querySelector('[data-field="image"]');
                if (imageInput) imageInput.value = '';
                previewImage.src = option.dataset.image;
                if (uploadName) uploadName.textContent = 'Foto produk digunakan otomatis';
                if (uploadButton) uploadButton.innerHTML = '<i class="fa-solid fa-arrow-up-from-bracket"></i> Pilih foto khusus';
            } else if (imageSourceInput.value === 'product') {
                imageSourceInput.value = 'banner';
                previewImage.src = previewImage.dataset.defaultSrc;
                if (uploadName) uploadName.textContent = 'Foto banner saat ini digunakan';
            }
        }
        updatePromoPreview();
    });
    discountInput?.addEventListener('input', updatePromoPreview);
    updatePromoPreview();

    const imageInput = card.querySelector('[data-field="image"]');
    if (imageInput && previewImage) {
        imageInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            previewImage.src = URL.createObjectURL(file);
            if (imageSourceInput) imageSourceInput.value = 'custom';
            if (uploadName) uploadName.textContent = file.name;
            if (uploadButton) uploadButton.innerHTML = '<i class="fa-solid fa-arrow-up-from-bracket"></i> Ganti foto khusus';
        });
    }
});
</script>
@endpush
