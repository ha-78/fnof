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
    Alpine.data('liveRegion', () => ({
        archived: false,
        loading: false,
        timer: null,
        version: null,

        init() {
            const parsed = Number(this.$el.dataset.version);

            this.archived = this.$el.dataset.archived === '1';
            this.version = Number.isFinite(parsed) ? parsed : null;
            this.timer = window.setInterval(() => this.refresh(), 4000);
        },

        destroy() {
            window.clearInterval(this.timer);
        },

        signedOut(response) {
            return response.type === 'opaqueredirect' || response.status === 401 || response.status === 419;
        },

        async refresh() {
            const region = this.$refs.region;

            if (document.hidden || this.loading || ! region || region.contains(document.activeElement)) {
                return;
            }

            this.loading = true;

            try {
                const version = await this.ping();

                if (version === null || version === this.version) {
                    return;
                }

                const response = await fetch(window.location.href, {
                    headers: {
                        Accept: 'text/html',
                        'X-Fragment': this.$el.dataset.fragment,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    redirect: 'manual',
                });

                if (this.signedOut(response)) {
                    window.location.reload();

                    return;
                }

                if (response.status === 404) {
                    if (this.$el.dataset.missing) {
                        window.location.assign(this.$el.dataset.missing);
                    }

                    return;
                }

                if (! response.ok) {
                    return;
                }

                region.innerHTML = await response.text();

                const state = region.querySelector('[data-archived]');

                if (state) {
                    this.archived = state.dataset.archived === '1';
                }

                this.version = version;
            } catch {
                // A missed poll can wait for the next interval.
            } finally {
                this.loading = false;
            }
        },

        async ping() {
            const response = await fetch(this.$el.dataset.versionUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                redirect: 'manual',
            });

            if (this.signedOut(response)) {
                window.location.reload();

                return null;
            }

            if (! response.ok) {
                return null;
            }

            const payload = await response.json();
            const version = Number(payload.version);

            return Number.isFinite(version) ? version : null;
        },
    }));

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
