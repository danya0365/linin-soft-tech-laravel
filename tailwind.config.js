module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    darkMode: 'class', // Enable dark mode with class strategy
    theme: {
        extend: {
            colors: {
                // Custom brand colors
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                },
                // Dark mode specific colors
                dark: {
                    bg: '#0f172a',
                    card: '#1e293b',
                    border: '#334155',
                    text: '#e2e8f0',
                },
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
            },
            backdropBlur: {
                xs: '2px',
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ['dark'],
            textColor: ['dark'],
            borderColor: ['dark'],
        },
    },
    plugins: [],
};

