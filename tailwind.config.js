module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    darkMode: 'class', // Enable dark mode with class strategy
    // Safelist dynamic classes used in menu components
    safelist: [
        // Gradients - from
        'from-blue-500', 'from-blue-700', 'from-amber-400', 'from-orange-500',
        'from-rose-500', 'from-red-500', 'from-red-600', 'from-emerald-500',
        'from-green-600', 'from-cyan-500', 'from-violet-500', 'from-purple-500',
        'from-purple-600', 'from-pink-600', 'from-indigo-600', 'from-teal-600',
        'from-sky-500', 'from-slate-500', 'from-gray-700',
        // Gradients - to
        'to-blue-500', 'to-blue-600', 'to-blue-700', 'to-blue-800',
        'to-orange-500', 'to-red-500', 'to-red-600', 'to-green-600',
        'to-teal-600', 'to-purple-600', 'to-violet-800', 'to-pink-600',
        'to-gray-700',
        // Text colors for subtitles
        'text-blue-100', 'text-blue-200', 'text-amber-100', 'text-orange-200',
        'text-rose-100', 'text-rose-200', 'text-emerald-100', 'text-emerald-200',
        'text-cyan-100', 'text-cyan-200', 'text-violet-100', 'text-violet-200',
        'text-purple-200', 'text-indigo-200', 'text-sky-200', 'text-slate-300',
        // Icon colors
        'text-blue-500', 'text-blue-600', 'text-orange-500', 'text-violet-500',
        'text-slate-500', 'text-slate-600', 'text-indigo-600',
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

