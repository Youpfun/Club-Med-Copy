import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                // Charte graphique Club Med
                'clubmed': {
                    'beige': '#f5f2ec',
                    'black': '#000000',
                    'gold': '#ffc72c',
                    'white': '#ffffff',
                    'blue': '#005aab',
                    'blue-dark': '#003d73',
                    'blue-light': '#0073cf',
                },
            },
            fontFamily: {
                // Polices Club Med
                'serif': ['Newsreader', ...defaultTheme.fontFamily.serif],
                'sans': ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
