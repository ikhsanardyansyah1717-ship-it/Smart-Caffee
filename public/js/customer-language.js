(function () {
    const languageKey = 'quattroLanguage';
    const translations = {
        'Profil': 'Profile',
        'Bahasa': 'Language',
        'Riwayat Pesanan': 'Order History',
        'History Pesanan': 'Order History',
        'Riwayat Pesanan - Quattro Coffee': 'Order History - Quattro Coffee',
        'Profil - Quattro Coffee': 'Profile - Quattro Coffee',
        'Favorit - Quattro Coffee': 'My Favorites - Quattro Coffee',
        'Quattro Coffee - Beranda': 'Quattro Coffee - Home',
        'Favorit': 'Favorites',
        'Kebijakan Privasi': 'Privacy Policy',
        'Masuk': 'Sign In',
        'Masuk sebagai Tamu': 'Log In as Guest',
        'Keluar': 'Log Out',
        'Bahasa aplikasi saat ini: Indonesia.': 'Current app language: English.',
        'Tutup': 'Close',
        'Informasi pesanan digunakan untuk memproses dan menampilkan pesanan Quattro Coffee. Favorit tamu disimpan pada sesi browser.': 'Order information is used to process and display Quattro Coffee orders. Guest favorites are saved in this browser session.',
        'Kebijakan ini menjelaskan cara Quattro Coffee menggunakan informasi saat Anda memakai layanan kami.': 'This policy explains how Quattro Coffee uses information when you use our services.',
        'Informasi yang digunakan': 'Information We Use',
        'Kami menggunakan nama dan informasi akun yang Anda berikan, detail pesanan, serta informasi yang diperlukan untuk pembayaran. Untuk pengunjung, daftar favorit dapat disimpan sementara di browser Anda.': 'We use the name and account information you provide, order details, and information needed for payment. For guests, favorites may be stored temporarily in your browser.',
        'Penggunaan informasi': 'How We Use Information',
        'Informasi digunakan untuk menerima dan menyiapkan pesanan, memproses pembayaran, menampilkan riwayat pesanan, menyimpan preferensi, dan membantu meningkatkan layanan kafe.': 'Information is used to receive and prepare orders, process payments, show order history, save preferences, and help improve our café services.',
        'Penyimpanan dan keamanan': 'Storage and Security',
        'Quattro Coffee menjaga informasi dengan langkah yang wajar dan hanya menyimpannya selama diperlukan untuk layanan, operasional, atau kewajiban yang berlaku.': 'Quattro Coffee uses reasonable measures to protect information and keeps it only as long as needed for services, operations, or applicable obligations.',
        'Pembagian informasi': 'Sharing Information',
        'Kami tidak menjual informasi pribadi Anda. Informasi hanya dapat dibagikan kepada penyedia layanan yang membantu operasional atau pembayaran, atau jika diwajibkan oleh hukum.': 'We do not sell your personal information. Information may only be shared with service providers that support operations or payments, or when required by law.',
        'Pilihan Anda': 'Your Choices',
        'Anda dapat menghubungi pihak Quattro Coffee untuk meminta bantuan terkait informasi akun Anda. Menghapus data browser dapat menghapus favorit pengunjung yang tersimpan di perangkat tersebut.': 'You can contact Quattro Coffee for help with your account information. Clearing browser data may remove guest favorites saved on that device.',
        'Jika kebijakan ini berubah, versi terbaru akan ditampilkan di halaman ini.': 'If this policy changes, the latest version will be shown on this page.',
        'Kembali ke Beranda': 'Back to Home',
        'Kembali ke Profil': 'Back to Profile',
        'Halo, Sahabat Quattro!': 'Hello, Quattro Friend!',
        'Mau ngopi apa di Quattro Coffee hari ini?': 'What would you like to drink at Quattro Coffee today?',
        'Kategori': 'Categories',
        'Pilih sesuai seleramu': 'Choose what you like',
        'Semua': 'All',
        'Coffee': 'Coffee',
        'Non-Coffee': 'Non-Coffee',
        'Food': 'Food',
        'Menu pilihan': 'Our Menu',
        'Pilihan favorit dari Quattro': 'Quattro favorites',
        'Rekomendasi': 'Recommended',
        'Best Seller pekan ini di Quattro Coffee': "Quattro Coffee's best seller this week",
        'Kopi creamy favorit pelanggan Quattro': 'A creamy coffee loved by Quattro customers',
        'Rasa manis dan creamy untuk hari ini': 'Sweet and creamy for today',
        'Simple, bold, dan cocok untuk kapan saja': 'Simple, bold, and perfect anytime',
        'Cari espresso, latte, croissant...': 'Search espresso, latte, croissant...',
        'Favorit Saya': 'My Favorites',
        'Belum ada menu favorit.': 'You have no favorite items yet.',
        'History Pesanan': 'Order History',
        'RIWAYAT PESANAN': 'ORDER HISTORY',
        'Pesanan Sebelumnya': 'Previous Orders',
        'Semua transaksi akun kamu.': 'All transactions on your account.',
        'Pesanan dari sesi browser ini.': 'Orders from this browser session.',
        'Pesanan': 'Orders',
        'Belum Ada Riwayat': 'No Order History Yet',
        'Pesanan yang sudah kamu buat akan otomatis muncul di sini.': 'Orders you place will appear here.',
        'Total Pembayaran': 'Total Payment',
        'Lanjutkan Checkout': 'Continue to Checkout',
        'Metode Pembayaran': 'Payment Method',
        'Termasuk pajak': 'Tax included',
        'Detail produk tidak tersedia.': 'Product details are unavailable.',
        'Habis': 'Sold Out',
        'Tambah ke favorit': 'Add to favorites',
        'Hapus dari favorit': 'Remove from favorites',
        'Belum ada pesanan.': 'No orders yet.',
        'Keranjang kamu masih kosong.': 'Your cart is empty.',
        'Yuk pilih kopi kesukaanmu!': 'Choose your favorite coffee!',
        'Memuat menu...': 'Loading menu...',
        'Pilihan tersedia': 'items available',
        'Detail Menu': 'Menu Details',
        'Suhu': 'Temperature',
        'Dingin': 'Cold',
        'Panas': 'Hot',
        'Tambah ke Keranjang': 'Add to Cart',
        'Pilih 1': 'Choose 1',
        'Lanjutkan Pembayaran': 'Continue to Payment',
        'Pilih Metode Pembayaran': 'Choose a Payment Method',
        'Pesanan berhasil dibuat': 'Order placed successfully',
        'Gagal memuat riwayat pesanan.': 'Could not load order history.',
        'Silakan pilih metode pembayaran': 'Please choose a payment method',
        'Silakan pilih bank debit': 'Please choose a debit bank',
        'Keranjang masih kosong': 'Your cart is empty',
        'Gagal mengambil riwayat pesanan': 'Could not fetch order history',
        'Pesanan yang sudah dibuat akan muncul di sini.': 'Orders you place will appear here.',
        'Pembayaran:': 'Payment:',
        'Berhasil': 'Successful',
        'Silakan refresh halaman dan coba lagi.': 'Please refresh the page and try again.',
        'Gagal mengubah favorit.': 'Could not update favorites.',
        'Catatan': 'Note',
        'Opsional': 'Optional',
        'Tambahkan catatan untuk pesanan ini (opsional)': 'Add a note for this order (optional)'
    };
    const reverseTranslations = Object.fromEntries(
        Object.entries(translations).map(([indonesian, english]) => [english, indonesian])
    );
    Object.assign(reverseTranslations, {
        'History Pesanan': 'Riwayat Pesanan',
        'Order History': 'Riwayat Pesanan',
        'Order History - Quattro Coffee': 'Riwayat Pesanan - Quattro Coffee',
        'Profile - Quattro Coffee': 'Profil - Quattro Coffee',
        'My Favorites - Quattro Coffee': 'Favorit - Quattro Coffee',
        'Quattro Coffee - Home': 'Quattro Coffee - Beranda'
    });
    const indonesianAliases = {
        'History Pesanan': 'Riwayat Pesanan'
    };
    const originalText = new WeakMap();
    const originalAttributes = new WeakMap();

    function normalize(value) {
        return value.trim().replace(/\s+/g, ' ');
    }

    function getLanguage() {
        try {
            return localStorage.getItem(languageKey) === 'en' ? 'en' : 'id';
        } catch (error) {
            return 'id';
        }
    }

    function translate(value, language) {
        const normalized = normalize(value);
        if (language === 'en') {
            const greeting = normalized.match(/^Halo, (.+)! 👋$/);
            if (greeting) return `Hello, ${greeting[1]}! 👋`;
            return translations[normalized] || normalized;
        }
        return indonesianAliases[normalized] || reverseTranslations[normalized] || normalized;
    }

    function applyLanguage() {
        const language = getLanguage();
        const treeWalker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        let textNode;

        while ((textNode = treeWalker.nextNode())) {
            const parent = textNode.parentElement;
            if (!parent || parent.closest('script, style, [data-no-translate]')) continue;

            if (!originalText.has(textNode)) originalText.set(textNode, textNode.nodeValue);
            const source = originalText.get(textNode);
            const normalized = normalize(source);
            const translated = translate(source, language);
            if (!normalized) continue;

            const leading = source.match(/^\s*/)?.[0] || '';
            const trailing = source.match(/\s*$/)?.[0] || '';
            const nextValue = `${leading}${translated}${trailing}`;
            if (textNode.nodeValue !== nextValue) textNode.nodeValue = nextValue;
        }

        document.documentElement.lang = language;
        document.title = translate(document.title, language);

        document.querySelectorAll('[placeholder], [aria-label], [title]').forEach(element => {
            if (element.closest('[data-no-translate]')) return;
            let saved = originalAttributes.get(element);
            if (!saved) {
                saved = {};
                originalAttributes.set(element, saved);
            }
            ['placeholder', 'aria-label', 'title'].forEach(attribute => {
                if (!element.hasAttribute(attribute)) return;
                if (!(attribute in saved)) saved[attribute] = element.getAttribute(attribute);
                element.setAttribute(attribute, translate(saved[attribute], language));
            });
        });

        document.querySelectorAll('[data-language-option]').forEach(button => {
            const selected = button.dataset.languageOption === language;
            button.classList.toggle('active', selected);
            button.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });

        const status = document.getElementById('language-status');
        if (status) {
            const nextStatus = language === 'en'
                ? 'Current app language: English.'
                : 'Bahasa aplikasi saat ini: Indonesia.';
            if (status.textContent !== nextStatus) status.textContent = nextStatus;
        }
    }

    window.setCustomerLanguage = function (language) {
        if (!['id', 'en'].includes(language)) return;
        try {
            localStorage.setItem(languageKey, language);
        } catch (error) {
            // Keep the selection for the current page when storage is unavailable.
        }
        applyLanguage();
    };

    applyLanguage();

    let translationUpdatePending = false;
    const observer = new MutationObserver(() => {
        if (translationUpdatePending) return;
        translationUpdatePending = true;
        requestAnimationFrame(() => {
            translationUpdatePending = false;
            applyLanguage();
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
})();
