const initializeDashboardInteractions = () => {
    const drawer = document.querySelector('[data-drawer]');
    const drawerOverlay = document.querySelector('[data-drawer-overlay]');
    const drawerOpenButton = document.querySelector('[data-drawer-open]');
    const appContent = document.querySelector('[data-app-content]');
    const drawerCloseButtons = document.querySelectorAll('[data-drawer-close], [data-drawer-link]');
    const flashDismissButtons = document.querySelectorAll('[data-flash-dismiss]');
    const deleteForms = document.querySelectorAll('[data-confirm-delete]');

    if (drawer && drawerOverlay && drawerOpenButton) {
        const updateDrawerAccessibility = (isOpen) => {
            const isDesktop = window.innerWidth >= 1024;

            if (isDesktop || isOpen) {
                drawer.removeAttribute('aria-hidden');
                drawer.removeAttribute('inert');
            } else {
                drawer.setAttribute('aria-hidden', 'true');
                drawer.setAttribute('inert', '');
            }

            if (isOpen && !isDesktop) {
                appContent?.setAttribute('inert', '');
            } else {
                appContent?.removeAttribute('inert');
            }
        };

        const openDrawer = () => {
            drawer.classList.remove('-translate-x-full');
            drawerOverlay.classList.remove('hidden');
            drawerOpenButton.setAttribute('aria-expanded', 'true');
            document.body.classList.add('overflow-hidden');
            updateDrawerAccessibility(true);

            drawer.querySelector('[data-drawer-close]')?.focus();
        };

        const closeDrawer = (restoreFocus = false) => {
            drawer.classList.add('-translate-x-full');
            drawerOverlay.classList.add('hidden');
            drawerOpenButton.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden');
            updateDrawerAccessibility(false);

            if (restoreFocus) {
                window.requestAnimationFrame(() => drawerOpenButton.focus());
            }
        };

        drawerOpenButton.addEventListener('click', openDrawer);
        drawerOverlay.addEventListener('click', () => closeDrawer(true));
        drawerCloseButtons.forEach((button) =>
            button.addEventListener('click', () => closeDrawer(!button.hasAttribute('data-drawer-link'))),
        );

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && drawerOpenButton.getAttribute('aria-expanded') === 'true') {
                closeDrawer(true);
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeDrawer(false);
            } else {
                updateDrawerAccessibility(drawerOpenButton.getAttribute('aria-expanded') === 'true');
            }
        });

        updateDrawerAccessibility(false);
    }

    flashDismissButtons.forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-flash-message]')?.remove();
        });
    });

    deleteForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const taskTitle = form.dataset.taskTitle ?? 'ini';
            const confirmed = window.confirm(
                `Hapus tugas "${taskTitle}"? Tugas dan pengumpulan terkait akan dihapus permanen.`,
            );

            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeDashboardInteractions);
} else {
    initializeDashboardInteractions();
}
