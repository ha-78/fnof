const DAY_MS = 86400000;

function pad(value) {
    return String(value).padStart(2, '0');
}

function clock(date) {
    return `${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function absolute(date) {
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${clock(date)}`;
}

function daysBeforeToday(date) {
    const start = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

    return Math.round((today.getTime() - start.getTime()) / DAY_MS);
}

function labels() {
    const root = document.documentElement;

    return {
        today: root.dataset.postedToday ?? '',
        yesterday: root.dataset.postedYesterday ?? '',
        daysAgo: root.dataset.postedDaysAgo ?? '',
    };
}

function relative(date, copy) {
    const days = daysBeforeToday(date);

    if (days === 0) {
        return copy.today;
    }

    if (days === 1) {
        return copy.yesterday;
    }

    if (days > 1) {
        return copy.daysAgo.replaceAll(':days', String(days));
    }

    return null;
}

export function formatPostedTimes(root = document) {
    const copy = labels();
    const canPhrase = copy.today !== '' && copy.yesterday !== '' && copy.daysAgo !== '';

    root.querySelectorAll('time[data-posted]').forEach((time) => {
        const date = new Date(time.dateTime);

        if (Number.isNaN(date.getTime())) {
            return;
        }

        if (time.hasAttribute('data-relative') && canPhrase) {
            const when = relative(date, copy);

            time.textContent = when === null ? absolute(date) : `${when} ${clock(date)}`;

            return;
        }

        time.textContent = absolute(date);
    });
}
