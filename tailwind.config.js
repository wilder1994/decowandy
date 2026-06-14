import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                dw: {
                    primary: '#A98AD4',
                    'primary-dark': 'var(--dw-primary-dark)',
                    lilac: '#C4A1E0',
                    'lilac-soft': 'var(--dw-lilac-soft)',
                    rose: '#F8A3C9',
                    yellow: '#F7D87B',
                    bg: 'var(--dw-bg)',
                    card: 'var(--dw-card)',
                    text: 'var(--dw-text)',
                    muted: 'var(--dw-muted)',
                    border: 'var(--dw-border)',
                    'border-neon': 'var(--dw-border-neon)',
                    success: '#4ADE80',
                    danger: '#F472B6',
                },
            },
            borderWidth: {
                hairline: '0.5px',
            },
            borderRadius: {
                dw: '0.75rem',
                'dw-lg': '1rem',
            },
            boxShadow: {
                'dw-neon': '0 0 0 0.5px rgba(169, 138, 212, 0.45), 0 0 14px rgba(169, 138, 212, 0.12)',
                'dw-neon-sm': '0 0 0 0.5px rgba(169, 138, 212, 0.35), 0 0 8px rgba(169, 138, 212, 0.08)',
                'dw-card': '0 1px 2px rgba(45, 42, 50, 0.04)',
            },
        },
    },

    plugins: [forms],
};
