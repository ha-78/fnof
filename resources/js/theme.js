const STORAGE_KEY = 'fnof_theme';

const VALID = new Set(['default', 'light', 'dark']);

export function normalizeTheme(value) {
    if (value === 'colorful') {
        return 'light';
    }

    return VALID.has(value) ? value : 'default';
}

export function systemTheme() {
    try {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    } catch {
        return 'light';
    }
}

export function resolveTheme(preference) {
    const pref = normalizeTheme(preference);

    if (pref === 'default') {
        return systemTheme();
    }

    return pref;
}

export function readThemePreference() {
    try {
        return normalizeTheme(localStorage.getItem(STORAGE_KEY));
    } catch {
        return 'default';
    }
}

let systemThemeListenerBound = false;

function bindSystemThemeListener() {
    if (systemThemeListenerBound || typeof window === 'undefined') {
        return;
    }

    systemThemeListenerBound = true;

    try {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (readThemePreference() === 'default') {
                document.documentElement.dataset.theme = systemTheme();
            }
        });
    } catch {
        // Older browsers without matchMedia.
    }
}

export function applyTheme(theme) {
    const preference = normalizeTheme(theme);

    try {
        localStorage.setItem(STORAGE_KEY, preference);
    } catch {
        // Ignore storage failures.
    }

    document.documentElement.dataset.theme = resolveTheme(preference);
    bindSystemThemeListener();
}

if (typeof window !== 'undefined') {
    window.fnofApplyTheme = applyTheme;
    bindSystemThemeListener();
}
