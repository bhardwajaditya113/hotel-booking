import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    corePlugins: {
        preflight: false,
    },

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                    DEFAULT: '#0d9488',
                },
            },
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', 'DM Sans', ...defaultTheme.fontFamily.sans],
            },
            letterSpacing: {
                tighter: '-0.045em',
            },
            borderRadius: {
                nx: '14px',
                'nx-xl': '20px',
            },
            boxShadow: {
                nx: '0 10px 40px rgba(15, 23, 42, 0.06)',
                'nx-sm': '0 4px 14px rgba(15, 23, 42, 0.05)',
            },
        },
    },

    plugins: [forms],
};
