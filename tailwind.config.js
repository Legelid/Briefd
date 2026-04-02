import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                navy: '#0a0a0f',
                sidebar: '#0f0f18',
                accent: '#ff6b2b',
                'border-subtle': '#1e1e2e',
                'active-nav': '#1f1008',
                muted: '#6b6b8a',
                warning: '#ef9f27',
                danger: '#e24b4a',
            },
        },
    },

    plugins: [forms],
};
