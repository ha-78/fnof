<script>
    (function () {
        var key = 'fnof_theme';

        function systemTheme() {
            try {
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            } catch (e) {
                return 'light';
            }
        }

        function resolveTheme(pref) {
            if (pref === 'colorful') {
                try { localStorage.setItem(key, 'light'); } catch (e) {}
                return 'light';
            }

            if (pref === 'light' || pref === 'dark') {
                return pref;
            }

            return systemTheme();
        }

        @if (! empty($deviceColorTheme ?? null))
            var stored = @json($deviceColorTheme);
            try { localStorage.setItem(key, stored); } catch (e) {}
            document.documentElement.dataset.theme = resolveTheme(stored);
        @else
            try {
                document.documentElement.dataset.theme = resolveTheme(localStorage.getItem(key));
            } catch (e) {
                document.documentElement.dataset.theme = systemTheme();
            }
        @endif
    })();
</script>
