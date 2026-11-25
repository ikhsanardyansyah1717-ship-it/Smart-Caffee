function openModal() {
    const modal = document.getElementById('orderModal');
    if (modal) modal.classList.add('show');
}
function closeModal() {
    const modal = document.getElementById('orderModal');
    if (modal) modal.classList.remove('show');
}
window.addEventListener('click', e => {
    const modal = document.getElementById('orderModal');
    if (modal && e.target === modal) closeModal();
});

function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;
    const term = input.value.toLowerCase();
    [...table.tBodies[0].rows].forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
    });
}

let selectedTotal = 0;
function selectPayment(button, id, total) {
    document.querySelectorAll('.payment-order').forEach(el => el.classList.remove('selected'));
    button.classList.add('selected');
    selectedTotal = total;
    document.getElementById('selectedText').textContent = 'Pesanan ' + id + ' dipilih.';
    document.getElementById('paymentTotal').textContent = rupiah(total);
    calculateChange();
}
function calculateChange() {
    const input = document.getElementById('cashInput');
    if (!input) return;
    const received = Number(input.value || 0);
    const change = Math.max(0, received - selectedTotal);
    document.getElementById('changeAmount').textContent = rupiah(change);
}
document.addEventListener('input', e => {
    if (e.target && e.target.id === 'cashInput') calculateChange();
});
function rupiah(number) {
    return 'Rp ' + Number(number).toLocaleString('id-ID');
}
function processPayment() {
    if (!selectedTotal) return window.showAppNotification('Pilih pesanan terlebih dahulu.', 'error');
    const received = Number(document.getElementById('cashInput').value || 0);
    const method = document.getElementById('paymentMethod').value;
    if (method === 'Cash' && received < selectedTotal) {
        return window.showAppNotification('Uang diterima belum mencukupi.', 'error');
    }
    window.showAppNotification('Pembayaran berhasil diproses.', 'success');
}


let kasirLiveSyncInFlight = false;
async function refreshKasirLiveSections() {
    const selectors = ['.stats-grid', '.sales-highlight', '.mini-list', '#cashierDashboardOrders tbody', '#cashierDashboardProducts tbody', '#ordersTable tbody'];
    const targets = selectors.map(selector => document.querySelector(selector)).filter(Boolean);
    if (!targets.length || kasirLiveSyncInFlight || document.visibilityState !== 'visible') return;
    kasirLiveSyncInFlight = true;
    try {
        const response = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'ngrok-skip-browser-warning': 'true' },
            credentials: 'same-origin', cache: 'no-store'
        });
        if (!response.ok) return;
        const parsed = new DOMParser().parseFromString(await response.text(), 'text/html');
        targets.forEach(target => {
            const fresh = parsed.querySelector(selectors.find(selector => document.querySelector(selector) === target));
            if (fresh) target.innerHTML = fresh.innerHTML;
        });
        const dashboardSearch = document.getElementById('cashierDashboardSearch');
        if (dashboardSearch) dashboardSearch.dispatchEvent(new Event('input', { bubbles: true }));
        const orderSearch = document.getElementById('orderSearch');
        if (orderSearch) filterTable('orderSearch', 'ordersTable');
    } catch (error) {
        console.warn('Pembaruan data Kasir gagal.', error);
    } finally {
        kasirLiveSyncInFlight = false;
    }
}
window.addEventListener('quattro:data-changed', event => {
    if (['orders', 'payments', 'products'].includes(event.detail?.type)) refreshKasirLiveSections();
});