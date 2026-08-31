const appState = {
    currentUser: window.quattroUser?.name ?? 'Customer',
    currentEmail: window.quattroUser?.email ?? '',
    menu: [
    {
        id: 4,
        name: 'Caramel Macchiato',
        category: 'coffee',
        price: 35000,
        rating: 4.9,
        desc: 'Espresso, milk, caramel drizzle',
        img: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=200&auto=format&fit=crop'
    },

    {
        id: 7,
        name: 'Butter Croissant',
        category: 'pastry',
        price: 24000,
        rating: 4.8,
        desc: 'Freshly baked crispy croissant',
        img: 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=200&auto=format&fit=crop'
    },

    {
        id: 2,
        name: 'Cafe Latte',
        category: 'coffee',
        price: 30000,
        rating: 4.9,
        desc: 'Smooth espresso with fresh milk',
        img: 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=200&auto=format&fit=crop'
    },

    {
        id: 5,
        name: 'Matcha Latte',
        category: 'non-coffee',
        price: 32000,
        rating: 4.7,
        desc: 'Premium matcha with fresh milk',
        img: 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=200&auto=format&fit=crop'
    },

    {
        id: 1,
        name: 'Cappuccino',
        category: 'coffee',
        price: 28000,
        rating: 4.8,
        desc: 'Espresso with steamed milk',
        img: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=200&auto=format&fit=crop'
    },

    {
        id: 3,
        name: 'Americano',
        category: 'coffee',
        price: 22000,
        rating: 4.7,
        desc: 'Rich espresso with hot water',
        img: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=200&auto=format&fit=crop'
    },

    {
        id: 6,
        name: 'Chocolate',
        category: 'non-coffee',
        price: 30000,
        rating: 4.8,
        desc: 'Rich chocolate with fresh milk',
        img: 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=200&auto=format&fit=crop'
    },

    {
        id: 8,
        name: 'French Fries',
        category: 'pastry',
        price: 26000,
        rating: 4.7,
        desc: 'Crispy golden french fries',
        img: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=200&auto=format&fit=crop'
    }
],
    cart: [],
    favorites: [1],
    history: []
};

const storageKey = `quattro_customer_${window.quattroUser?.id ?? 'guest'}`;

function loadCustomerState() {
    try {
        const saved = JSON.parse(localStorage.getItem(storageKey));
        if (saved) {
            appState.cart = saved.cart ?? [];
            appState.favorites = saved.favorites ?? [1];
            appState.history = saved.history ?? [];
        }
    } catch (_) {}
}

function saveCustomerState() {
    localStorage.setItem(storageKey, JSON.stringify({
        cart: appState.cart,
        favorites: appState.favorites,
        history: appState.history
    }));
}

function rupiah(value) {
    return `Rp ${Number(value).toLocaleString('id-ID')}`;
}

function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toast-message');
    if (!toast || !toastMsg) return;
    toastMsg.innerText = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2300);
}

function renderMenu(items) {
    const container = document.getElementById('main-menu-list');
    if (!container) return;
    if (!items.length) {
        container.innerHTML = '<div class="empty-state"><i class="fa-solid fa-mug-hot"></i><p>Menu tidak ditemukan</p></div>';
        return;
    }
    container.innerHTML = items.map(item => {
        const isFav = appState.favorites.includes(item.id);
        return `<div class="menu-card">
            <img src="${item.img}" alt="${item.name}">
            <div class="menu-info"><h5>${item.name}</h5><p>${item.desc}</p>
                <div class="menu-footer"><span class="price">${rupiah(item.price)}</span><span class="rating"><i class="fa-solid fa-star"></i> ${item.rating}</span></div>
            </div>
            <div class="card-actions">
                <button class="btn-fav ${isFav ? 'active' : ''}" onclick="toggleFavorite(${item.id})"><i class="fa-${isFav ? 'solid' : 'regular'} fa-heart"></i></button>
                <button class="add-btn" onclick="addToCart(${item.id})"><i class="fa-solid fa-plus"></i></button>
            </div>
        </div>`;
    }).join('');
}

function filterMenu() {
    const input = document.getElementById('search-input');
    if (!input) return;
    const query = input.value.toLowerCase();
    renderMenu(appState.menu.filter(m => m.name.toLowerCase().includes(query) || m.desc.toLowerCase().includes(query)));
}

function selectCategory(element, category) {
    document.querySelectorAll('.category-item').forEach(c => c.classList.remove('active'));
    element.classList.add('active');
    renderMenu(category === 'all' ? appState.menu : appState.menu.filter(m => m.category === category));
}

function addToCart(menuId) {
    const item = appState.menu.find(m => m.id === menuId);
    if (!item) return;
    const existing = appState.cart.find(c => c.id === menuId);
    if (existing) existing.qty += 1;
    else appState.cart.push({ ...item, qty: 1 });
    saveCustomerState();
    updateCartUI();
    showToast(`${item.name} ditambahkan ke keranjang`);
}

function changeQty(menuId, delta) {
    const item = appState.cart.find(c => c.id === menuId);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) appState.cart = appState.cart.filter(c => c.id !== menuId);
    saveCustomerState();
    updateCartUI();
}

function updateCartUI() {
    const container = document.getElementById('cart-items-container');
    const summaryBox = document.getElementById('cart-summary-box');
    const count = document.getElementById('cart-count');
    if (!container || !summaryBox || !count) return;

    count.innerText = appState.cart.reduce((sum, item) => sum + item.qty, 0);
    if (!appState.cart.length) {
        container.innerHTML = '<div class="empty-state"><i class="fa-solid fa-cart-shopping"></i><p>Keranjang kamu masih kosong.<br>Yuk pilih kopi kesukaanmu!</p></div>';
        summaryBox.style.display = 'none';
        return;
    }

    summaryBox.style.display = 'block';
    container.innerHTML = appState.cart.map(item => `<div class="cart-item">
        <img src="${item.img}" alt="${item.name}">
        <div class="cart-details"><h5>${item.name}</h5><p>${rupiah(item.price * item.qty)}</p></div>
        <div class="qty-control"><button class="qty-btn" onclick="changeQty(${item.id},-1)">-</button><span class="qty-val">${item.qty}</span><button class="qty-btn" onclick="changeQty(${item.id},1)">+</button></div>
    </div>`).join('');

    const subtotal = appState.cart.reduce((sum, item) => sum + item.price * item.qty, 0);
    const tax = subtotal * 0.1;
    document.getElementById('subtotal-val').innerText = rupiah(subtotal);
    document.getElementById('tax-val').innerText = rupiah(tax);
    document.getElementById('total-val').innerText = rupiah(subtotal + tax);
}

function switchOrderTab(tab) {
    const tabs = document.querySelectorAll('.tab-btn');
    const cart = document.getElementById('tab-cart');
    const history = document.getElementById('tab-history');
    if (!cart || !history) return;
    tabs.forEach(t => t.classList.remove('active'));
    if (tab === 'cart') {
        tabs[0]?.classList.add('active'); cart.style.display = 'block'; history.style.display = 'none';
    } else {
        tabs[1]?.classList.add('active'); cart.style.display = 'none'; history.style.display = 'block'; renderHistory();
    }
}

async function checkout() {

    // Cek keranjang
    if (!appState.cart.length) {
        showToast('Keranjang masih kosong');
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Ambil CSRF Token Laravel
    |--------------------------------------------------------------------------
    */

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (!csrfToken) {
        showToast('CSRF token tidak ditemukan');
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Ubah isi cart menjadi format yang dibutuhkan Laravel
    |--------------------------------------------------------------------------
    */

    const items = appState.cart.map(item => ({
        product_id: item.id,
        quantity: item.qty
    }));

    /*
    |--------------------------------------------------------------------------
    | Ambil nomor meja jika ada
    |--------------------------------------------------------------------------
    */

    const tableInput = document.getElementById('table-number');

    const tableNumber = tableInput
        ? tableInput.value.trim()
        : null;

    /*
    |--------------------------------------------------------------------------
    | Kirim Order ke Laravel
    |--------------------------------------------------------------------------
    */

    try {

        showToast('Menyimpan pesanan...');

        const response = await fetch(
            '/customer/orders',
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },

                body: JSON.stringify({
                    table_number: tableNumber,
                    items: items
                })
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Jika validasi Laravel gagal
        |--------------------------------------------------------------------------
        */

        if (!response.ok) {

            let errorMessage =
                'Pesanan gagal dibuat';

            try {

                const errorData =
                    await response.json();

                if (errorData.message) {
                    errorMessage =
                        errorData.message;
                }

            } catch (error) {
                // Abaikan jika response bukan JSON
            }

            throw new Error(errorMessage);
        }

        /*
        |--------------------------------------------------------------------------
        | Order berhasil masuk database
        |--------------------------------------------------------------------------
        */

        appState.cart = [];

        saveCustomerState();

        updateCartUI();

        showToast(
            'Pesanan berhasil dibuat!'
        );

        /*
        |--------------------------------------------------------------------------
        | Pindah ke halaman Orders
        |--------------------------------------------------------------------------
        */

        setTimeout(() => {

            window.location.href =
                '/customer/orders';

        }, 800);

    } catch (error) {

        console.error(
            'Checkout Error:',
            error
        );

        showToast(
            error.message ||
            'Terjadi kesalahan saat membuat pesanan'
        );
    }
}

function renderHistory() {
    const container = document.getElementById('history-container');
    if (!container) return;
    if (!appState.history.length) {
        container.innerHTML = '<div class="empty-state"><i class="fa-solid fa-receipt"></i><p>Belum ada riwayat pesanan</p></div>';
        return;
    }
    container.innerHTML = appState.history.map(h => `<div class="history-card">
        <div class="history-header"><span>${h.id} • ${h.date}</span><span class="history-status">Selesai</span></div>
        <div class="history-title">${h.items}</div><div class="history-price">${rupiah(h.total)}</div>
    </div>`).join('');
}

function toggleFavorite(id) {
    const index = appState.favorites.indexOf(id);
    if (index >= 0) appState.favorites.splice(index, 1);
    else appState.favorites.push(id);
    saveCustomerState();
    renderMenu(appState.menu);
    renderFavorites();
}

function renderFavorites() {
    const container = document.getElementById('fav-container');
    if (!container) return;
    const items = appState.menu.filter(m => appState.favorites.includes(m.id));
    if (!items.length) {
        container.innerHTML = '<div class="empty-state" style="grid-column:span 2"><i class="fa-solid fa-heart-crack"></i><p>Belum ada menu favorit.</p></div>';
        return;
    }
    container.innerHTML = items.map(item => `<div class="fav-card">
        <button class="fav-remove" onclick="toggleFavorite(${item.id})"><i class="fa-solid fa-xmark"></i></button>
        <img src="${item.img}" alt="${item.name}"><h5>${item.name}</h5><p>${rupiah(item.price)}</p>
        <div class="fav-footer"><span class="rating"><i class="fa-solid fa-star"></i> ${item.rating}</span><button class="add-btn" onclick="addToCart(${item.id})"><i class="fa-solid fa-plus"></i></button></div>
    </div>`).join('');
}

document.addEventListener('DOMContentLoaded', () => {
    loadCustomerState();
    renderMenu(appState.menu);
    renderFavorites();
    updateCartUI();
    renderHistory();
});

function togglePassword(inputId, eyeId) {

    const input = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);

    if (!input || !eye) {
        return;
    }

    // Password sedang tersembunyi
    if (input.type === "password") {

        // Tampilkan password
        input.type = "text";

        // Mata terbuka
        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");

    } else {

        // Sembunyikan password
        input.type = "password";

        // Mata tertutup / silang
        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");

    }
}
