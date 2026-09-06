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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
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
                    '0%, 100%': { 
                        transform: 'scale(1)', 
                        boxShadow: '0 0 0 0 rgba(225, 29, 72, 0)' 
                    },
                    '50%': { 
                        transform: 'scale(1.03)', 
                        boxShadow: '0 0 25px 5px rgba(225, 29, 72, 0.3)' 
                    },
                }
            }
        },
    },

    plugins: [forms],
};
