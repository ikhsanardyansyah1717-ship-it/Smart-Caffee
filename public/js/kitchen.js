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
    window.showQuattroPageTransition?.(window.location.href);
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


let kitchenOrderTimerInterval = null;
function syncKitchenOrderTimers() {
    if (kitchenOrderTimerInterval !== null) window.clearInterval(kitchenOrderTimerInterval);
    kitchenOrderTimerInterval = null;
    const timers = Array.from(document.querySelectorAll('.order-timer')).map(timer => ({
        startedAt: timer.dataset.startedAt ? new Date(timer.dataset.startedAt).getTime() : Date.now(),
        label: timer.querySelector('.timer-label')
    })).filter(timer => timer.label);
    const update = () => {
        const now = Date.now();
        timers.forEach(timer => {
            const elapsed = Math.max(0, Math.floor((now - timer.startedAt) / 1000));
            timer.label.textContent = 'Waktu proses ' + String(Math.floor(elapsed / 60)).padStart(2, '0') + ':' + String(elapsed % 60).padStart(2, '0');
        });
    };
    if (document.visibilityState === 'visible' && timers.length) {
        update();
        kitchenOrderTimerInterval = window.setInterval(update, 1000);
    }
}

let kitchenLiveSyncInFlight = false;
async function refreshKitchenLiveData() {
    const targets = ['order-list', 'dashboard-orders'].map(id => document.getElementById(id)).filter(Boolean);
    if (!targets.length || kitchenLiveSyncInFlight || document.visibilityState !== 'visible') return;
    kitchenLiveSyncInFlight = true;
    try {
        const response = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'ngrok-skip-browser-warning': 'true' },
            credentials: 'same-origin', cache: 'no-store'
        });
        if (!response.ok) return;
        const markup = await response.text();
        const parsed = new DOMParser().parseFromString(markup, 'text/html');
        targets.forEach(target => {
            const fresh = parsed.getElementById(target.id);
            if (fresh) target.innerHTML = fresh.innerHTML;
        });
        document.getElementById('kitchen-search')?.dispatchEvent(new Event('input', { bubbles: true }));
        document.getElementById('priority-filter')?.dispatchEvent(new Event('change', { bubbles: true }));
        updatePageCount();
        syncKitchenOrderTimers();
    } catch (error) {
        console.warn('Pembaruan pesanan Kitchen gagal.', error);
    } finally {
        kitchenLiveSyncInFlight = false;
    }
}
window.addEventListener('quattro:data-changed', event => {
    if (['orders', 'payments'].includes(event.detail?.type)) refreshKitchenLiveData();
});
document.addEventListener('visibilitychange', syncKitchenOrderTimers);
/**
 * Jalankan ketika halaman selesai dimuat.
 */
document.addEventListener(
    'DOMContentLoaded',
    function () {

        updatePageCount();

        setupSearch();

        setupPriorityFilter();

        syncKitchenOrderTimers();


    }
);
