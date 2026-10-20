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
    if (!selectedTotal) return alert('Pilih pesanan terlebih dahulu.');
    const received = Number(document.getElementById('cashInput').value || 0);
    const method = document.getElementById('paymentMethod').value;
    if (method === 'Cash' && received < selectedTotal) {
        return alert('Uang diterima belum mencukupi.');
    }
    alert('Pembayaran berhasil diproses.');
}
