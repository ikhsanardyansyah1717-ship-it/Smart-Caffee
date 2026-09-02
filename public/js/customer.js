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

            rating:
                productRatings[name] ?? 4.8,

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

    favorites: [],

    history: []

};


/* =========================================================
   STORAGE KEY
   ========================================================= */

const storageKey =

    `quattro_customer_${
        window.quattroUser?.id ?? 'guest'
    }`;


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


        appState.cart =

            Array.isArray(saved.cart)

                ? saved.cart

                : [];


        appState.favorites =

            Array.isArray(saved.favorites)

                ? saved.favorites

                : [];


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

            favorites:
                appState.favorites,

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
        document.getElementById(
            'main-menu-list'
        );

    if (!container) {
        return;
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
                appState.favorites
                    .includes(item.id);


            return `

                <div class="menu-card">

                    <img
                        src="${item.img}"
                        alt="${item.name}"
                    >

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

                            <span class="rating">

                                <i class="fa-solid fa-star"></i>

                                ${item.rating}

                            </span>

                        </div>

                    </div>

                    <div class="card-actions">

                        <button
                            class="btn-fav ${
                                isFav
                                    ? 'active'
                                    : ''
                            }"
                            onclick="toggleFavorite(${item.id})"
                        >

                            <i
                                class="fa-${
                                    isFav
                                        ? 'solid'
                                        : 'regular'
                                } fa-heart"
                            ></i>

                        </button>

                        <button
                            class="add-btn"
                            onclick="addToCart(${item.id})"
                        >

                            <i class="fa-solid fa-plus"></i>

                        </button>

                    </div>

                </div>

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

            menu.name
                .toLowerCase()
                .includes(query)

            ||

            menu.desc
                .toLowerCase()
                .includes(query)

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

    document
        .querySelectorAll(
            '.category-item'
        )
        .forEach(item => {

            item.classList.remove(
                'active'
            );

        });


    if (element) {

        element.classList.add(
            'active'
        );

    }


    if (category === 'all') {

        renderMenu(
            appState.menu
        );

        return;
    }


    renderMenu(

        appState.menu.filter(
            item =>
                item.category ===
                normalizeCategory(category)
        )

    );

}


/* =========================================================
   ADD TO CART
   ========================================================= */

function addToCart(menuId) {

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


    const existing =

        appState.cart.find(
            cartItem =>
                Number(cartItem.id) ===
                Number(menuId)
        );


    if (existing) {

        existing.qty += 1;

    }

    else {

        appState.cart.push({

            ...item,

            qty: 1

        });

    }


    saveCustomerState();

    updateCartUI();

    showToast(
        `${item.name} ditambahkan ke keranjang`
    );

}


/* =========================================================
   CHANGE QUANTITY
   ========================================================= */

function changeQty(
    menuId,
    delta
) {

    const item =

        appState.cart.find(
            cartItem =>
                Number(cartItem.id) ===
                Number(menuId)
        );


    if (!item) {
        return;
    }


    item.qty += delta;


    if (item.qty <= 0) {

        appState.cart =

            appState.cart.filter(
                cartItem =>
                    Number(cartItem.id) !==
                    Number(menuId)
            );

    }


    saveCustomerState();

    updateCartUI();

}


/* =========================================================
   UPDATE CART
   ========================================================= */

function updateCartUI() {

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
                            -1
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
                            1
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

    const items =

        appState.cart.map(item => ({

            product_id:
                Number(item.id),

            quantity:
                Number(item.qty)

        }));


    /* =====================================================
       TABLE
       ===================================================== */

    const tableInput =
        document.getElementById(
            'table-number'
        );


    /* =====================================================
       PAYLOAD
       ===================================================== */

    const payload = {

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

                        'X-CSRF-TOKEN':
                            csrfToken

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


        /* =================================================
           BERHASIL
           ================================================= */

        appState.cart = [];


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
                '/customer/orders';

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

function renderHistory() {

    const container =
        document.getElementById(
            'history-container'
        );


    if (!container) {
        return;
    }


    if (!appState.history.length) {

        container.innerHTML = `

            <div class="empty-state">

                <i class="fa-solid fa-receipt"></i>

                <p>
                    Belum ada riwayat pesanan
                </p>

            </div>

        `;

        return;

    }


    container.innerHTML =

        appState.history.map(
            history => `

                <div class="history-card">

                    <div class="history-header">

                        <span>
                            ${history.id}
                            •
                            ${history.date}
                        </span>

                        <span class="history-status">
                            Selesai
                        </span>

                    </div>

                    <div class="history-title">

                        ${history.items}

                    </div>

                    <div class="history-price">

                        ${rupiah(
                            history.total
                        )}

                    </div>

                </div>

            `
        ).join('');

}


/* =========================================================
   FAVORITE
   ========================================================= */

function toggleFavorite(id) {

    const index =

        appState.favorites.indexOf(
            Number(id)
        );


    if (index >= 0) {

        appState.favorites.splice(
            index,
            1
        );

    }

    else {

        appState.favorites.push(
            Number(id)
        );

    }


    saveCustomerState();


    renderMenu(
        appState.menu
    );


    renderFavorites();

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

                        ${item.rating}

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

        renderMenu(
            appState.menu
        );

        renderFavorites();

        updateCartUI();

        renderHistory();

        initPaymentUI();

    }
);