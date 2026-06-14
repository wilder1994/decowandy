export const dwChartTheme = {
    primary: '#A98AD4',
    lilac: '#C4A1E0',
    rose: '#F8A3C9',
    yellow: '#F7D87B',
    success: 'rgba(74, 222, 128, 0.85)',
    danger: 'rgba(244, 114, 182, 0.85)',
    grid: 'rgba(169, 138, 212, 0.12)',
    text: '#6B6573',
    fontFamily: 'Inter, system-ui, sans-serif',
};

export function dwChartDefaults() {
    if (typeof Chart === 'undefined') {
        return;
    }

    Chart.defaults.font.family = dwChartTheme.fontFamily;
    Chart.defaults.font.size = 11;
    Chart.defaults.color = dwChartTheme.text;
    Chart.defaults.borderColor = dwChartTheme.grid;

    if (Chart.defaults.plugins?.legend?.labels) {
        Chart.defaults.plugins.legend.labels.boxWidth = 10;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
    }
}

if (typeof window !== 'undefined') {
    window.dwChartTheme = dwChartTheme;
    window.dwChartDefaults = dwChartDefaults;
}
