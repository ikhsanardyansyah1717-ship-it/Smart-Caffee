(function () {
    const duration = 3000;
    const timers = new WeakMap();
    const noticeSelector = '.alert, [role="alert"], [class*="-alert"], .toast.success, .transfer-feedback';

    function dismissAfterThreeSeconds(element) {
        if (!element || timers.has(element)) return;
        timers.set(element, window.setTimeout(() => {
            element.style.transition = 'opacity 180ms ease, transform 180ms ease, margin 180ms ease, padding 180ms ease';
            element.style.opacity = '0';
            element.style.transform = 'translateY(-4px)';
            window.setTimeout(() => element.remove(), 200);
        }, duration));
    }

    function scanNotices(root) {
        if (root.nodeType !== Node.ELEMENT_NODE && root.nodeType !== Node.DOCUMENT_NODE) return;
        if (root.matches?.(noticeSelector)) dismissAfterThreeSeconds(root);
        root.querySelectorAll?.(noticeSelector).forEach(dismissAfterThreeSeconds);
    }

    window.showAppNotification = function (message, type = 'info') {
        let element = document.getElementById('app-notification');
        if (!element) {
            element = document.createElement('div');
            element.id = 'app-notification';
            element.setAttribute('role', 'status');
            element.setAttribute('aria-live', 'polite');
            Object.assign(element.style, {
                position: 'fixed', top: '20px', right: '22px', zIndex: '10000',
                maxWidth: 'min(420px, calc(100vw - 32px))', padding: '13px 17px',
                border: '1px solid #dcebdd', borderRadius: '12px', background: '#edf7f0',
                color: '#28754a', boxShadow: '0 12px 30px rgba(43, 35, 26, .14)',
                font: '600 13px/1.45 system-ui, sans-serif', opacity: '0', transition: 'opacity 180ms ease'
            });
            document.body.appendChild(element);
        }
        if (timers.has(element)) window.clearTimeout(timers.get(element));
        timers.delete(element);
        element.textContent = message;
        element.style.background = type === 'error' ? '#fff1ef' : '#edf7f0';
        element.style.color = type === 'error' ? '#a5372c' : '#28754a';
        element.style.opacity = '1';
        dismissAfterThreeSeconds(element);
    };

    scanNotices(document);
    new MutationObserver(records => records.forEach(record => record.addedNodes.forEach(scanNotices)))
        .observe(document.documentElement, { childList: true, subtree: true });
})();

