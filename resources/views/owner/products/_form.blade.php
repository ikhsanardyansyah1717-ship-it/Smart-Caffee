{{-- =====================================================
     NAMA PRODUK
===================================================== --}}
@push('styles')
<style>
.product-form-panel{width:100%;max-width:1380px;margin:0 auto;padding:26px 30px}
.product-form-panel form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 24px;align-items:start}
.product-form-panel .pf-group{min-width:0;margin:0}
.product-form-panel .pf-group>label{display:block;margin:0 0 6px;color:#554537;font-size:13px;font-weight:700}
.product-form-panel .pf-group input[type=text],.product-form-panel .pf-group select,.product-form-panel .pf-group textarea{width:100%;min-height:44px;padding:10px 12px;border:1px solid #e9dfd4;border-radius:10px;background:#fcfbf9;font-size:14px}
.product-form-panel .pf-group textarea{min-height:180px;resize:vertical}
.product-form-panel .pf-field-name{grid-column:1;grid-row:1}
.product-form-panel .pf-field-category{grid-column:1;grid-row:4}
.product-form-panel .pf-field-pricing{grid-column:1/-1;grid-row:2}
.product-form-panel .pf-field-icon{grid-column:1;grid-row:3}
.product-form-panel .pf-field-description{grid-column:1;grid-row:5;align-self:start;margin:0}
.product-form-panel .pf-field-image{grid-column:2;grid-row:3/span 3;align-self:start}
.product-form-panel .pf-field-status{grid-column:1/-1;grid-row:6}
.product-form-panel .pf-field-actions{grid-column:1/-1;grid-row:7}
.product-form-panel .pf-icon-preview{width:42px;height:42px;margin-top:7px;border-radius:10px;font-size:17px}
.product-form-panel .pf-image-preview{position:relative;display:grid;place-items:center;width:min(100%,380px);aspect-ratio:1/1;height:auto;overflow:hidden;margin:3px 0 9px;border:1px dashed #dfcfbb;border-radius:16px;background:linear-gradient(145deg,#fbf7f1,#f2e7d8);color:#96734b}
.product-form-panel .pf-image-preview img{width:100%;height:100%;object-fit:cover}
.product-form-panel .pf-image-preview span{display:grid;justify-items:center;gap:8px;color:#9b7a52}
.product-form-panel .pf-image-preview span[hidden],.product-form-panel .pf-image-preview img[hidden]{display:none}
.product-form-panel .pf-image-preview i{font-size:25px}
.product-form-panel .pf-image-preview small,.product-form-panel .pf-group>small{font-size:11px;line-height:1.55;color:#8b7a6b}
.product-form-panel .pf-image-picker{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin:7px 0}
.product-form-panel .pf-image-picker input[type=file]{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;clip-path:inset(50%)}
.product-form-panel .pf-file-picker{display:inline-flex;align-items:center;gap:8px;min-height:38px;padding:0 14px;border:1px solid #e8d8c6;border-radius:10px;background:#fff;color:#75440b;font-size:12px;font-weight:750;cursor:pointer}
.product-form-panel .pf-file-picker:hover{background:#fbf5ee;border-color:#c99a64}
.product-form-panel .pf-image-file-name{color:#8b7a6b;font-size:11px}
.product-form-panel .pf-image-picker input:focus-visible+.pf-file-picker{outline:3px solid #c8904d;outline-offset:2px}
.product-form-panel .pf-field-status{padding-top:2px}
.product-form-panel .pf-field-actions{display:flex;gap:9px;padding-top:13px;border-top:1px solid #f0e7dd}
.product-form-panel .product-form-btn{min-height:42px;padding:0 16px;font-size:13px}
@media(max-width:850px){.product-form-panel{max-width:640px;padding:18px}.product-form-panel form{grid-template-columns:minmax(0,1fr);gap:12px}.product-form-panel .pf-field-name,.product-form-panel .pf-field-category,.product-form-panel .pf-field-pricing,.product-form-panel .pf-field-icon,.product-form-panel .pf-field-description,.product-form-panel .pf-field-image,.product-form-panel .pf-field-status,.product-form-panel .pf-field-actions{grid-column:1;grid-row:auto}.product-form-panel .pf-field-image{grid-row:auto}.product-form-panel .pf-field-name{order:1}.product-form-panel .pf-field-category{order:4}.product-form-panel .pf-field-pricing{order:3}.product-form-panel .pf-field-description{order:5;align-self:stretch;margin:0}.product-form-panel .pf-field-image{order:6}.product-form-panel .pf-field-icon{order:3}.product-form-panel .pf-field-status{order:7}.product-form-panel .pf-field-actions{order:8}.product-form-panel .pf-image-preview{width:min(100%,280px)}}
</style>
@endpush

<div class="pf-group pf-field-name">

    <label for="name">
        Nama Produk
    </label>

    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $product->name ?? '') }}"
        placeholder="Contoh: Caramel Macchiato"
        required
    >

    @error('name')
        <div class="pf-error">
            {{ $message }}
        </div>
    @enderror

</div>

<div class="pf-group pf-field-category">
    <label for="category">Kategori</label>
    <select id="category" name="category" required>
        @foreach(config('product_categories') as $cat)
            <option
                value="{{ $cat['value'] }}"
                {{ in_array(old('category', $selectedCategory ?? ($product->category ?? '')), [$cat['value'], $cat['value'] === 'Food' ? 'Makanan Berat' : null], true) ? 'selected' : '' }}
            >{{ $cat['label'] }}</option>
        @endforeach
    </select>
    @error('category')<div class="pf-error">{{ $message }}</div>@enderror
</div>

{{-- Harga jual dan biaya produksi dibuat sejajar. --}}

<div class="pf-row pf-field-pricing">

    <div class="pf-group">
        <label for="price">
            Harga Jual (Rp)
        </label>

        <input
            type="text"
            id="price"
            name="price"
            inputmode="numeric"
            value="{{ old('price', $product->price ?? '') }}"
            placeholder="28000"
            required
        >

        @error('price')<div class="pf-error">{{ $message }}</div>@enderror
    </div>

    <div class="pf-group">
        <label for="cost_price">Harga Pokok Produksi (Rp)</label>
        <input
            type="text"
            id="cost_price"
            name="cost_price"
            inputmode="numeric"
            value="{{ old('cost_price', $product->cost_price ?? 0) }}"
            required
        >
        <small>Biaya bahan per produk; dipakai menghitung laba bersih.</small>
        @error('cost_price')<div class="pf-error">{{ $message }}</div>@enderror
    </div>

</div>



{{-- =====================================================
     ICON
===================================================== --}}

<div class="pf-group pf-field-icon">

    <label for="icon">
        Icon
    </label>

    <select
        id="icon"
        name="icon"
        onchange="
            document.getElementById('iconPreview').className =
            'fa-solid ' + this.value;
        "
    >

        @foreach($iconOptions as $value => $label)

            <option
                value="{{ $value }}"
                {{ old('icon', $product->icon ?? 'fa-mug-hot') == $value ? 'selected' : '' }}
            >
                {{ $label }}
            </option>

        @endforeach

    </select>


    <div class="pf-icon-preview">

        <i
            id="iconPreview"
            class="fa-solid {{ old('icon', $product->icon ?? 'fa-mug-hot') }}"
        ></i>

    </div>


    @error('icon')
        <div class="pf-error">
            {{ $message }}
        </div>
    @enderror

</div>



{{-- =====================================================
     GAMBAR PRODUK
===================================================== --}}

<div class="pf-group pf-field-image">

    <label for="image">
        Gambar Produk
    </label>

    @php
        $currentImage = trim((string) ($product->image ?? ''));
        $currentImageUrl = $currentImage
            ? (\Illuminate\Support\Str::startsWith($currentImage, ['http://', 'https://']) ? $currentImage : route('products.image', $product, false) . '?v=' . substr(sha1($currentImage), 0, 12))
            : '';
    @endphp
    <div class="pf-image-preview" id="productImagePreviewWrap">
        <img id="productImagePreview" src="{{ $currentImageUrl }}" alt="Pratinjau gambar produk" decoding="async" onerror="this.hidden=true;document.getElementById('productImagePlaceholder').hidden=false;document.querySelector('#productImagePlaceholder small').textContent='Gambar gagal dimuat · pilih ulang foto'" @if(!$currentImage) hidden @endif>
        <span id="productImagePlaceholder" @if($currentImage) hidden @endif><i class="fa-solid fa-image"></i><small>Pratinjau gambar produk</small></span>
    </div>

    <div class="pf-image-picker">
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" onchange="previewProductImage(event)" aria-describedby="productImageFileName">
        <label class="pf-file-picker" for="image"><i class="fa-solid fa-camera"></i><span>Pilih Foto</span></label>
        <span class="pf-image-file-name" id="productImageFileName">{{ $currentImage ? 'Foto saat ini tersimpan · pilih file untuk mengganti' : 'Belum ada foto dipilih' }}</span>
    </div>
    <small>Disarankan 800 × 800 px (rasio 1:1). Foto ini juga tampil di Home Customer dan bisa diganti kapan saja. Bingkai sudut 18 px · JPG, PNG, WEBP · Maksimal 5 MB.</small>


    @error('image')

        <div class="pf-error">
            {{ $message }}
        </div>

    @enderror

</div>

@push('scripts')
<script>
function previewProductImage(event) {
    const file = event.target.files?.[0];
    const preview = document.getElementById('productImagePreview');
    const placeholder = document.getElementById('productImagePlaceholder');
    const fileName = document.getElementById('productImageFileName');
    if (!file || !preview || !placeholder) return;
    preview.src = URL.createObjectURL(file);
    preview.hidden = false;
    placeholder.hidden = true;
    if (fileName) fileName.textContent = file.name;
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.product-form-panel form');
    const moneyInputs = ['cost_price', 'price'].map(id => document.getElementById(id)).filter(Boolean);
    const formatRupiah = value => {
        const clean = String(value).replace(/(?:[.,]00)$/, '').replace(/\D/g, '');
        return clean ? new Intl.NumberFormat('id-ID').format(Number(clean)) : '';
    };

    moneyInputs.forEach(input => {
        input.value = formatRupiah(input.value);
        input.addEventListener('input', function () {
            this.value = formatRupiah(this.value);
        });
    });

    form?.addEventListener('submit', function () {
        moneyInputs.forEach(input => input.value = input.value.replace(/\D/g, ''));
    });
});
</script>
@endpush



{{-- =====================================================
     DESKRIPSI
===================================================== --}}

<div class="pf-group pf-field-description">

    <label for="description">
        Deskripsi
    </label>

    <textarea
        id="description"
        name="description"
        rows="6"
        placeholder="Deskripsi produk..."
    >{{ old('description', $product->description ?? '') }}</textarea>

    @error('description')

        <div class="pf-error">
            {{ $message }}
        </div>

    @enderror

</div>



{{-- =====================================================
     STATUS
===================================================== --}}

<div class="pf-group pf-checkbox pf-field-status">

    <input
        type="checkbox"
        id="is_available"
        name="is_available"
        value="1"
        {{ old('is_available', $product->is_available ?? true) ? 'checked' : '' }}
    >

    <label for="is_available">
        Produk tersedia / aktif
    </label>

</div>




