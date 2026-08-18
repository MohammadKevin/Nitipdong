import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cyan: {
                    50:  '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#06b6d4',
                    600: '#0891b2',
                    700: '#0e7490',
                    800: '#155e75',
                    900: '#164e63',
                    950: '#083344',
                },
                brand: {
                    50:  '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#0891b2',
                    600: '#0e7490',
                    700: '#155e75',
                    800: '#164e63',
                    900: '#083344',
                },
                slate: {
                    50:  '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                }
            },
            borderRadius: {
                'xs': '4px',
                'sm': '6px',
                'md': '8px',
                'lg': '10px',
                'xl': '12px',
                '2xl': '16px',
            },
            boxShadow: {
                'xs':   '0 1px 2px 0 rgba(15, 23, 42, 0.04)',
                'sm':   '0 1px 3px 0 rgba(15, 23, 42, 0.05)',
                'card': '0 1px 3px 0 rgba(15, 23, 42, 0.05)',
                'card-hover': '0 6px 16px -4px rgba(15, 23, 42, 0.08), 0 2px 4px -2px rgba(15, 23, 42, 0.04)',
                'dropdown': '0 10px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.05)',
            },
            keyframes: {
                'fade-up': {
                    '0%':   { opacity: '0', transform: 'translateY(6px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'fade-up': 'fade-up 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards',
            },
        },
    },

    plugins: [forms],
};
