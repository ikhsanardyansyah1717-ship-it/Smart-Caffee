const appState = {
    currentUser: window.quattroUser?.name ?? 'Customer',
    currentEmail: window.quattroUser?.email ?? '',
    menu: [
        { id: 1, name: 'Caramel Macchiato', category: 'coffee', price: 28000, rating: 4.9, desc: 'Espresso, milk, caramel drizzle', img: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=200&auto=format&fit=crop' },
        { id: 2, name: 'Butter Croissant', category: 'pastry', price: 22000, rating: 4.8, desc: 'Freshly baked crispy croissant', img: 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=200&auto=format&fit=crop' },
        { id: 3, name: 'Hazelnut Latte', category: 'coffee', price: 30000, rating: 4.9, desc: 'Espresso with smooth hazelnut syrup', img: 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=200&auto=format&fit=crop' },
        { id: 4, name: 'Matcha Cream Latte', category: 'non-coffee', price: 32000, rating: 4.7, desc: 'Premium Uji matcha with fresh milk', img: 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=200&auto=format&fit=crop' }
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

function checkout() {
    if (!appState.cart.length) return;
    const subtotal = appState.cart.reduce((sum, item) => sum + item.price * item.qty, 0);
    const total = subtotal * 1.1;
    appState.history.unshift({
        id: `ORD-${Math.floor(1000 + Math.random() * 9000)}`,
        date: new Date().toLocaleString('id-ID'),
        items: appState.cart.map(c => `${c.qty}x ${c.name}`).join(', '),
        total
    });
    appState.cart = [];
    saveCustomerState();
    updateCartUI();
    switchOrderTab('history');
    showToast('Pesanan berhasil dibuat');
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
