import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/css/**/*.css',
        './modules/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            minWidth: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '100': '28rem',
                '200': '56rem',
                '300': '84rem',
                '400': '112rem',
                '1/5': '20%',
                '1/4': '25%',
                '1/3': '33.3333%',
                '1/2': '50%',
                '2/3': '66.6667%',
                '3/4': '75%',
                '4/5': '80%',
                '9/10': '90%'
            },
            minHeight: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '192': '48rem',
                '384': '96rem',
                '1/5': '20vh',
                '1/4': '25vh',
                '1/3': '33.3333vh',
                '1/2': '50vh',
                '2/3': '67.6666vh',
                '3/4': '75vh',
                '4/5': '80vh',
                '9/10': '90vh',
            },
            maxWidth: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '100': '28rem',
                '200': '56rem',
                '300': '84rem',
                '400': '112rem',
                '1/5': '20%',
                '1/4': '25%',
                '1/3': '33.3333%',
                '1/2': '50%',
                '2/3': '66.6667%',
                '3/4': '75%',
                '4/5': '80%',
                '9/10': '90%'
               },
            maxHeight: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '192': '48rem',
                '384': '96rem',
                '1/5': '20vh',
                '1/4': '25vh',
                '1/3': '33.3333vh',
                '1/2': '50vh',
                '2/3': '67.6666vh',
                '3/4': '75vh',
                '4/5': '80vh',
                '9/10': '90vh',
            },
        },
    },
    plugins: [
        forms,
        require("daisyui")
    ],
    daisyui: {
        themes: [
            {
                light: {
                    // ...require("daisyui/src/colors/themes")["[data-theme=light]"],
                    "primary": "#c379ff",

                    "secondary": "#1aa897",

                    "accent": "#ea9b67",

                    "neutral": "#18A594",

                    // "neutral-focus": "#bfd4ff",

                    "neutral-content": "#f5f5f4",

                    "base-100": "#dfddee",

                    "base-200": " #E7F5F5",

                    "base-content": "#372464",

                    "info": "#5371f3",

                    "success": "#6deeba",

                    "warning": "#8a610f",

                    "error": "#e94e60",

                },
            },
            {
                'newdark': {
                    "primary": "#f6ff7f",

                    "secondary": "#2dd4bf",

                    "accent": "#07772c",

                    "neutral": "#16151e",

                    "base-100": "#2e3447",

                    "info": "#2b9cde",

                    "success": "#31dd8a",

                    "warning": "#f5c751",

                    "error": "#ea8a7b",
                },
            },
        ],
    },
};
