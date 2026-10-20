/* =========================================================
   QUATTRO COFFEE
   CUSTOMER.JS
   ========================================================= */


/* =========================================================
   PRODUCT IMAGE
   ========================================================= */

const productImages = {

    'Cappuccino':
        'https://images.unsplash.com/photo-1534778101976-62847782c213?w=500&auto=format&fit=crop',

    'Cafe Latte':
        'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop',

    'Americano':
        'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop',

    'Caramel Macchiato':
        'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=500&auto=format&fit=crop',

    'Matcha Latte':
        'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop',

    'Chocolate':
        'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=500&auto=format&fit=crop',

    'Croissant':
        'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500&auto=format&fit=crop',

    'French Fries':
        'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=500&auto=format&fit=crop'
};


/* =========================================================
   PRODUCT DESCRIPTION
   ========================================================= */

const productDescriptions = {

    'Cappuccino':
        'Espresso dengan steamed milk dan foam yang lembut',

    'Cafe Latte':
        'Espresso creamy dengan susu yang lembut',

    'Americano':
        'Espresso dengan air panas dengan rasa kopi yang kuat',

    'Caramel Macchiato':
        'Espresso, susu creamy dan sentuhan caramel',

    'Matcha Latte':
        'Matcha premium dengan susu segar yang creamy',

    'Chocolate':
        'Minuman cokelat lembut dengan rasa manis yang nikmat',

    'Croissant':
        'Croissant renyah dan fresh dipanggang setiap hari',

    'French Fries':
        'Kentang goreng renyah dengan rasa gurih'
};


/* =========================================================
   PRODUCT RATING
   ========================================================= */

const productRatings = {

    'Cappuccino': 4.9,

    'Cafe Latte': 4.8,

    'Americano': 4.7,

    'Caramel Macchiato': 4.9,

    'Matcha Latte': 4.8,

    'Chocolate': 4.7,

    'Croissant': 4.8,

    'French Fries': 4.6
};


/* =========================================================
   NORMALIZE CATEGORY
   ========================================================= */

function normalizeCategory(category) {

    if (!category) {
        return 'other';
    }

    return String(category)
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-');

}


/* =========================================================
   DATABASE PRODUCTS
   ========================================================= */

const databaseProducts =

    Array.isArray(window.quattroProducts)

        ? window.quattroProducts

        : [];


/* =========================================================
   CONVERT DATABASE PRODUCT
   ========================================================= */

const menuFromDatabase =
    databaseProducts.map(product => {

        const name = product.name;

        return {

            id: Number(product.id),

            name: name,

            category:
                normalizeCategory(
                    product.category
                ),

            price:
                Number(product.price),

            // STATUS KETERSEDIAAN DARI DATABASE
            is_available:
                product.is_available === true ||
                product.is_available === 1 ||
                product.is_available === '1',

            rating:
                product.reviews_avg_rating === null || product.reviews_avg_rating === undefined
                    ? null
                    : Number(product.reviews_avg_rating),

            reviewCount:
                Number(product.reviews_count || 0),

            desc:
                productDescriptions[name] ??
                'Menu pilihan Quattro Coffee',

            img:
                productImages[name] ??
                'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500&auto=format&fit=crop'

        };

    });


/* =========================================================
   APP STATE
   ========================================================= */

const appState = {

    currentUser:
        window.quattroUser?.name ??
        'Customer',

    currentEmail:
        window.quattroUser?.email ??
        '',

    menu:
        menuFromDatabase,

    cart: [],

    favorites: Array.isArray(window.quattroFavoriteIds)
    ? window.quattroFavoriteIds.map(Number)
    : [],

    history: []

};

let activeMenuCategory = 'all';
let activeDetailProduct = null;
let activeDetailQuantity = 1;
let activeProductOptions = {};
const historyReviewRatings = {};


/* =========================================================
   STORAGE KEY
   ========================================================= */

const storageKey =

    `quattro_customer_${
        window.quattroUser?.id ?? 'guest'
    }`;

const guestTokenStorageKey = 'quattro_guest_token_v1';

function getGuestToken() {
    if (window.quattroUser?.id) {
        return '';
    }

    let token = localStorage.getItem(guestTokenStorageKey);
    if (!/^[A-Za-z0-9]{64}$/.test(token || '')) {
        const bytes = new Uint8Array(32);
        crypto.getRandomValues(bytes);
        token = Array.from(bytes, byte => byte.toString(16).padStart(2, '0')).join('');
        localStorage.setItem(guestTokenStorageKey, token);
    }

    return token;
}


/* =========================================================
   LOAD CUSTOMER STATE
   ========================================================= */

function loadCustomerState() {

    try {

        const saved =

            JSON.parse(
                localStorage.getItem(
                    storageKey
                )
            );

        if (!saved) {
            return;
        }


        appState.cart = (Array.isArray(saved.cart) ? saved.cart : []).map(cartItem => {
            const customization = {
                temperature: cartItem.customization?.temperature || '',
                note: String(cartItem.customization?.note || '').trim().slice(0, 300)
            };
            const product = appState.menu.find(item => Number(item.id) === Number(cartItem.id));
            return {
                ...cartItem,
                customization,
                variantKey: String(cartItem.id) + ':' + JSON.stringify(customization),
                price: Number(product?.price ?? cartItem.price)
            };
        });



        appState.history =

            Array.isArray(saved.history)

                ? saved.history

                : [];

    }

    catch (error) {

        console.error(
            'Gagal membaca customer state:',
            error
        );

    }

}


/* =========================================================
   SAVE CUSTOMER STATE
   ========================================================= */

function saveCustomerState() {

    localStorage.setItem(

        storageKey,

        JSON.stringify({

            cart:
                appState.cart,


            history:
                appState.history

        })

    );

}


/* =========================================================
   RUPIAH
   ========================================================= */

function rupiah(value) {

    return `Rp ${
        Number(value || 0)
            .toLocaleString('id-ID')
    }`;

}


/* =========================================================
   TOAST
   ========================================================= */

function showToast(message) {

    const toast =
        document.getElementById('toast');

    const toastMsg =
        document.getElementById(
            'toast-message'
        );

    if (!toast || !toastMsg) {
        return;
    }

    toastMsg.innerText =
        message;

    toast.classList.add('show');

    setTimeout(() => {

        toast.classList.remove('show');

    }, 2300);

}


/* =========================================================
   RENDER MENU
   ========================================================= */

function renderMenu(items) {

    const container =
        document.getElementById('main-menu-list');

    if (!container) {
        return;
    }

    const resultCount = document.getElementById('menu-result-count');
    if (resultCount) {
        resultCount.textContent = items.length
            ? `${items.length} pilihan tersedia`
            : 'Coba kata kunci atau kategori lain';
    }

    if (!items.length) {

        container.innerHTML = `

            <div class="empty-state">

                <i class="fa-solid fa-mug-hot"></i>

                <p>
                    Menu tidak ditemukan
                </p>

            </div>

        `;

        return;
    }

    container.innerHTML =

        items.map(item => {

            const isFav =
                appState.favorites.includes(item.id);

            const isAvailable =
                item.is_available === true;

            const productCartItems = appState.cart.filter(
                cartItem => Number(cartItem.id) === Number(item.id)
            );
            const productCartQty = productCartItems.reduce(
                (sum, cartItem) => sum + Number(cartItem.qty || 0),
                0
            );

            return `

                <article class="menu-card ${!isAvailable ? 'product-unavailable' : ''}" onclick="openProductDetail(${item.id})" tabindex="0" role="button" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openProductDetail(${item.id})}">

                    <div class="menu-image-wrapper">

                        <img
                            src="${item.img}"
                            alt="${item.name}"
                        >

                        ${
                            !isAvailable
                                ? `
                                    <span class="availability-badge unavailable">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                        Habis
                                    </span>
                                `
                                : ''
                        }

                    </div>

                    <div class="menu-info">

                        <h5>
                            ${item.name}
                        </h5>

                        <p>
                            ${item.desc}
                        </p>

                        <div class="menu-footer">

                            <span class="price">
                                ${rupiah(item.price)}
                            </span>

                            ${productCartQty > 0
                                ? `
                                    <div class="menu-card-qty-control" onclick="event.stopPropagation()" aria-label="Jumlah ${item.name}">
                                        <button type="button" aria-label="Kurangi ${item.name}" onclick="updateProductCartQty(${item.id}, -1)">−</button>
                                        <span>${productCartQty}</span>
                                        <button type="button" aria-label="Tambah ${item.name}" onclick="updateProductCartQty(${item.id}, 1)">+</button>
                                    </div>
                                `
                                : `
                                    <button
                                        class="menu-footer-add"
                                        ${!isAvailable ? 'disabled' : ''}
                                        onclick="event.stopPropagation();openProductDetail(${item.id})"
                                    >
                                        ${isAvailable ? 'Tambahkan' : 'Habis'}
                                    </button>
                                `}

                            <button
                                class="btn-fav menu-footer-fav ${isFav ? 'active' : ''}"
                                aria-label="${isFav ? 'Hapus dari favorit' : 'Tambahkan ke favorit'}"
                                onclick="event.stopPropagation();toggleFavorite(${item.id})"
                            >
                                <i class="fa-${isFav ? 'solid' : 'regular'} fa-heart"></i>
                            </button>

                            <span class="rating">

                                <i class="fa-solid fa-star"></i>

                                ${item.reviewCount ? `${Number(item.rating).toFixed(1)} (${item.reviewCount})` : 'Baru'}

                            </span>

                        </div>

                    </div>

                    <div class="card-actions">
                        ${
                            isAvailable

                                ? `
                                    <button
                                        class="add-btn"
                                        onclick="event.stopPropagation();openProductDetail(${item.id})"
                                    >
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                `

                                : `
                                    <button
                                        class="add-btn disabled"
                                        disabled
                                        title="Produk sedang habis"
                                    >
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                `
                        }

                    </div>

                </article>

            `;

        }).join('');

}


/* =========================================================
   SEARCH MENU
   ========================================================= */

function filterMenu() {

    const input =
        document.getElementById(
            'search-input'
        );

    if (!input) {
        return;
    }


    const query =
        input.value
            .toLowerCase()
            .trim();


    const filtered =
        appState.menu.filter(menu =>
            (activeMenuCategory === 'all' || menu.category === normalizeCategory(activeMenuCategory))
            && (

            menu.name
                .toLowerCase()
                .includes(query)

            ||

            menu.desc
                .toLowerCase()
                .includes(query)
            )
        );


    renderMenu(filtered);

}


/* =========================================================
   CATEGORY
   ========================================================= */

function selectCategory(
    element,
    category
) {

    activeMenuCategory = category;

    document
        .querySelectorAll(
            '.category-item'
        )
        .forEach(item => {

            item.classList.remove(
                'active'
            );
            item.setAttribute('aria-pressed', 'false');

        });


    if (element) {

        element.classList.add(
            'active'
        );
        element.setAttribute('aria-pressed', 'true');

    }


    filterMenu();

}


/* =========================================================
   ADD TO CART
   ========================================================= */

function addToCart(menuId) {

    addProductVariant(menuId, {}, 1);

}

function addProductVariant(menuId, customization = {}, quantity = 1) {

    const item =

        appState.menu.find(
            menu =>
                Number(menu.id) ===
                Number(menuId)
        );


    if (!item) {

        showToast(
            'Produk tidak ditemukan'
        );

        return;
    }

 if (!item.is_available) {

    showToast(
        `${item.name} sedang habis`
    );

    return;
}

    const normalizedCustomization = {
        temperature: customization.temperature || '',
        note: String(customization.note || '').trim().slice(0, 300)
    };
    const variantKey = `${item.id}:${JSON.stringify(normalizedCustomization)}`;
    const unitPrice = getConfiguredPrice(item, normalizedCustomization);

    const existing =

        appState.cart.find(
            cartItem =>
                cartItem.variantKey === variantKey
        );


    if (existing) {

        existing.qty += quantity;

    }

    else {

        appState.cart.push({

            ...item,
            customization: normalizedCustomization,
            variantKey,
            price: unitPrice,
            qty: quantity

        });

    }


    saveCustomerState();

    updateCartUI();

    showToast(
        `${item.name} ditambahkan ke keranjang`
    );

}

const optionGroups = {
    temperature: { label: 'Suhu', choices: [['ice', 'Dingin'], ['hot', 'Panas']] }
};

function productOptionTypes(product) {
    const category = normalizeCategory(product.category);
    if (category === 'food') return [];
    return ['temperature'];
}

function getConfiguredPrice(product, options = {}) {
    return Number(product.price) || 0;
}

function openProductDetail(menuId) {
    const product = appState.menu.find(item => Number(item.id) === Number(menuId));
    if (!product || !product.is_available) {
        if (product) showToast(`${product.name} sedang habis`);
        return;
    }

    activeDetailProduct = product;
    activeDetailQuantity = 1;
    activeProductOptions = { note: '' };
    productOptionTypes(product).forEach(type => {
        activeProductOptions[type] = optionGroups[type].choices[0][0];
    });

    document.getElementById('detail-product-image').src = product.img;
    document.getElementById('detail-product-image').alt = product.name;
    document.getElementById('detail-product-name').textContent = product.name;
    document.getElementById('detail-product-description').textContent = product.desc;
    renderProductOptions();
    updateProductDetailPrice();

    const modal = document.getElementById('product-detail-modal');
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('product-detail-open');
}

function closeProductDetail() {
    const modal = document.getElementById('product-detail-modal');
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('product-detail-open');
    activeDetailProduct = null;
}

function renderProductOptions() {
    const container = document.getElementById('product-options');
    if (!activeDetailProduct) return;
    const types = productOptionTypes(activeDetailProduct);
    container.innerHTML = types.map(type => {
        const group = optionGroups[type];
        return `<section class="product-option-group">
            <div class="product-option-heading"><h4>${group.label}</h4><span>${group.multiple ? 'Pilih beberapa' : 'Pilih 1'}</span></div>
            <div class="product-option-choices">${group.choices.map(([value, label]) => `
                <button type="button" class="product-option-choice ${(Array.isArray(activeProductOptions[type]) ? activeProductOptions[type].includes(value) : activeProductOptions[type] === value) ? 'selected' : ''}" onclick="selectProductOption('${type}','${value}')">${label}</button>
            `).join('')}</div>
        </section>`;
    }).join('');
    if (!types.length) {
        container.innerHTML = '<p class="detail-simple-note"><i class="fa-solid fa-circle-info"></i> Produk ini tidak memiliki opsi racikan.</p>';
    }

    const noteGroup = document.createElement('section');
    noteGroup.className = 'product-option-group detail-note-group';
    noteGroup.innerHTML = '<label class="product-option-heading" for="detail-product-note"><h4>Catatan</h4><span>Opsional</span></label><textarea id="detail-product-note" class="detail-product-note" maxlength="300" placeholder="Tambahkan catatan untuk pesanan ini (opsional)"></textarea>';
    container.appendChild(noteGroup);
    document.getElementById('detail-product-note').value = activeProductOptions.note || '';

    const reviewSection = document.createElement('section');
    reviewSection.className = 'product-review-section';
    reviewSection.innerHTML = `
        <div class="product-review-heading">
            <h4>Ulasan Pelanggan</h4>
            <span id="product-review-average">Memuat ulasan...</span>
        </div>
        <div id="product-review-list" class="product-review-list"></div>
        <p class="product-review-empty">Sudah memesan menu ini? Beri ulasan dari halaman History Pesanan.</p>
    `;
    container.appendChild(reviewSection);
    loadProductReviews(activeDetailProduct.id);
}

async function loadProductReviews(productId) {
    const list = document.getElementById('product-review-list');
    if (!list) return;

    try {
        const response = await fetch(`/customer/products/${productId}/reviews`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        if (!response.ok) throw new Error('Gagal memuat ulasan.');
        const data = await response.json();

        if (Number(activeDetailProduct?.id) !== Number(productId)) return;
        const average = document.getElementById('product-review-average');
        if (average) average.textContent = data.count ? `★ ${Number(data.average).toFixed(1)} · ${data.count} ulasan` : 'Belum ada ulasan';

        list.innerHTML = data.reviews?.length
            ? data.reviews.map(review => `
                <article class="product-review-card">
                    <div><strong>${escapeHtml(review.customer_name || 'Pelanggan Quattro')}</strong><span>★ ${Number(review.rating)} / 5</span></div>
                    <p>${escapeHtml(review.comment)}</p>
                    <small>${escapeHtml(review.created_at || '')}</small>
                </article>
            `).join('')
            : '<p class="product-review-empty">Belum ada ulasan. Jadilah yang pertama memberi rating.</p>';
    } catch (error) {
        list.innerHTML = '<p class="product-review-empty">Ulasan belum dapat dimuat.</p>';
    }
}

function selectProductOption(type, value) {
    activeProductOptions.note = document.getElementById('detail-product-note')?.value || '';
    activeProductOptions[type] = value;
    renderProductOptions();
    updateProductDetailPrice();
}

function changeDetailQty(delta) {
    activeDetailQuantity = Math.max(1, activeDetailQuantity + delta);
    updateProductDetailPrice();
}

function updateProductDetailPrice() {
    if (!activeDetailProduct) return;
    const price = getConfiguredPrice(activeDetailProduct, activeProductOptions);
    document.getElementById('detail-product-price').textContent = rupiah(price);
    document.getElementById('detail-quantity').textContent = activeDetailQuantity;
    document.getElementById('detail-line-total').textContent = rupiah(price * activeDetailQuantity);
}

function addConfiguredProduct() {
    if (!activeDetailProduct) return;
    activeProductOptions.note = document.getElementById('detail-product-note')?.value.trim().slice(0, 300) || '';
    addProductVariant(activeDetailProduct.id, activeProductOptions, activeDetailQuantity);
    closeProductDetail();
}


/* =========================================================
   CHANGE QUANTITY
   ========================================================= */

function changeQty(
    menuId,
    delta,
    variantKey = null
) {

    const item =

        appState.cart.find(
            cartItem =>
                variantKey
                    ? cartItem.variantKey === variantKey
                    : Number(cartItem.id) === Number(menuId)
        );


    if (!item) {
        return;
    }


    item.qty += delta;


    if (item.qty <= 0) {

        appState.cart =

            appState.cart.filter(
                cartItem =>
                    variantKey
                        ? cartItem.variantKey !== variantKey
                        : Number(cartItem.id) !== Number(menuId)
            );

    }


    saveCustomerState();

    updateCartUI();

}

function updateProductCartQty(menuId, delta) {
    const cartItem = appState.cart.find(
        item => Number(item.id) === Number(menuId)
    );
    if (!cartItem) return;
    changeQty(menuId, delta, cartItem.variantKey || null);
}


/* =========================================================
   UPDATE CART
   ========================================================= */

function updateCartUI() {
    updateQuickCartBar();
    window.renderCustomerOrderPage?.();

    if (document.getElementById('main-menu-list')) {
        filterMenu();
    }

    const container =
        document.getElementById(
            'cart-items-container'
        );

    const summaryBox =
        document.getElementById(
            'cart-summary-box'
        );

    const count =
        document.getElementById(
            'cart-count'
        );


    if (
        !container ||
        !summaryBox ||
        !count
    ) {

        return;

    }


    const totalQuantity =

        appState.cart.reduce(
            (sum, item) =>
                sum +
                Number(item.qty || 0),
            0
        );


    count.innerText =
        totalQuantity;


    if (!appState.cart.length) {

        container.innerHTML = `

            <div class="empty-state">

                <i class="fa-solid fa-cart-shopping"></i>

                <p>
                    Keranjang kamu masih kosong.
                    <br>
                    Yuk pilih kopi kesukaanmu!
                </p>

            </div>

        `;

        summaryBox.style.display =
            'none';

        return;

    }


    summaryBox.style.display =
        'block';


    container.innerHTML =

        appState.cart.map(item => `

            <div class="cart-item">

                <img
                    src="${item.img}"
                    alt="${item.name}"
                >

                <div class="cart-details">

                    <h5>
                        ${item.name}
                    </h5>

                    ${renderCustomizationSummary(item.customization)}

                    <p>
                        ${rupiah(
                            Number(item.price) *
                            Number(item.qty)
                        )}
                    </p>

                </div>

                <div class="qty-control">

                    <button
                        class="qty-btn"
                        onclick="changeQty(
                            ${item.id},
                            -1,
                            '${item.variantKey || ''}'
                        )"
                    >
                        -
                    </button>

                    <span class="qty-val">
                        ${item.qty}
                    </span>

                    <button
                        class="qty-btn"
                        onclick="changeQty(
                            ${item.id},
                            1,
                            '${item.variantKey || ''}'
                        )"
                    >
                        +
                    </button>

                </div>

            </div>

        `).join('');


    const subtotal =

        appState.cart.reduce(
            (sum, item) =>

                sum +

                Number(item.price || 0) *
                Number(item.qty || 0),

            0
        );


    const tax =
        subtotal * 0.10;


    const total =
        subtotal + tax;


    const subtotalElement =
        document.getElementById(
            'subtotal-val'
        );

    const taxElement =
        document.getElementById(
            'tax-val'
        );

    const totalElement =
        document.getElementById(
            'total-val'
        );


    if (subtotalElement) {

        subtotalElement.innerText =
            rupiah(subtotal);

    }


    if (taxElement) {

        taxElement.innerText =
            rupiah(tax);

    }


    if (totalElement) {

        totalElement.innerText =
            rupiah(total);

    }

}

function updateQuickCartBar() {
    const bar = document.getElementById('quick-cart-bar');
    if (!bar) return;

    const itemCount = appState.cart.reduce(
        (sum, item) => sum + Number(item.qty || 0),
        0
    );
    bar.hidden = itemCount === 0;

    if (itemCount > 0) {
        document.getElementById('quick-cart-count').textContent = itemCount;
        document.getElementById('quick-cart-total').textContent = rupiah(getCheckoutTotal());
    }
}

function renderCustomizationSummary(options = {}) {
    if (!options || typeof options !== 'object') return '';
    const labels = [];
    if (options.note) labels.push('Catatan: ' + escapeHtml(options.note));
    return labels.length ? `<small class="cart-customization">${labels.join(' · ')}</small>` : '';
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[character]);
}


/* =========================================================
   ORDER TAB
   ========================================================= */

function switchOrderTab(tab) {

    const tabs =
        document.querySelectorAll(
            '.tab-btn'
        );

    const cart =
        document.getElementById(
            'tab-cart'
        );

    const history =
        document.getElementById(
            'tab-history'
        );


    if (!cart || !history) {
        return;
    }


    tabs.forEach(tabButton => {

        tabButton.classList.remove(
            'active'
        );

    });


    if (tab === 'cart') {

        tabs[0]?.classList.add(
            'active'
        );

        cart.style.display =
            'block';

        history.style.display =
            'none';

    }

    else {

        tabs[1]?.classList.add(
            'active'
        );

        cart.style.display =
            'none';

        history.style.display =
            'block';

        renderHistory();

    }

}


/* =========================================================
   CHECKOUT TOTAL
   ========================================================= */

function getCheckoutTotal() {

    if (
        !appState.cart ||
        !appState.cart.length
    ) {

        return 0;

    }


    const subtotal =

        appState.cart.reduce(
            (sum, item) =>

                sum +

                Number(item.price || 0) *
                Number(item.qty || 0),

            0
        );


    return subtotal + (
        subtotal * 0.10
    );

}


/* =========================================================
   PAYMENT STATE
   ========================================================= */

window.selectedPaymentMethod =
    null;

window.selectedDebitBank =
    null;

window.selectedCashReceived =
    null;


/* =========================================================
   UPDATE PAYMENT DETAIL
   ========================================================= */

function updatePaymentDetail(method) {

    const container =
        document.getElementById(
            'checkout-payment-details'
        );

    const cash =
        document.getElementById(
            'checkout-cash-detail'
        );

    const qris =
        document.getElementById(
            'checkout-qris-detail'
        );

    const debit =
        document.getElementById(
            'checkout-debit-detail'
        );


    if (
        !container ||
        !cash ||
        !qris ||
        !debit
    ) {

        return;

    }


    cash.style.display =
        'none';

    qris.style.display =
        'none';

    debit.style.display =
        'none';


    container.style.display =
        'block';


    window.selectedPaymentMethod =
        method;


    /* CASH */

    if (method === 'Cash') {

        cash.style.display =
            'block';

        calculateCashChange();

    }


    /* QRIS */

    if (method === 'QRIS') {

        qris.style.display =
            'block';

        updateQrisTotal();

    }


    /* DEBIT */

    if (method === 'Debit') {

        debit.style.display =
            'block';

        window.selectedDebitBank =
            null;


        document
            .querySelectorAll(
                'input[name="debit_bank"]'
            )
            .forEach(input => {

                input.checked =
                    false;

            });

    }

}


/* =========================================================
   QRIS TOTAL
   ========================================================= */

function updateQrisTotal() {

    const element =
        document.getElementById(
            'checkout-qris-total'
        );


    if (!element) {
        return;
    }


    element.innerText =
        rupiah(
            getCheckoutTotal()
        );

}


/* =========================================================
   CASH CHANGE
   ========================================================= */

function calculateCashChange() {

    const input =
        document.getElementById(
            'cash-received'
        );

    const changeElement =
        document.getElementById(
            'cash-change'
        );


    if (
        !input ||
        !changeElement
    ) {

        return;

    }


    const received =
        Number(
            input.value || 0
        );


    const total =
        getCheckoutTotal();


    if (!received) {

        changeElement.innerText =
            'Rp 0';

        window.selectedCashReceived =
            null;

        return;

    }


    window.selectedCashReceived =
        received;


    if (received < total) {

        changeElement.innerText =
            'Kurang ' +
            rupiah(
                total - received
            );

        return;

    }


    changeElement.innerText =
        rupiah(
            received - total
        );

}


/* =========================================================
   SELECT DEBIT BANK
   ========================================================= */

function selectDebitBank(bank) {

    window.selectedDebitBank =
        bank;

}


/* =========================================================
   PAYMENT UI INIT
   ========================================================= */

function initPaymentUI() {

    document
        .querySelectorAll(
            'input[name="payment_method"]'
        )
        .forEach(input => {

            input.addEventListener(
                'change',
                function () {

                    updatePaymentDetail(
                        this.value
                    );

                }
            );

        });


    document
        .querySelectorAll(
            'input[name="debit_bank"]'
        )
        .forEach(input => {

            input.addEventListener(
                'change',
                function () {

                    selectDebitBank(
                        this.value
                    );

                }
            );

        });


    const cashInput =
        document.getElementById(
            'cash-received'
        );


    if (cashInput) {

        cashInput.addEventListener(
            'input',
            calculateCashChange
        );

    }

}


/* =========================================================
   PAYMENT VALIDATION
   ========================================================= */

function validatePaymentSelection() {

    const method =
        document.querySelector(
            'input[name="payment_method"]:checked'
        );


    if (!method) {

        showToast(
            'Silakan pilih metode pembayaran'
        );

        return false;

    }


    /* =====================================================
       DEBIT
       ===================================================== */

    if (method.value === 'Debit') {

        const bank =
            document.querySelector(
                'input[name="debit_bank"]:checked'
            );


        if (!bank) {

            showToast(
                'Silakan pilih bank debit'
            );

            return false;

        }


        window.selectedDebitBank =
            bank.value;

    }


    /* =====================================================
       CASH
       ===================================================== */

    if (method.value === 'Cash') {

        const received =

            Number(
                document.getElementById(
                    'cash-received'
                )?.value || 0
            );


        const total =
            getCheckoutTotal();


        if (received <= 0) {

            showToast(
                'Masukkan uang yang diterima'
            );

            return false;

        }


        if (received < total) {

            showToast(
                'Uang cash belum mencukupi'
            );

            return false;

        }


        window.selectedCashReceived =
            received;

    }


    return true;

}


/* =========================================================
   RESET PAYMENT UI
   ========================================================= */

function resetPaymentUI() {

    window.selectedPaymentMethod =
        null;

    window.selectedDebitBank =
        null;

    window.selectedCashReceived =
        null;


    document
        .querySelectorAll(
            'input[name="payment_method"]'
        )
        .forEach(input => {

            input.checked =
                false;

        });


    document
        .querySelectorAll(
            'input[name="debit_bank"]'
        )
        .forEach(input => {

            input.checked =
                false;

        });


    const container =
        document.getElementById(
            'checkout-payment-details'
        );


    if (container) {

        container.style.display =
            'none';

    }


    [

        'checkout-cash-detail',

        'checkout-qris-detail',

        'checkout-debit-detail'

    ].forEach(id => {

        const element =
            document.getElementById(id);


        if (element) {

            element.style.display =
                'none';

        }

    });


    const cashInput =
        document.getElementById(
            'cash-received'
        );


    if (cashInput) {

        cashInput.value =
            '';

    }


    const cashChange =
        document.getElementById(
            'cash-change'
        );


    if (cashChange) {

        cashChange.innerText =
            'Rp 0';

    }

}


/* =========================================================
   CHECKOUT
   ========================================================= */

async function checkout(
    paymentData = {}
) {

    if (!appState.cart.length) {

        showToast(
            'Keranjang masih kosong'
        );

        return;

    }

    // Cek apakah ada produk yang sudah habis
const unavailableItems = appState.cart.filter(cartItem => {

    const currentProduct =
        appState.menu.find(
            product =>
                Number(product.id) ===
                Number(cartItem.id)
        );

    return !currentProduct ||
           currentProduct.is_available !== true;

});

if (unavailableItems.length) {

    const productNames =
        unavailableItems
            .map(item => item.name)
            .join(', ');

    showToast(
        `Produk berikut sedang habis: ${productNames}`
    );

    // Refresh cart supaya produk habis terlihat
    updateCartUI();

    return;
}

    const csrfToken =

        document
            .querySelector(
                'meta[name="csrf-token"]'
            )
            ?.getAttribute(
                'content'
            );


    if (!csrfToken) {

        showToast(
            'CSRF token tidak ditemukan'
        );

        return;

    }


    const paymentMethod =

        paymentData.payment_method ||

        window.selectedPaymentMethod;


    if (!paymentMethod) {

        showToast(
            'Silakan pilih metode pembayaran'
        );

        return;

    }


    /* =====================================================
       ITEMS
       ===================================================== */

    const orderNote = document.getElementById('order-note')?.value.trim()
        || sessionStorage.getItem('quattro_order_note')
        || '';
    const items =

        appState.cart.map((item, index) => ({

            product_id:
                Number(item.id),

            quantity:
                Number(item.qty),
            customization: {
                ...(item.customization || {}),
                note: [item.customization?.note, index === 0 ? orderNote : '']
                    .filter(Boolean)
                    .join(' | ')
                    .slice(0, 300)
            }

        }));


    /* =====================================================
       TABLE
       ===================================================== */

    const tableInput =
        document.getElementById(
            'table-number'
        );

    const customerNameInput = document.getElementById('customer-name');


    /* =====================================================
       PAYLOAD
       ===================================================== */

    const payload = {

        customer_name:
            paymentData.customer_name ??
            (customerNameInput ? customerNameInput.value.trim() : window.quattroUser?.name),

        customer_phone: paymentData.customer_phone || null,
        guest_token: getGuestToken() || null,

        customer_email: paymentData.customer_email || null,

        table_number:

            paymentData.table_number ??

            (
                tableInput
                    ? tableInput.value.trim()
                    : null
            ),


        items:


            items,


        payment_method:


            paymentMethod,


        debit_bank:

            paymentData.debit_bank ||

            window.selectedDebitBank ||

            null,


        cash_received:

            paymentData.cash_received ??

            window.selectedCashReceived ??

            null

    };


    console.log(
        'CHECKOUT PAYLOAD:',
        payload
    );


    try {

        showToast(
            'Menyimpan transaksi...'
        );


        const response =

            await fetch(
                '/customer/orders',
                {

                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'X-CSRF-TOKEN': csrfToken,
                        'X-Guest-Token': getGuestToken()

                    },

                    body:
                        JSON.stringify(
                            payload
                        )

                }
            );


        let data = {};


        try {

            data =
                await response.json();

        }

        catch (error) {

            data = {};

        }


        /* =================================================
           ERROR SERVER
           ================================================= */

        if (!response.ok) {

            let message =

                data.message ||

                'Transaksi gagal dibuat';


            if (data.errors) {

                const firstError =

                    Object.values(
                        data.errors
                    )
                    .flat()
                    .find(Boolean);


                if (firstError) {

                    message =
                        firstError;

                }

            }


            throw new Error(
                message
            );

        }

        if (data.guest_token && !window.quattroUser?.id) {
            localStorage.setItem(guestTokenStorageKey, data.guest_token);
        }


        /* =================================================
           BERHASIL
           ================================================= */

        appState.cart = [];

        sessionStorage.removeItem('quattro_order_note');
        sessionStorage.removeItem('quattro_pickup_time');


        saveCustomerState();


        updateCartUI();


        window.selectedPaymentMethod =
            null;

        window.selectedDebitBank =
            null;

        window.selectedCashReceived =
            null;


        closePaymentModalIfExists();


        showToast(

            data.message ||

            `Transaksi ${
                data.order_number ?? ''
            } berhasil dibuat`

        );


        setTimeout(() => {

            window.location.href =
                '/customer/history';

        }, 800);

    }


    catch (error) {

        console.error(
            'Checkout Error:',
            error
        );


        showToast(

            error.message ||

            'Terjadi kesalahan saat membuat transaksi'

        );

    }

}


/* =========================================================
   CLOSE PAYMENT MODAL
   ========================================================= */

function closePaymentModalIfExists() {

    const modal =
        document.getElementById(
            'payment-modal'
        );


    if (!modal) {
        return;
    }


    modal.classList.remove(
        'show'
    );


    document.body.style.overflow =
        '';


    resetPaymentUI();

}


/* =========================================================
   HISTORY
   ========================================================= */

async function renderHistory() {

    const container = document.getElementById('history-container');

    if (!container) {
        return;
    }

    try {

        container.innerHTML = `
            <div class="history-loading">
                <i class="fa-solid fa-spinner fa-spin"></i>
                Memuat riwayat pesanan...
            </div>
        `;

        const response = await fetch('/customer/orders/history', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-Guest-Token': getGuestToken()
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Gagal mengambil riwayat pesanan');
        }

        const data = await response.json();

        if (data.guest_token && !window.quattroUser?.id) {
            localStorage.setItem(guestTokenStorageKey, data.guest_token);
        }

        const orders = Array.isArray(data.orders)
            ? data.orders
            : [];

        if (!orders.length) {

            container.innerHTML = `
                <div class="history-empty">

                    <i class="fa-solid fa-clock-rotate-left"></i>

                    <h4>
                        Belum Ada Riwayat
                    </h4>

                    <p>
                        Pesanan yang sudah dibuat akan muncul di sini.
                    </p>

                </div>
            `;

            return;
        }

        container.innerHTML = orders.map(order => {

            const status = order.status || 'Menunggu';

            let statusClass = 'waiting';

            if (status === 'Diproses') {
                statusClass = 'processing';
            }

            if (
                status === 'Selesai' ||
                status === 'Sudah Diambil'
            ) {
                statusClass = 'completed';
            }

            if (status === 'Dibatalkan') {
                statusClass = 'cancelled';
            }

            const items = Array.isArray(order.items)
                ? order.items
                : [];

            return `
                <div class="history-order-card">

                    <div class="history-order-header">

                        <div>

                            <strong>
                                #${order.order_number ?? '-'}
                            </strong>

                            <small>
                                ${order.created_at ?? '-'}
                            </small>

                        </div>

                        <span class="history-status ${statusClass}">
                            ${status}
                        </span>

                    </div>

                    <div class="history-order-items">

                        ${
                            items.length
                                ? items.map(item => `
                                    <div class="history-item">

                                        <div class="history-item-main">
                                            <div class="history-item-info">
                                                <strong>${item.product_name ?? '-'}</strong>
                                                <span>${item.quantity ?? 0}x</span>
                                                ${renderCustomizationSummary(item.customization)}
                                            </div>
                                            <strong class="history-item-price">${rupiah(item.subtotal)}</strong>
                                        </div>

                                        <div class="history-item-review">
                                            ${item.review
                                                ? `<div class="history-review-existing"><strong>Ulasanmu · ★ ${Number(item.review.rating)}/5</strong><p>${escapeHtml(item.review.comment)}</p></div>`
                                                : ''}
                                            <button type="button" class="history-review-toggle" onclick="toggleOrderItemReview(${Number(item.id)})">${item.review ? 'Ubah Ulasan' : 'Beri Ulasan'}</button>
                                            <form id="order-review-form-${Number(item.id)}" class="history-review-form" ${item.review ? `data-rating="${Number(item.review.rating)}"` : ''} hidden onsubmit="event.preventDefault();submitOrderItemReview(${Number(item.id)}, ${Number(item.product_id)})">
                                                <span class="history-review-label">Penilaianmu</span>
                                                <div class="review-star-picker" role="group" aria-label="Pilih rating">
                                                    ${[1, 2, 3, 4, 5].map(value => `<button type="button" class="review-star-button ${Number(item.review?.rating || 0) >= value ? 'selected' : ''}" data-order-item="${Number(item.id)}" onclick="setOrderItemRating(${Number(item.id)}, ${value})" aria-label="${value} bintang"><i class="fa-solid fa-star"></i></button>`).join('')}
                                                </div>
                                                <textarea id="order-review-comment-${Number(item.id)}" maxlength="500" required placeholder="Apa yang disukai atau kurang dari menu ini?">${escapeHtml(item.review?.comment || '')}</textarea>
                                                <button type="submit" class="product-review-submit">Kirim Ulasan</button>
                                            </form>
                                        </div>

                                    </div>
                                `).join('')
                                : `
                                    <div class="history-item">
                                        <span>
                                            Tidak ada detail produk
                                        </span>
                                    </div>
                                `
                        }

                    </div>

                    <div class="history-order-footer">

                        <span>
                            Total Pembayaran
                        </span>

                        <strong>
                            ${rupiah(order.total)}
                        </strong>

                    </div>

                    ${
                        order.payment_method
                            ? `
                                <div class="history-payment">

                                    <span>
                                        Pembayaran:
                                    </span>

                                    <strong>
                                        ${order.payment_method}
                                    </strong>

                                    <span class="payment-success">
                                        ${order.payment_status ?? 'Berhasil'}
                                    </span>

                                </div>
                            `
                            : ''
                    }

                </div>
            `;

        }).join('');

    } catch (error) {

        console.error(
            'Gagal memuat history customer:',
            error
        );

        container.innerHTML = `
            <div class="history-empty">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <h4>
                    Riwayat gagal dimuat
                </h4>

                <p>
                    Silakan refresh halaman dan coba lagi.
                </p>

            </div>
        `;
    }
}

function toggleOrderItemReview(orderItemId) {
    const form = document.getElementById(`order-review-form-${orderItemId}`);
    if (!form) return;
    form.hidden = !form.hidden;
    if (!historyReviewRatings[orderItemId]) {
        historyReviewRatings[orderItemId] = Number(form.dataset.rating || 0);
    }
}

function setOrderItemRating(orderItemId, rating) {
    historyReviewRatings[orderItemId] = Number(rating);
    document.querySelectorAll(`.review-star-button[data-order-item="${orderItemId}"]`).forEach((button, index) => {
        button.classList.toggle('selected', index < Number(rating));
    });
}

async function submitOrderItemReview(orderItemId, productId) {
    const form = document.getElementById(`order-review-form-${orderItemId}`);
    const rating = Number(historyReviewRatings[orderItemId] || form?.dataset.rating || 0);
    const comment = document.getElementById(`order-review-comment-${orderItemId}`)?.value.trim() || '';
    if (!rating) return showToast('Pilih rating bintang terlebih dahulu.');
    if (comment.length < 3) return showToast('Tulis ulasan minimal 3 karakter.');

    try {
        const response = await fetch(`/customer/products/${productId}/reviews`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Guest-Token': getGuestToken()
            },
            credentials: 'same-origin',
            body: JSON.stringify({ order_item_id: orderItemId, rating, comment })
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Ulasan gagal disimpan.');
        showToast(data.message || 'Ulasan berhasil disimpan.');
        renderHistory();
    } catch (error) {
        showToast(error.message || 'Ulasan gagal disimpan.');
    }
}


/* =========================================================
   FAVORITE
   ========================================================= */

async function toggleFavorite(id) {
    try {
        const response = await fetch('/customer/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector(
                    'meta[name="csrf-token"]'
                )?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                product_id: id
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(
                data.message || 'Gagal mengubah favorit.'
            );
        }

        const numericId = Number(id);

        if (data.favorite) {
            if (!appState.favorites.includes(numericId)) {
                appState.favorites.push(numericId);
            }
        } else {
            appState.favorites = appState.favorites.filter(
                favoriteId => Number(favoriteId) !== numericId
            );
        }

        // Tetap simpan state lain seperti cart/history.
        saveCustomerState();

        // Update tampilan menu.
        renderMenu(appState.menu);

        // Update halaman favorites.
        renderFavorites();

    } catch (error) {
        console.error('Favorite error:', error);

        alert(
            error.message ||
            'Terjadi kesalahan saat mengubah favorit.'
        );
    }
}


/* =========================================================
   FAVORITES
   ========================================================= */

function renderFavorites() {

    const container =
        document.getElementById(
            'fav-container'
        );


    if (!container) {
        return;
    }


    const items =

        appState.menu.filter(
            item =>
                appState.favorites
                    .includes(item.id)
        );


    if (!items.length) {

        container.innerHTML = `

            <div
                class="empty-state"
                style="grid-column:span 2"
            >

                <i class="fa-solid fa-heart-crack"></i>

                <p>
                    Belum ada menu favorit.
                </p>

            </div>

        `;

        return;

    }


    container.innerHTML =

        items.map(item => `

            <div class="fav-card">

                <button
                    class="fav-remove"
                    onclick="toggleFavorite(
                        ${item.id}
                    )"
                >

                    <i class="fa-solid fa-xmark"></i>

                </button>


                <img
                    src="${item.img}"
                    alt="${item.name}"
                >


                <h5>
                    ${item.name}
                </h5>


                <p>
                    ${rupiah(item.price)}
                </p>


                <div class="fav-footer">

                    <span class="rating">

                        <i class="fa-solid fa-star"></i>

                        ${item.reviewCount ? `${Number(item.rating).toFixed(1)} (${item.reviewCount})` : 'Baru'}

                    </span>


                    <button
                        class="add-btn"
                        onclick="addToCart(
                            ${item.id}
                        )"
                    >

                        <i class="fa-solid fa-plus"></i>

                    </button>

                </div>

            </div>

        `).join('');

}


/* =========================================================
   PASSWORD TOGGLE
   ========================================================= */

function togglePassword(
    inputId,
    eyeId
) {

    const input =
        document.getElementById(
            inputId
        );

    const eye =
        document.getElementById(
            eyeId
        );


    if (!input || !eye) {
        return;
    }


    if (input.type === 'password') {

        input.type =
            'text';


        eye.classList.remove(
            'fa-eye-slash'
        );

        eye.classList.add(
            'fa-eye'
        );

    }

    else {

        input.type =
            'password';


        eye.classList.remove(
            'fa-eye'
        );

        eye.classList.add(
            'fa-eye-slash'
        );

    }

}


/* =========================================================
   DOM READY
   ========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        loadCustomerState();

        const detailModal = document.getElementById('product-detail-modal');
        detailModal?.addEventListener('click', event => {
            if (event.target === detailModal) closeProductDetail();
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && activeDetailProduct) closeProductDetail();
        });

        renderMenu(
            appState.menu
        );

        renderFavorites();

        updateCartUI();

        renderHistory();

        initPaymentUI();

    }
);
