/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './templates/**/*.html.twig',
        './assets/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                maroon: '#8B1E1E',
                gold: '#FFD700',
                beige: '#FFF8E7',
                offwhite: '#FAF9F6',
                dark: '#000000',
            },
        },
    },
    plugins: [],
}

