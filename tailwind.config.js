import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    safelist: [
        // warna teks dan latar belakang dinamis
        'text-green-500', 'text-yellow-500', 'text-red-500',
        'text-green-600', 'text-yellow-600', 'text-red-600',
        'bg-white', 'dark:bg-gray-900', 'dark:bg-gray-700',
        'bg-blue-500', 'bg-blue-600', 'hover:bg-blue-700',
        'translate-x-4', // untuk switch toggle
        'shadow', 'shadow-md', 'shadow-sm',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
    ],
};

