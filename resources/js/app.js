import './bootstrap';

document.querySelectorAll('[data-password-toggle]').forEach(button => {
    const input = document.getElementById(button.dataset.passwordToggle);
    const originalLabel = button.getAttribute('aria-label');
    button.addEventListener('click', () => {
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        button.textContent = visible ? 'Sorok' : 'Papar';
        button.setAttribute('aria-pressed', String(visible));
        button.setAttribute('aria-label', visible ? originalLabel.replace('Papar', 'Sorok') : originalLabel);
    });
});

const homePage = document.querySelector('[data-home-page]');
if (homePage && 'IntersectionObserver' in window) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const activeAnimations = new Set();
    const targets = homePage.querySelectorAll('[data-aos], .home-section-heading > *, .home-feature-grid > article, .home-faq-intro, .home-faq-list details, .home-stories-layout > div, .home-contact-layout > div');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            observer.unobserve(entry.target);
            if (reducedMotion.matches || !entry.target.animate) return;
            const animation = entry.target.animate([
                { opacity: 0, translate: '0 22px' },
                { opacity: 1, translate: '0 0' },
            ], {
                duration: 650,
                delay: Math.min(Number(entry.target.dataset.aosDelay) || 0, 180),
                easing: 'cubic-bezier(.2,.7,.2,1)',
                fill: 'backwards',
            });
            activeAnimations.add(animation);
            animation.finished.catch(() => {}).finally(() => activeAnimations.delete(animation));
        });
    }, { threshold: 0.08 });
    targets.forEach(target => observer.observe(target));
    reducedMotion.addEventListener('change', () => {
        if (reducedMotion.matches) activeAnimations.forEach(animation => animation.cancel());
    });
    homePage.addEventListener('focusin', () => activeAnimations.forEach(animation => animation.finish()));
}

const promotionDialog = document.querySelector('[data-promotion-dialog]');
if (promotionDialog) {
    const container = promotionDialog.closest('[data-promotion-container]');
    const expiresAt = Date.parse(promotionDialog.dataset.expiresAt);
    const isExpired = () => Date.now() >= expiresAt;
    const expire = () => {
        if (isExpired()) {
            if (promotionDialog.open) promotionDialog.close();
            container.hidden = true;
            return;
        }
        window.setTimeout(expire, Math.min(expiresAt - Date.now(), 2147483647));
    };
    const open = () => {
        if (isExpired()) { expire(); return; }
        if (!promotionDialog.open) promotionDialog.showModal();
    };
    promotionDialog.querySelectorAll('[data-promotion-close]').forEach(button => button.addEventListener('click', () => promotionDialog.close()));
    open();
    expire();
    window.addEventListener('pageshow', open);
    document.addEventListener('visibilitychange', () => { if (isExpired()) expire(); });
}

const posterUpload = document.querySelector('[data-poster-upload]');
if (posterUpload) {
    let previewUrl;
    posterUpload.addEventListener('change', () => {
        const file = posterUpload.files[0];
        if (!file || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) return;
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = URL.createObjectURL(file);
        const preview = document.querySelector('[data-poster-preview]');
        preview.src = previewUrl;
        preview.hidden = false;
    });
}

const managerMenu = document.querySelector('[data-manager-menu]');
if (managerMenu) {
    const desktop = window.matchMedia('(min-width: 1024px)');
    const syncManagerMenu = () => { managerMenu.open = desktop.matches; };
    syncManagerMenu();
    desktop.addEventListener('change', syncManagerMenu);
    managerMenu.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !desktop.matches && managerMenu.open) {
            managerMenu.open = false;
            managerMenu.querySelector('summary').focus();
        }
    });
}
import Swal from 'sweetalert2';

// Jadikan Swal boleh diakses secara global
window.Swal = Swal;


document.querySelectorAll('[data-copy-target]').forEach(button => {
    button.addEventListener('click', async () => {
        const field = document.getElementById(button.dataset.copyTarget);
        if (!field) return;
        try {
            await navigator.clipboard.writeText(field.value);
        } catch {
            field.select();
            document.execCommand('copy');
        }
        const status = document.querySelector('[data-copy-status]');
        if (status) status.textContent = button.dataset.copyTarget === 'whatsapp-message' ? 'Mesej telah disalin.' : 'Pautan telah disalin.';
    });
});
