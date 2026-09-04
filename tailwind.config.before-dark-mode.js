/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                primary: '#163B6D',
                secondary: '#F59B45',
                accent: '#6FA9E6',
                background: '#F8FAFC',
                card: '#FFFFFF',
                border: '#E5E7EB',

                success: '#22C55E',
                warning: '#FBBF24',
                error: '#EF4444',

                navy: {
                    50: '#F2F6FB',
                    100: '#E3ECF7',
                    200: '#C3D7EC',
                    300: '#96B9DB',
                    400: '#6A9AC9',
                    500: '#477FB4',
                    600: '#356598',
                    700: '#2D517B',
                    800: '#284667',
                    900: '#223B57',
                    950: '#163B6D',
                },

                sunset: {
                    50: '#FFF8F1',
                    100: '#FEEEDB',
                    200: '#FCD9B6',
                    300: '#F9BC84',
                    400: '#F59B45',
                    500: '#F27D24',
                    600: '#E36019',
                    700: '#BC4817',
                    800: '#963A1B',
                    900: '#793219',
                },

                sky: {
                    50: '#F3F8FE',
                    100: '#E7F1FC',
                    200: '#C9E1F8',
                    300: '#9BC9F0',
                    400: '#6FA9E6',
                    500: '#478BD8',
                    600: '#326FC4',
                    700: '#2A5AA0',
                    800: '#284D85',
                    900: '#26426E',
                },
            },

            fontFamily: {
                heading: ['Poppins', 'sans-serif'],
                button: ['Poppins', 'sans-serif'],
                body: ['Inter', 'sans-serif'],
            },

            boxShadow: {
                'soft': '0 4px 20px rgba(22, 59, 109, 0.08)',
                'card': '0 2px 10px rgba(15, 23, 42, 0.06)',
            },

            borderRadius: {
                'xl2': '1rem',
            },
        },
    },

    plugins: [],
};
