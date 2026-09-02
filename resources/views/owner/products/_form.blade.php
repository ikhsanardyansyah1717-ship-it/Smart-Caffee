{{-- =====================================================
     NAMA PRODUK
===================================================== --}}

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

            @foreach(['Coffee', 'Non Coffee', 'Food'] as $cat)

                <option
                    value="{{ $cat }}"
                    {{ old('category', $product->category ?? '') == $cat ? 'selected' : '' }}
                >
                    {{ $cat }}
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
            Harga (Rp)
        </label>

        <input
            type="number"
            id="price"
            name="price"
            min="0"
            step="500"
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

    <input
        type="file"
        id="image"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
    >


    {{-- Gambar yang sudah tersimpan ketika edit --}}
    @if(isset($product) && $product->image)

        <small>
            Gambar saat ini:
            {{ $product->image }}
        </small>

    @endif


    @error('image')

        <div class="pf-error">
            {{ $message }}
        </div>

    @enderror

</div>



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