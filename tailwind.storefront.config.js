import forms from '@tailwindcss/forms';

/*
| Configuración de Tailwind EXCLUSIVA del storefront público (SEXLANDIA).
| Reemplaza al antiguo CDN de Tailwind (cdn.tailwindcss.com) que se cargaba en
| runtime dentro de `storefront/layout.blade.php`. El tema replica exactamente
| el `tailwind.config` que vivía en línea en ese layout (Montserrat como sans,
| Cormorant Garamond como serif, paleta vino/crema y las 3 animaciones).
|
| El panel admin sigue usando `tailwind.config.js` a través de
| `resources/css/app.css`; este archivo solo lo consume `resources/css/storefront.css`
| mediante la directiva `@config`.
*/

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/storefront/**/*.blade.php',
        './resources/views/components/sl/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                cream: '#f3eee8',
                wine: '#8d263d',
                'wine-dark': '#5c1424',
                chocolate: '#2b1c18',
                blush: '#d7aaa6',
                powder: '#e7d1ce',
                butter: '#e8d5a6',
                ink: '#171515',
                bone: '#fbf8f3',
                /* remapeo: las vistas de cliente heredadas usan la escala "indigo" → vino SEXLANDIA */
                indigo: {
                    50: '#f7edef', 100: '#eed7dc', 200: '#e0b8c0', 300: '#cd8f9c',
                    400: '#b25f72', 500: '#9c3a4f', 600: '#8d263d',
                    700: '#5c1424', 800: '#4a1620', 900: '#3a141b', 950: '#250b11',
                },
            },
            fontFamily: {
                sans: ['"Montserrat"', 'sans-serif'],
                serif: ['"Cormorant Garamond"', 'serif'],
            },
            animation: {
                'fade-in': 'fadeIn 1.5s cubic-bezier(0.4, 0, 0.2, 1) forwards',
                'slide-up-fade': 'slideUpFade 1.2s cubic-bezier(0.4, 0, 0.2, 1) forwards',
                'breathe': 'breathe 4s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUpFade: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                breathe: {
                    '0%, 100%': { transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(225, 29, 72, 0)' },
                    '50%': { transform: 'scale(1.03)', boxShadow: '0 0 25px 5px rgba(225, 29, 72, 0.3)' },
                },
            },
        },
    },

    plugins: [forms],
};
