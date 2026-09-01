/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                slate: {
                    950: '#0b1220',
                },
            },
        },
    },
    plugins: [],
};
