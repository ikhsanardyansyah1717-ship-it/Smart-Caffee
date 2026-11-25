
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('ownerSidebar');
    const menu = document.getElementById('ownerMenu');
    const refresh = document.getElementById('ownerRefresh');

    menu?.addEventListener('click', () => sidebar?.classList.toggle('open'));

    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 780 && sidebar?.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !menu?.contains(e.target)) sidebar.classList.remove('open');
        }
    });

    refresh?.addEventListener('click', () => {
        refresh.classList.add('spin');
        setTimeout(() => refresh.classList.remove('spin'), 700);
        showToast('Data dashboard diperbarui');
    });

    document.querySelectorAll('[data-search]').forEach(input => {
        const applySearch = () => {
            const selector = input.dataset.search;
            const q = input.value.toLowerCase().trim();
            const items = [...document.querySelectorAll(selector)];
            let visibleCount = 0;
            items.forEach(item => {
                const matches = item.textContent.toLowerCase().includes(q);
                item.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });

            const emptyState = input.dataset.empty ? document.querySelector(input.dataset.empty) : null;
            if (emptyState) emptyState.hidden = q.length === 0 || visibleCount > 0;

            const noDataState = input.dataset.noData ? document.querySelector(input.dataset.noData) : null;
            if (noDataState) noDataState.hidden = q.length > 0;
        };

        input.addEventListener('input', applySearch);
        if (input.value.trim()) applySearch();
    });

    document.querySelectorAll('[data-demo]').forEach(btn => {
        btn.addEventListener('click', () => showToast(btn.dataset.demo));
    });
});

function showToast(message){
    let toast = document.querySelector('.toast');
    if(!toast){
        toast = document.createElement('div');
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.style.display = 'block';
    clearTimeout(window.__toastTimer);
    window.__toastTimer = setTimeout(() => toast.style.display = 'none', 3000);
}


let ownerLiveSyncInFlight = false;
async function refreshOwnerLiveSections(type) {
    if (ownerLiveSyncInFlight || document.visibilityState !== 'visible') return;
    const selectors = ['.stats', '.owner-transaction-table tbody', '.cards', '#employeeRows', '#customerRows'];
    const targets = selectors.map(selector => document.querySelector(selector)).filter(Boolean);
    ownerLiveSyncInFlight = true;
    try {
        const response = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'ngrok-skip-browser-warning': 'true' },
            credentials: 'same-origin', cache: 'no-store'
        });
        if (!response.ok) return;
        const parsed = new DOMParser().parseFromString(await response.text(), 'text/html');
        targets.forEach(target => {
            const selector = selectors.find(item => document.querySelector(item) === target);
            const fresh = selector ? parsed.querySelector(selector) : null;
            if (fresh) target.innerHTML = fresh.innerHTML;
        });
        const currentNav = document.querySelector('.owner-nav');
        const freshNav = parsed.querySelector('.owner-nav');
        if (currentNav && freshNav) currentNav.innerHTML = freshNav.innerHTML;
        if (type === 'owner' && window.showAppNotification) window.showAppNotification('Ada pembaruan data untuk Owner.');
    } catch (error) {
        console.warn('Pembaruan data Owner gagal.', error);
    } finally {
        ownerLiveSyncInFlight = false;
    }
}
window.addEventListener('quattro:data-changed', event => {
    if (['orders', 'payments', 'products', 'owner'].includes(event.detail?.type)) refreshOwnerLiveSections(event.detail.type);
});