/**
 * QUATTRO COFFEE - KITCHEN
 * JavaScript
 *
 * Data pesanan TIDAK lagi dibuat di JavaScript.
 * Semua data pesanan berasal dari Laravel + Database.
 */

/**
 * Mengubah isi element berdasarkan ID.
 */
function set(id, value) {
    const element = document.getElementById(id);

    if (element) {
        element.textContent = value;
    }
}


/**
 * Refresh Kitchen.
 *
 * Karena data berasal dari database,
 * refresh dilakukan dengan reload halaman.
 */
function refreshKitchen() {
    window.location.reload();
}


/**
 * Search pesanan.
 */
function setupSearch() {

    const searchInput = document.getElementById('kitchen-search');

    if (!searchInput) {
        return;
    }

    searchInput.addEventListener('input', function () {

        const keyword = this.value
            .toLowerCase()
            .trim();

        const cards = document.querySelectorAll(
            '#order-list .order-card, #dashboard-orders [data-searchable]'
        );

        let visibleCount = 0;

        cards.forEach(function (card) {

            const text = card.textContent
                .toLowerCase();

            if (text.includes(keyword)) {

                card.style.display = '';

                visibleCount++;

            } else {

                card.style.display = 'none';

            }

        });

        const counter = document.getElementById('page-count');
        if (counter) counter.textContent = visibleCount + ' pesanan';
    });
}


/**
 * Filter prioritas.
 *
 * Jika kartu tidak mempunyai data-priority,
 * maka dianggap normal.
 */
function setupPriorityFilter() {

    const priorityFilter =
        document.getElementById('priority-filter');

    if (!priorityFilter) {
        return;
    }

    priorityFilter.addEventListener(
        'change',
        function () {

            const selected = this.value;

            const cards = document.querySelectorAll(
                '#order-list .order-card'
            );

            let visibleCount = 0;

            cards.forEach(function (card) {

                const priority =
                    card.dataset.priority || 'normal';

                if (
                    selected === 'all' ||
                    selected === priority
                ) {

                    card.style.display = '';

                    visibleCount++;

                } else {

                    card.style.display = 'none';

                }

            });

            set(
                'page-count',
                visibleCount + ' pesanan'
            );
        }
    );
}


/**
 * Hitung jumlah kartu yang tampil.
 *
 * Digunakan pada halaman:
 * - Pesanan Baru
 * - Sedang Diproses
 * - Siap Diambil
 * - Riwayat
 */
function updatePageCount() {

    const orderList =
        document.getElementById('order-list');

    if (!orderList) {
        return;
    }

    const cards =
        orderList.querySelectorAll('.order-card');

    set(
        'page-count',
        cards.length + ' pesanan'
    );
}


/**
 * Jalankan ketika halaman selesai dimuat.
 */
document.addEventListener(
    'DOMContentLoaded',
    function () {

        updatePageCount();

        setupSearch();

        setupPriorityFilter();

        document.querySelectorAll('.order-timer').forEach(function (timer) {
            const startedAt = timer.dataset.startedAt ? new Date(timer.dataset.startedAt).getTime() : Date.now();
            const label = timer.querySelector('.timer-label');
            const update = function () {
                const elapsed = Math.max(0, Math.floor((Date.now() - startedAt) / 1000));
                const minutes = Math.floor(elapsed / 60);
                const seconds = elapsed % 60;
                label.textContent = 'Waktu proses ' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            };
            update();
            window.setInterval(update, 1000);
        });

    }
);
