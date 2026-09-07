<style>
.pf-group{margin-bottom:18px;}
.pf-group label{display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#333;}
.pf-group input[type=text],.pf-group input[type=file],.pf-group textarea,.pf-group select{width:100%;padding:10px 12px;border:1px solid #e2e2e2;border-radius:10px;font-size:14px;background:#fafafa;}
.pf-group input:focus,.pf-group select:focus,.pf-group textarea:focus{outline:none;border-color:#b08a5a;background:#fff;}
.pf-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.pf-icon-preview{width:44px;height:44px;border-radius:10px;background:#f1e9df;display:flex;align-items:center;justify-content:center;margin-top:8px;font-size:18px;color:#8a6a3a;}
.pf-error{color:#d9534f;font-size:12px;margin-top:4px;}
.pf-checkbox{display:flex;align-items:center;gap:8px;}
</style>

<div class="pf-group">
    <label for="name">Nama Produk</label>
    <input type="text" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" placeholder="Contoh: Caramel Macchiato" required>
    @error('name') <div class="pf-error">{{ $message }}</div> @enderror
</div>

<div class="pf-row">
    <div class="pf-group">
        <label for="category">Kategori</label>
        <select id="category" name="category" required>
            @foreach(['Coffee','Non Coffee','Food'] as $cat)
                <option value="{{ $cat }}" {{ old('category', $product->category ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        @error('category') <div class="pf-error">{{ $message }}</div> @enderror
    </div>

    <div class="pf-group">
        <label for="price">Harga (Rp)</label>
        <input type="text" inputmode="numeric" id="price" name="price" value="{{ old('price', isset($product) ? number_format($product->price, 0, ',', '.') : '') }}" placeholder="28.000" required>
        @error('price') <div class="pf-error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="pf-group">
    <label for="icon">Icon</label>
    <select id="icon" name="icon" onchange="document.getElementById('iconPreview').className='fa-solid ' + this.value;" required>
        @foreach(['fa-mug-hot'=>'Cangkir Panas','fa-mug-saucer'=>'Cangkir + Piring','fa-glass-water'=>'Gelas Dingin','fa-bread-slice'=>'Roti','fa-cake-candles'=>'Kue','fa-cookie'=>'Cookie','fa-ice-cream'=>'Es Krim'] as $value => $label)
            <option value="{{ $value }}" {{ old('icon', $product->icon ?? 'fa-mug-hot') == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <div class="pf-icon-preview"><i id="iconPreview" class="fa-solid {{ old('icon', $product->icon ?? 'fa-mug-hot') }}"></i></div>
    @error('icon') <div class="pf-error">{{ $message }}</div> @enderror
</div>

<div class="pf-group">
    <label for="image">Gambar Produk</label>
    <input type="file" id="image" name="image" accept="image/*">
    @if(isset($product) && $product->image)
        <p style="font-size:12px;color:#888;margin-top:6px;">Gambar saat ini: {{ $product->image }}</p>
    @endif
    @error('image') <div class="pf-error">{{ $message }}</div> @enderror
</div>

<div class="pf-group">
    <label for="description">Deskripsi</label>
    <textarea id="description" name="description" rows="3" placeholder="Deskripsi produk...">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <div class="pf-error">{{ $message }}</div> @enderror
</div>

<div class="pf-group pf-checkbox">
    <input type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', $product->is_available ?? true) ? 'checked' : '' }}>
    <label for="is_available" style="margin:0;">Produk tersedia (ditampilkan di menu)</label>
</div>

<script>
(function () {
    const priceInput = document.getElementById('price');
    if (!priceInput) return;

    function formatRupiah(value) {
        const digitsOnly = value.replace(/\D/g, '');
        if (!digitsOnly) return '';
        return new Intl.NumberFormat('id-ID').format(parseInt(digitsOnly, 10));
    }

    priceInput.value = formatRupiah(priceInput.value);

    priceInput.addEventListener('input', function (e) {
        e.target.value = formatRupiah(e.target.value);
    });

    const form = priceInput.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            priceInput.value = priceInput.value.replace(/\D/g, '');
        });
    }
})();
</script>