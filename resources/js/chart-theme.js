function isDarkTheme() {
    return document.documentElement.getAttribute('data-theme') === 'dark';
}

export function getDwChartTheme() {
    const dark = isDarkTheme();

    return {
        primary: '#A98AD4',
        lilac: '#C4A1E0',
        rose: '#F8A3C9',
        yellow: '#F7D87B',
        success: 'rgba(74, 222, 128, 0.85)',
        danger: 'rgba(244, 114, 182, 0.85)',
        grid: dark ? 'rgba(185, 154, 224, 0.14)' : 'rgba(169, 138, 212, 0.12)',
        text: dark ? '#A39DAD' : '#6B6573',
        fontFamily: 'Inter, system-ui, sans-serif',
    };
}

export const dwChartTheme = getDwChartTheme();

export function dwChartDefaults() {
    if (typeof Chart === 'undefined') {
        return;
    }

    const theme = getDwChartTheme();

    Chart.defaults.font.family = theme.fontFamily;
    Chart.defaults.font.size = 11;
    Chart.defaults.color = theme.text;
    Chart.defaults.borderColor = theme.grid;

    if (Chart.defaults.plugins?.legend?.labels) {
        Chart.defaults.plugins.legend.labels.boxWidth = 10;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.legend.labels.color = theme.text;
    }

    if (Chart.defaults.scale) {
        Chart.defaults.scale.grid = Chart.defaults.scale.grid || {};
        Chart.defaults.scale.grid.color = theme.grid;
    }
}

if (typeof window !== 'undefined') {
    window.getDwChartTheme = getDwChartTheme;
    window.dwChartTheme = dwChartTheme;
    window.dwChartDefaults = dwChartDefaults;

    window.addEventListener('dw-theme-change', () => {
        dwChartDefaults();
    });
}
