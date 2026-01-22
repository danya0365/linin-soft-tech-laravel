module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    darkMode: 'class', // Enable dark mode with class strategy
    // Safelist dynamic classes used in menu components using patterns
    safelist: [
        // Gradient patterns - covers all color variations
        {
            pattern: /^from-(blue|amber|orange|rose|red|emerald|green|teal|cyan|sky|violet|purple|pink|indigo|slate|gray)-(400|500|600|700|800)$/,
        },
        {
            pattern: /^to-(blue|amber|orange|rose|red|emerald|green|teal|cyan|sky|violet|purple|pink|indigo|slate|gray)-(500|600|700|800)$/,
        },
        // Text colors for subtitles and icons
        {
            pattern: /^text-(blue|amber|orange|rose|red|emerald|green|teal|cyan|sky|violet|purple|pink|indigo|slate|gray)-(100|200|300|500|600)$/,
        },
        // Background gradient
        'bg-gradient-to-br',
    ],
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

