{{-- =====================================================
     NAMA PRODUK
===================================================== --}}
@push('styles')
<style>
.product-form-panel .pf-image-preview{position:relative;display:grid;place-items:center;width:100%;height:205px;overflow:hidden;margin:2px 0 4px;border:1px dashed #dfcfbb;border-radius:14px;background:linear-gradient(145deg,#fbf7f1,#f2e7d8);color:#96734b}.product-form-panel .pf-image-preview img{width:100%;height:100%;object-fit:cover}.product-form-panel .pf-image-preview span{display:grid;justify-items:center;gap:8px;color:#9b7a52}.product-form-panel .pf-image-preview span[hidden],.product-form-panel .pf-image-preview img[hidden]{display:none}.product-form-panel .pf-image-preview i{font-size:27px}.product-form-panel .pf-image-preview small{font-size:11px}
.product-form-panel .pf-group input[type=file]{padding:11px;background:#fff!important}
</style>
@endpush

<div class="pf-group">

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
    @error('cost_price')
        <div class="pf-error">{{ $message }}</div>
    @enderror
</div>



{{-- =====================================================
     KATEGORI + HARGA
===================================================== --}}

<div class="pf-row">

    <div class="pf-group">

        <label for="category">
            Kategori
        </label>

        <select
            id="category"
            name="category"
            required
        >

            @foreach(config('product_categories') as $cat)

                <option
                    value="{{ $cat['value'] }}"
                    {{ in_array(old('category', $selectedCategory ?? ($product->category ?? '')), [$cat['value'], $cat['value'] === 'Food' ? 'Makanan Berat' : null], true) ? 'selected' : '' }}
                >
                    {{ $cat['label'] }}
                </option>

            @endforeach

        </select>

        @error('category')
            <div class="pf-error">
                {{ $message }}
            </div>
        @enderror

    </div>


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

        @error('price')
            <div class="pf-error">
                {{ $message }}
            </div>
        @enderror

    </div>

</div>



{{-- =====================================================
     ICON
===================================================== --}}

<div class="pf-group">

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

<div class="pf-group">

    <label for="image">
        Gambar Produk
    </label>

    @php
        $currentImage = trim((string) ($product->image ?? ''));
        $currentImageUrl = $currentImage
            ? (\Illuminate\Support\Str::startsWith($currentImage, ['http://', 'https://']) ? $currentImage : route('products.image', $product))
            : '';
    @endphp
    <div class="pf-image-preview" id="productImagePreviewWrap">
        <img id="productImagePreview" src="{{ $currentImageUrl }}" alt="Pratinjau gambar produk" @if(!$currentImage) hidden @endif>
        <span id="productImagePlaceholder" @if($currentImage) hidden @endif><i class="fa-solid fa-image"></i><small>Pratinjau gambar produk</small></span>
    </div>

    <input
        type="file"
        id="image"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
        onchange="previewProductImage(event)"
    >


    @if(isset($product) && $product->image)
        <small>Gambar saat ini tampil di pratinjau. Pilih file baru untuk menggantinya.</small>
    @endif


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
    if (!file || !preview || !placeholder) return;
    preview.src = URL.createObjectURL(file);
    preview.hidden = false;
    placeholder.hidden = true;
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

<div class="pf-group">

    <label for="description">
        Deskripsi
    </label>

    <textarea
        id="description"
        name="description"
        rows="4"
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

<div class="pf-group pf-checkbox">

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
