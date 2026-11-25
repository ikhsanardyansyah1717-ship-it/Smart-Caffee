(() => {
    const loader = document.getElementById('quattro-page-loader');
    if (!loader) return;

    let pendingNavigation = false;
    let showTimer = null;

    const revealLoader = () => {
        if (showTimer !== null) window.clearTimeout(showTimer);
        loader.classList.add('is-visible');
        loader.setAttribute('aria-hidden', 'false');
    };

    const clearLoader = () => {
        if (showTimer !== null) window.clearTimeout(showTimer);
        showTimer = null;
        pendingNavigation = false;
        loader.classList.remove('is-visible');
        loader.setAttribute('aria-hidden', 'true');
    };

    window.showQuattroPageTransition = destination => {
        if (destination) {
            try {
                if (new URL(destination, window.location.href).origin !== window.location.origin) return;
            } catch (_) {
                return;
            }
        }
        pendingNavigation = true;
        showTimer = window.setTimeout(revealLoader, 70);
    };

    const eligibleUrl = href => {
        try {
            const url = new URL(href, window.location.href);
            return url.origin === window.location.origin && url.href !== window.location.href;
        } catch (_) {
            return false;
        }
    };

    document.addEventListener('click', event => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        const link = event.target.closest('a[href]');
        if (!link || link.target === '_blank' || link.hasAttribute('download') || link.dataset.noPageTransition !== undefined) return;
        if (!eligibleUrl(link.href)) return;

        pendingNavigation = true;
        window.setTimeout(() => {
            if (event.defaultPrevented) {
                pendingNavigation = false;
                return;
            }
            showTimer = window.setTimeout(revealLoader, 70);
        }, 0);
    });

    document.addEventListener('submit', event => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.noPageTransition !== undefined) return;
        const action = form.action || window.location.href;
        if (!eligibleUrl(action)) return;

        pendingNavigation = true;
        window.setTimeout(() => {
            if (event.defaultPrevented) {
                pendingNavigation = false;
                return;
            }
            showTimer = window.setTimeout(revealLoader, 70);
        }, 0);
    }, true);

    window.addEventListener('beforeunload', () => {
        if (pendingNavigation) revealLoader();
    });
    window.addEventListener('pageshow', clearLoader);

    const enableMotion = () => {
        document.body.classList.add('quattro-motion-enabled');
        document.querySelector('main, .app-container')?.classList.add('quattro-page-enter');
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enableMotion, { once:true });
    } else {
        enableMotion();
    }
})();