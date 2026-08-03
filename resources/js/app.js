const initializeDashboardInteractions = () => {
    const drawer = document.querySelector('[data-drawer]');
    const drawerOverlay = document.querySelector('[data-drawer-overlay]');
    const drawerOpenButton = document.querySelector('[data-drawer-open]');
    const drawerCloseButtons = document.querySelectorAll('[data-drawer-close], [data-drawer-link]');
    const flashDismissButtons = document.querySelectorAll('[data-flash-dismiss]');

    if (drawer && drawerOverlay && drawerOpenButton) {
        const openDrawer = () => {
            drawer.classList.remove('-translate-x-full');
            drawerOverlay.classList.remove('hidden');
            drawerOpenButton.setAttribute('aria-expanded', 'true');
            document.body.classList.add('overflow-hidden');

            drawer.querySelector('[data-drawer-close]')?.focus();
        };

        const closeDrawer = () => {
            drawer.classList.add('-translate-x-full');
            drawerOverlay.classList.add('hidden');
            drawerOpenButton.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden');
        };

        drawerOpenButton.addEventListener('click', openDrawer);
        drawerOverlay.addEventListener('click', closeDrawer);
        drawerCloseButtons.forEach((button) => button.addEventListener('click', closeDrawer));

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && drawerOpenButton.getAttribute('aria-expanded') === 'true') {
                closeDrawer();
                drawerOpenButton.focus();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeDrawer();
            }
        });
    }

    flashDismissButtons.forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-flash-message]')?.remove();
        });
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeDashboardInteractions);
} else {
    initializeDashboardInteractions();
}
