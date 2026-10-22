import Alpine from 'alpinejs';
import { applyTheme, readThemePreference } from './theme.js';

function persistDeviceTheme(theme) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    if (! csrf) {
        return;
    }

    fetch('/device/theme', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrf,
        },
        credentials: 'same-origin',
        body: JSON.stringify({ theme }),
    }).catch(() => {});
}

document.addEventListener('alpine:init', () => {
    Alpine.data('themeToggle', () => ({
        current: readThemePreference(),

        pick(theme) {
            this.current = theme;
            applyTheme(theme);
            persistDeviceTheme(theme);
        },
    }));
});

Alpine.start();
