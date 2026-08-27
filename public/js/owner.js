
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
        input.addEventListener('input', () => {
            const selector = input.dataset.search;
            const q = input.value.toLowerCase().trim();
            document.querySelectorAll(selector).forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
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
    window.__toastTimer = setTimeout(() => toast.style.display = 'none', 2200);
}
