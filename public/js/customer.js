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

    const normalized = String(category)
        .toLowerCase()
        .trim()
        .replace(/&/g, ' and ')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');

    const aliases = {
        coffee: ['coffee', 'coffees', 'kopi', 'minuman-kopi'],
        'non-coffee': ['non-coffee', 'noncoffee', 'non-kopi', 'minuman-non-kopi'],
        food: ['food', 'foods', 'makanan', 'makanan-berat', 'makanan-utama', 'menu-utama', 'main-course'],
        snack: ['snack', 'snacks', 'camilan', 'cemilan', 'makanan-ringan'],
        bakery: ['bakery', 'bakery-and-pastry', 'bakery-pastry', 'pastry', 'roti'],
        dessert: ['dessert', 'desserts', 'pencuci-mulut', 'hidangan-penutup']
    };

    for (const [canonical, values] of Object.entries(aliases)) {
        if (values.includes(normalized)) return canonical;
    }

    return normalized;

}


/* =========================================================
   DATABASE PRODUCTS
   ========================================================= */

const databaseProducts =

    Array.isArray(window.quattroProducts)

        ? window.quattroProducts

        : [];

const savedProductImageRequests = new Map();
let savedProductImageObserver = null;

function productImageAttributes(item) {
    const loadingAttributes = 'loading="lazy" decoding="async"';
    if (String(item.img || '').startsWith('/product-images/')) {
        const fallback = productImages[item.name] || '';
        return `data-product-src="${item.img}" data-fallback-src="${fallback}" ${loadingAttributes}`;
    }
    return `src="${item.img}" ${loadingAttributes}`;
}

function loadSavedProductImage(img) {
    if (!img || img.dataset.imageLoading === 'true') return;
    img.dataset.imageLoading = 'true';

    const url = img.dataset.productSrc;
    let request = savedProductImageRequests.get(url);
    if (!request) {
        request = fetch(url, { headers: { 'ngrok-skip-browser-warning': 'true' } })
            .then(response => {
                if (!response.ok || !response.headers.get('content-type')?.startsWith('image/')) {
                    throw new Error('Foto produk tidak dapat dimuat.');
                }
                return response.blob();
            })
            .then(blob => URL.createObjectURL(blob))
            .catch(error => {
                savedProductImageRequests.delete(url);
                throw error;
            });
        savedProductImageRequests.set(url, request);
    }

    request.then(objectUrl => {
        if (img.isConnected) img.src = objectUrl;
    }).catch(() => {
        if (!img.isConnected) return;
        img.onerror = function () {
            const fallback = img.dataset.fallbackSrc;
            if (fallback && !img.dataset.fallbackAttempted) {
                img.dataset.fallbackAttempted = 'true';
                img.src = fallback;
                return;
            }
            img.hidden = true;
        };
        img.src = url;
    });
}

function loadSavedProductImages(root) {
    const images = root?.matches?.('img[data-product-src]')
        ? [root]
        : root?.querySelectorAll?.('img[data-product-src]');
    if (!images) return;

    if (!savedProductImageObserver && 'IntersectionObserver' in window) {
        savedProductImageObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                savedProductImageObserver.unobserve(entry.target);
                loadSavedProductImage(entry.target);
            });
        }, { rootMargin: '180px 0px' });
    }

    images.forEach(img => {
        if (img.dataset.imageLoading === 'true' || img.dataset.imageObserved === 'true') return;
        if (savedProductImageObserver) {
            img.dataset.imageObserved = 'true';
            savedProductImageObserver.observe(img);
        } else {
            loadSavedProductImage(img);
        }
    });
}
function loadProductImage(img, url, fallback = '') {
    if (!img) return;
    if (String(url || '').startsWith('/product-images/')) {
        img.dataset.productSrc = url;
        img.dataset.fallbackSrc = fallback;
        loadSavedProductImages(img.parentElement || img);
        return;
    }
    img.src = url || fallback;
}

function getSavedProductImage(product) {
    const image = String(product?.image || '').trim();
    if (!image) return '';
    if (/^https?:\/\//i.test(image)) return image;
    // The same product image route is used by the Owner catalog. Including
    // the stored path as a version avoids showing an older cached photo.
    return `/product-images/${encodeURIComponent(product.id)}?v=${encodeURIComponent(image)}`;
}


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
            originalPrice: Number(product.original_price ?? product.price),
            discountPercent: Number(product.discount_percent ?? 0),

            // STATUS KETERSEDIAAN DARI DATABASE
            stock: Number(product.stock ?? 0),

            is_available:
                (product.is_available === true ||
                product.is_available === 1 ||
                product.is_available === '1') && Number(product.stock ?? 0) > 0,

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
                getSavedProductImage(product) || (productImages[name] ??
                'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500&auto=format&fit=crop')

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
window.appState = appState;

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

    const icon = toast.querySelector('i') || document.createElement('i');
    const normalizedMessage = String(message || 'Pemberitahuan');
    const lowerMessage = normalizedMessage.toLocaleLowerCase('id-ID');
    const type = /gagal|error|tidak dapat|belum dapat|tidak berhasil|tidak tersedia/.test(lowerMessage)
        ? 'error'
        : (/habis|tersisa|yang tersedia hanya|mencapai batas|kurangi jumlah/.test(lowerMessage)
            ? 'warning'
            : (/berhasil|ditambahkan|disimpan|dibuat|diperbarui|dibatalkan/.test(lowerMessage) ? 'success' : 'info'));
    const icons = {
        success: 'fa-circle-check',
        warning: 'fa-triangle-exclamation',
        error: 'fa-circle-exclamation',
        info: 'fa-circle-info',
    };

    if (!icon.isConnected) toast.prepend(icon);
    icon.className = `fa-solid ${icons[type]} toast-icon`;
    toast.classList.remove('toast-success', 'toast-warning', 'toast-error', 'toast-info');
    toast.classList.add(`toast-${type}`);
    toastMsg.textContent = normalizedMessage;

    toast.classList.add('show');

    clearTimeout(window.__customerToastTimer);
    window.__customerToastTimer = setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);

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
        const categoryName = {
            coffee: 'Coffee',
            'non-coffee': 'Non Coffee',
            food: 'Makanan Berat',
            snack: 'Snack',
            bakery: 'Bakery & Pastry',
            dessert: 'Dessert'
        }[normalizeCategory(activeMenuCategory)];
        const emptyMessage = categoryName
            ? `Belum ada menu untuk kategori ${categoryName}`
            : 'Menu tidak ditemukan';

        container.innerHTML = `

            <div class="empty-state">

                <i class="fa-solid fa-mug-hot"></i>

                <p>
                    ${emptyMessage}
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
            const stock = Math.max(0, Number(item.stock || 0));
            const availabilityText = stock <= 0
                ? 'Tersisa 0'
                : (!isAvailable ? 'Tidak tersedia' : `Tersisa ${stock}`);
            const availabilityClass = stock <= 0 ? 'out' : (!isAvailable ? 'unavailable' : (stock <= 5 ? 'low' : 'available'));
            const priceMarkup = Number(item.discountPercent) > 0
                ? `<span class="price price-discount"><del>${rupiah(item.originalPrice)}</del><strong>${rupiah(item.price)}</strong><em>-${item.discountPercent}%</em></span>`
                : `<span class="price">${rupiah(item.price)}</span>`;

            return `

                <article class="menu-card ${!isAvailable ? 'product-unavailable' : ''}" onclick="openProductDetail(${item.id})" tabindex="0" role="button" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openProductDetail(${item.id})}">

                    <div class="menu-image-wrapper">

                        <img ${productImageAttributes(item)} alt="${item.name}">

                        ${
                            !isAvailable
                                ? `
                                    <span class="availability-badge unavailable">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                        ${stock <= 0 ? 'Habis' : 'Tidak tersedia'}
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

                            ${priceMarkup}

                            <small class="stock-warning ${availabilityClass}">${availabilityText}</small>

                            ${productCartQty > 0
                                ? `
                                    <div class="menu-card-qty-control" onclick="event.stopPropagation()" aria-label="Jumlah ${item.name}">
                                        <button type="button" aria-label="Kurangi ${item.name}" onclick="updateProductCartQty(${item.id}, -1)">−</button>
                                        <span>${productCartQty}</span>
                                        <button type="button" aria-label="Tambah ${item.name}" ${productCartQty >= stock ? 'disabled' : ''} onclick="updateProductCartQty(${item.id}, 1)">+</button>
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

    loadSavedProductImages(container);

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

    const alreadyInCart = appState.cart
        .filter(cartItem => Number(cartItem.id) === Number(item.id))
        .reduce((sum, cartItem) => sum + Number(cartItem.qty || 0), 0);
    if (alreadyInCart + Number(quantity) > Number(item.stock || 0)) {
        showToast(`Jumlah ${item.name} yang tersedia hanya ${Number(item.stock || 0)}. Pesanan di keranjang sudah mencapai batas.`);
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

    const detailImage = document.getElementById('detail-product-image');
    detailImage.alt = product.name;
    loadProductImage(detailImage, product.img, productImages[product.name] || '');
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
                    <div><strong>${escapeHtml(review.customer_name || 'Pelanggan')}</strong><span>★ ${Number(review.rating)} / 5</span></div>
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
    const alreadyInCart = appState.cart
        .filter(cartItem => Number(cartItem.id) === Number(activeDetailProduct?.id))
        .reduce((sum, cartItem) => sum + Number(cartItem.qty || 0), 0);
    const nextQuantity = Math.max(1, activeDetailQuantity + delta);
    if (delta > 0 && alreadyInCart + nextQuantity > Number(activeDetailProduct?.stock || 0)) {
        showToast(`Jumlah ${activeDetailProduct?.name} yang tersedia hanya ${Number(activeDetailProduct?.stock || 0)}.`);
        return;
    }
    activeDetailQuantity = nextQuantity;
    updateProductDetailPrice();
}

function updateProductDetailPrice() {
    if (!activeDetailProduct) return;
    const price = getConfiguredPrice(activeDetailProduct, activeProductOptions);
    document.getElementById('detail-product-price').textContent = rupiah(price);
    const originalPriceElement = document.getElementById('detail-original-price');
    const discountBadgeElement = document.getElementById('detail-discount-badge');
    const discountPercent = Number(activeDetailProduct.discountPercent || 0);
    if (originalPriceElement) {
        originalPriceElement.textContent = rupiah(activeDetailProduct.originalPrice ?? activeDetailProduct.price);
        originalPriceElement.hidden = discountPercent <= 0;
    }
    if (discountBadgeElement) {
        discountBadgeElement.textContent = '-' + discountPercent + '%';
        discountBadgeElement.hidden = discountPercent <= 0;
    }
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

    if (delta > 0) {
        const product = appState.menu.find(menu => Number(menu.id) === Number(menuId));
        const currentQuantity = appState.cart
            .filter(cartItem => Number(cartItem.id) === Number(menuId))
            .reduce((sum, cartItem) => sum + Number(cartItem.qty || 0), 0);
        if (!product || currentQuantity + delta > Number(product.stock || 0)) {
            showToast(`Jumlah ${product?.name || item.name} yang tersedia hanya ${Number(product?.stock || 0)}.`);
            return;
        }
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

                <img ${productImageAttributes(item)} alt="${item.name}">

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

    loadSavedProductImages(container);


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

let checkoutInProgress = false;

function setCheckoutButtonLoading(button, label) {
    if (!button) return;
    if (!button.dataset.originalHtml) button.dataset.originalHtml = button.innerHTML;
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.innerHTML = `<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i><span>${escapeHtml(label)}</span>`;
}

function restoreCheckoutButton(button) {
    if (!button) return;
    if (button.dataset.originalHtml) button.innerHTML = button.dataset.originalHtml;
    delete button.dataset.originalHtml;
    button.disabled = false;
    button.removeAttribute('aria-busy');
}

function showCheckoutLoading(message = 'Menyiapkan pesanan') {
    let overlay = document.getElementById('quattro-checkout-loading');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'quattro-checkout-loading';
        overlay.className = 'quattro-checkout-loading';
        overlay.setAttribute('role', 'status');
        overlay.setAttribute('aria-live', 'polite');
        overlay.innerHTML = '<div class="quattro-checkout-loading-card"><span class="quattro-checkout-spinner" aria-hidden="true"></span><strong></strong><small>Mohon tunggu sebentar</small></div>';
        document.body.appendChild(overlay);
    }
    overlay.querySelector('strong').textContent = message;
    overlay.hidden = false;
    document.body.classList.add('quattro-checkout-busy');
}

function hideCheckoutLoading() {
    const overlay = document.getElementById('quattro-checkout-loading');
    if (overlay) overlay.hidden = true;
    document.body.classList.remove('quattro-checkout-busy');
}

async function checkout(
    paymentData = {}
) {

    if (checkoutInProgress) return;

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

    const totalInCart = appState.cart
        .filter(item => Number(item.id) === Number(cartItem.id))
        .reduce((sum, item) => sum + Number(item.qty || 0), 0);
    return !currentProduct || currentProduct.is_available !== true || totalInCart > Number(currentProduct.stock || 0);

});

if (unavailableItems.length) {

    const productNames =
        unavailableItems
            .map(item => item.name)
            .join(', ');

    const changedQuantity = unavailableItems.find(cartItem => {
        const currentProduct = appState.menu.find(product => Number(product.id) === Number(cartItem.id));
        const totalInCart = appState.cart.filter(item => Number(item.id) === Number(cartItem.id))
            .reduce((sum, item) => sum + Number(item.qty || 0), 0);
        return currentProduct && totalInCart > Number(currentProduct.stock || 0);
    });

    if (changedQuantity) {
        const currentProduct = appState.menu.find(product => Number(product.id) === Number(changedQuantity.id));
        showToast(`Jumlah ${currentProduct.name} yang tersedia hanya ${currentProduct.stock}. Kurangi jumlah di keranjang.`);
        return;
    }

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

        bank_account_id: paymentData.bank_account_id || null,

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


    try {

        checkoutInProgress = true;
        const checkoutStartedAt = Date.now();
        setCheckoutButtonLoading(document.getElementById('pay-payment-button'), 'Memproses pesanan…');
        showCheckoutLoading('Mengirim pesanan');

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


        // Keep the feedback visible long enough to notice even on a very fast response.
        await new Promise(resolve => window.setTimeout(resolve, Math.max(0, 500 - (Date.now() - checkoutStartedAt))));

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

            if (data.require_login && data.login_url) {
                showToast(`${data.message} Mengalihkan ke halaman masuk…`);
                setTimeout(() => { window.showQuattroPageTransition?.(data.login_url); window.location.assign(data.login_url); }, 1400);
                return;
            }

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


        saveCustomerState();


        updateCartUI();


        window.selectedPaymentMethod =
            null;

        window.selectedDebitBank =
            null;

        window.selectedCashReceived =
            null;


        closePaymentModalIfExists();


        if (data.bank_transfer_url) {
            showToast('Pesanan dibuat. Menampilkan rekening transfer...');
            setTimeout(() => { window.showQuattroPageTransition?.(data.bank_transfer_url); window.location.href = data.bank_transfer_url; }, 400);
        } else if (data.payment_url) {
            showToast('Pesanan dibuat. Membuka pembayaran QRIS...');
            setTimeout(() => { window.showQuattroPageTransition?.(data.payment_url);
        window.location.href = data.payment_url; }, 500);
        } else {
            showToast(data.message || `Pesanan ${data.order_number ?? ''} dibuat. QRIS belum tersedia; silakan bayar dari Riwayat Pesanan.`);
            setTimeout(() => { window.showQuattroPageTransition?.('/customer/history'); window.location.href = '/customer/history'; }, 1200);
        }

    }


    catch (error) {

        checkoutInProgress = false;
        hideCheckoutLoading();
        restoreCheckoutButton(document.getElementById('pay-payment-button'));

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

                                    <span class="history-payment-status ${order.payment_status === 'Dibayar' ? 'is-paid' : order.payment_status === 'Dibatalkan' ? 'is-cancelled' : 'is-pending'}">${order.payment_status ?? 'Belum Dibayar'}</span>

                                </div>
                            `
                            : ''
                    }

                    ${String(order.payment_method || '').toUpperCase() === 'QRIS' && order.payment_status !== 'Dibayar' && order.status !== 'Dibatalkan' ? `
                        <div class="history-payment-action">
                            <div class="history-payment-timer ${order.payment_deadline && Date.parse(order.payment_deadline) > Date.now() ? '' : 'is-expired'}">
                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                <span data-history-countdown data-deadline-ms="${order.payment_deadline ? Date.parse(order.payment_deadline) : ''}">${order.payment_deadline && Date.parse(order.payment_deadline) > Date.now() ? 'Sisa waktu…' : 'QRIS siap dibuat'}</span>
                            </div>
                            <div class="history-order-actions">
                                <a class="history-qris-pay" href="/customer/orders/${Number(order.id)}/payment">${order.payment_deadline && Date.parse(order.payment_deadline) > Date.now() ? 'Bayar Sekarang' : 'Buat QR Baru'}</a>
                                ${order.can_cancel_order ? `<button type="button" class="history-cancel-order" onclick="openCancelOrderDialog(${Number(order.id)})"><i class="fa-regular fa-circle-xmark" aria-hidden="true"></i> Batalkan</button>` : ''}
                            </div>
                        </div>
                    ` : ''}

                    ${String(order.payment_method || '').toUpperCase() === 'BANK TRANSFER' && order.payment_status !== 'Dibayar' && order.status !== 'Dibatalkan' ? `
                        <div class="history-payment-action">
                            <div class="history-order-actions">
                                <a class="history-qris-pay" href="/customer/orders/${Number(order.id)}/bank-transfer">Lihat rekening transfer</a>
                                ${order.can_cancel_order ? `<button type="button" class="history-cancel-order" onclick="openCancelOrderDialog(${Number(order.id)})"><i class="fa-regular fa-circle-xmark" aria-hidden="true"></i> Batalkan</button>` : ''}
                            </div>
                        </div>
                    ` : ''}

                    ${order.status === 'Dibatalkan' ? `
                        <div class="history-cancellation-note" style="padding:12px 16px 16px;background:#fff8f5;border-top:1px solid #f0e5dc;color:#765b49;font-size:13px">
                            <strong style="color:#754000">Pesanan dibatalkan${order.cancelled_at ? ` · ${escapeHtml(order.cancelled_at)}` : ''}</strong>
                            <div style="margin-top:4px">Alasan: ${escapeHtml(order.cancellation_reason || 'Dibatalkan oleh pelanggan.')}</div>
                        </div>
                    ` : ''}

                </div>
            `;

        }).join('');
        updateHistoryCountdowns();
        if (!window.quattroHistoryCountdownInterval) {
            window.quattroHistoryCountdownInterval = window.setInterval(updateHistoryCountdowns, 1000);
        }

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

function updateHistoryCountdowns() {
    document.querySelectorAll('[data-history-countdown]').forEach(element => {
        const deadline = Number(element.dataset.deadlineMs);
        const wrapper = element.closest('.history-payment-action');
        const timer = element.closest('.history-payment-timer');
        const payLink = wrapper?.querySelector('.history-qris-pay');
        if (!deadline || !Number.isFinite(deadline)) {
            element.textContent = 'QRIS siap dibuat';
            timer?.classList.add('is-expired');
            if (payLink) payLink.textContent = 'Buat QR Baru';
            return;
        }
        const secondsLeft = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
        if (secondsLeft === 0) {
            element.textContent = 'Waktu habis · buat QR baru';
            timer?.classList.add('is-expired');
            if (payLink) payLink.textContent = 'Buat QR Baru';
            return;
        }
        const minutes = String(Math.floor(secondsLeft / 60)).padStart(2, '0');
        const seconds = String(secondsLeft % 60).padStart(2, '0');
        element.textContent = `Sisa waktu ${minutes}:${seconds}`;
        timer?.classList.remove('is-expired');
        if (payLink) payLink.textContent = 'Bayar Sekarang';
    });
}

window.openCancelOrderDialog = function (orderId) {
    let dialog = document.getElementById('customer-cancel-order-dialog');
    if (!dialog) {
        dialog = document.createElement('dialog');
        dialog.id = 'customer-cancel-order-dialog';
        dialog.className = 'quattro-cancel-dialog';
        dialog.innerHTML = `
            <form id="customer-cancel-order-form" class="quattro-cancel-card">
                <header class="quattro-cancel-header">
                    <div>
                        <span class="quattro-cancel-eyebrow">QUATTRO COFFEE</span>
                        <h2>Pilih Alasan Pembatalan</h2>
                    </div>
                    <button type="button" class="quattro-cancel-close" data-cancel-dialog-close aria-label="Tutup">&times;</button>
                </header>
                <div class="quattro-cancel-warning"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i><span>Semua menu dalam pesanan ini akan dibatalkan. Pesanan yang sudah dibatalkan tidak dapat dipulihkan.</span></div>
                <fieldset class="quattro-reason-list">
                    <legend class="quattro-cancel-label">Kenapa pesanan dibatalkan?</legend>
                    <label class="quattro-reason-option"><input type="radio" name="cancel_reason" value="Saya berubah pikiran" required><span>Saya berubah pikiran</span></label>
                    <label class="quattro-reason-option"><input type="radio" name="cancel_reason" value="Salah memilih menu atau jumlah"><span>Salah memilih menu atau jumlah</span></label>
                    <label class="quattro-reason-option"><input type="radio" name="cancel_reason" value="Pesanan dibuat dua kali"><span>Pesanan dibuat dua kali</span></label>
                    <label class="quattro-reason-option"><input type="radio" name="cancel_reason" value="Waktu tunggu terlalu lama"><span>Waktu tunggu terlalu lama</span></label>
                    <label class="quattro-reason-option"><input type="radio" name="cancel_reason" value="Ada kendala pembayaran"><span>Ada kendala pembayaran</span></label>
                    <label class="quattro-reason-option"><input type="radio" name="cancel_reason" value="Saya tidak ingin membeli lagi"><span>Saya tidak ingin membeli lagi</span></label>
                    <label class="quattro-reason-option"><input type="radio" name="cancel_reason" value="Lainnya"><span>Lainnya</span></label>
                </fieldset>
                <div id="customer-cancel-other-wrap" class="quattro-cancel-other" hidden>
                    <label class="quattro-cancel-label" for="customer-cancel-other-reason">Tulis alasan lainnya</label>
                    <textarea id="customer-cancel-other-reason" maxlength="500" rows="3" placeholder="Ceritakan alasan pembatalan…"></textarea>
                </div>
                <button type="submit" class="quattro-cancel-confirm">Konfirmasi Pembatalan</button>
            </form>`;
        document.body.appendChild(dialog);
        dialog.querySelectorAll('input[name="cancel_reason"]').forEach(input => input.addEventListener('change', event => {
            const otherWrap = dialog.querySelector('#customer-cancel-other-wrap');
            const otherField = dialog.querySelector('#customer-cancel-other-reason');
            const isOther = event.target.value === 'Lainnya';
            otherWrap.hidden = !isOther;
            otherField.required = isOther;
            dialog.querySelectorAll('.quattro-reason-option').forEach(row => row.classList.toggle('is-selected', row.contains(event.target)));
            if (isOther) otherField.focus();
        }));
        dialog.addEventListener('click', event => {
            if (event.target === dialog || event.target.closest('[data-cancel-dialog-close]')) dialog.close();
        });
        dialog.querySelector('form').addEventListener('submit', async event => {
            event.preventDefault();
            const form = event.currentTarget;
            const submit = form.querySelector('[type="submit"]');
            const selectedReason = form.querySelector('input[name="cancel_reason"]:checked')?.value || '';
            const customReason = form.querySelector('#customer-cancel-other-reason').value.trim();
            const reason = selectedReason === 'Lainnya' ? customReason : selectedReason;
            submit.disabled = true;
            submit.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Memproses…';
            try {
                const response = await fetch(`/customer/orders/${Number(form.dataset.orderId)}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Guest-Token': getGuestToken()
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ reason })
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data.message || 'Pesanan belum dapat dibatalkan.');
                dialog.close();
                showToast(data.message || 'Pesanan berhasil dibatalkan.');
                await renderHistory();
            } catch (error) {
                showToast(error.message || 'Pesanan belum dapat dibatalkan.');
            } finally {
                submit.disabled = false;
                submit.textContent = 'Konfirmasi Pembatalan';
            }
        });
    }
    const form = dialog.querySelector('form');
    form.dataset.orderId = String(Number(orderId));
    form.querySelectorAll('input[name="cancel_reason"]').forEach(input => { input.checked = false; });
    form.querySelectorAll('.quattro-reason-option').forEach(row => row.classList.remove('is-selected'));
    form.querySelector('#customer-cancel-other-reason').value = '';
    form.querySelector('#customer-cancel-other-reason').required = false;
    form.querySelector('#customer-cancel-other-wrap').hidden = true;
    dialog.showModal();
};

function formatPaymentDeadline(value) {
    if (!value) return '—';
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? '—' : date.toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
}

window.resumeQrisPayment = async function (orderId, retry) {
    try {
        if (!retry) {
            window.showQuattroPageTransition?.(`/customer/orders/${orderId}/payment`);
            window.location.href = `/customer/orders/${orderId}/payment`;
            return;
        }
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const response = await fetch(`/customer/orders/${orderId}/payment/retry`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Guest-Token': getGuestToken() }, credentials: 'same-origin' });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.payment_url) throw new Error(data.message || 'QRIS belum dapat dibuka.');
        window.showQuattroPageTransition?.(data.payment_url);
        window.location.href = data.payment_url;
    } catch (error) {
        showToast(error.message || 'Pembayaran belum dapat dibuka.');
    }
};

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

        showToast(
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


                <img ${productImageAttributes(item)} alt="${item.name}">


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

    loadSavedProductImages(container);

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

        const quickCartBar = document.getElementById('quick-cart-bar');
        quickCartBar?.addEventListener('click', event => {
            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
            if (quickCartBar.dataset.busy === 'true') {
                event.preventDefault();
                return;
            }
            event.preventDefault();
            quickCartBar.dataset.busy = 'true';
            const checkoutLabel = quickCartBar.querySelector('.quick-cart-checkout');
            if (checkoutLabel) {
                checkoutLabel.setAttribute('aria-busy', 'true');
                checkoutLabel.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i><span>Menyiapkan…</span>';
            }
            showCheckoutLoading('Membuka keranjang');
            window.setTimeout(() => { window.location.href = quickCartBar.href; }, 550);
        });

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




let customerLiveProductSyncTimer = null;
let customerLiveProductSyncInFlight = false;

window.addEventListener('quattro:data-changed', event => {
    if (event.detail?.type !== 'products' || !document.getElementById('main-menu-list')) return;
    window.clearTimeout(customerLiveProductSyncTimer);
    customerLiveProductSyncTimer = window.setTimeout(syncCustomerProductsLive, 220);
});

async function syncCustomerProductsLive() {
    if (customerLiveProductSyncInFlight || document.visibilityState !== 'visible') return;
    customerLiveProductSyncInFlight = true;
    try {
        const response = await fetch('/customer/live/products', {
            headers: { 'Accept': 'application/json', 'ngrok-skip-browser-warning': 'true' },
            credentials: 'same-origin',
            cache: 'no-store'
        });
        if (!response.ok) return;
        const data = await response.json();
        const previousMenu = new Map(appState.menu.map(item => [Number(item.id), item]));
        appState.menu = (data.products || []).map(product => {
            const previous = previousMenu.get(Number(product.id)) || {};
            return {
                ...previous,
                id: Number(product.id),
                name: product.name,
                category: normalizeCategory(product.category),
                price: Number(product.price || 0),
                originalPrice: Number(previous.originalPrice ?? product.price ?? 0),
                discountPercent: Number(previous.discountPercent || 0),
                stock: Math.max(0, Number(product.stock || 0)),
                is_available: Boolean(product.is_available) && Number(product.stock || 0) > 0,
                rating: product.reviews_avg_rating == null ? null : Number(product.reviews_avg_rating),
                reviewCount: Number(product.reviews_count || 0),
                desc: productDescriptions[product.name] || product.description || previous.desc || 'Menu pilihan Quattro Coffee',
                img: getSavedProductImage(product) || productImages[product.name] || previous.img || ''
            };
        });
        if (activeDetailProduct) {
            activeDetailProduct = appState.menu.find(item => Number(item.id) === Number(activeDetailProduct.id)) || activeDetailProduct;
        }
        filterMenu();
        updateCartUI();
    } catch (error) {
        console.warn('Pembaruan menu realtime gagal.', error);
    } finally {
        customerLiveProductSyncInFlight = false;
    }
}
window.addEventListener('quattro:data-changed', event => {
    if (!['orders', 'payments'].includes(event.detail?.type)) return;
    if (document.getElementById('history-container')) renderHistory();
});
