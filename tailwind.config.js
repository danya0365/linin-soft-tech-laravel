module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    darkMode: 'class', // Enable dark mode with class strategy
    // Safelist dynamic classes used in menu components
    safelist: [
        // Gradients - from (all variations)
        'from-blue-500', 'from-blue-600', 'from-blue-700', 'from-blue-800',
        'from-amber-400', 'from-amber-500',
        'from-orange-500', 'from-orange-600',
        'from-rose-500', 'from-rose-600',
        'from-red-500', 'from-red-600',
        'from-emerald-500', 'from-emerald-600',
        'from-green-500', 'from-green-600',
        'from-teal-500', 'from-teal-600',
        'from-cyan-500', 'from-cyan-600',
        'from-sky-500', 'from-sky-600',
        'from-violet-500', 'from-violet-600', 'from-violet-700',
        'from-purple-500', 'from-purple-600', 'from-purple-700',
        'from-pink-500', 'from-pink-600',
        'from-indigo-500', 'from-indigo-600', 'from-indigo-700',
        'from-slate-500', 'from-slate-600', 'from-slate-700',
        'from-gray-500', 'from-gray-600', 'from-gray-700', 'from-gray-800',
        // Gradients - to (all variations)
        'to-blue-500', 'to-blue-600', 'to-blue-700', 'to-blue-800',
        'to-amber-500', 'to-amber-600',
        'to-orange-500', 'to-orange-600',
        'to-rose-500', 'to-rose-600',
        'to-red-500', 'to-red-600',
        'to-emerald-500', 'to-emerald-600',
        'to-green-500', 'to-green-600',
        'to-teal-500', 'to-teal-600',
        'to-cyan-500', 'to-cyan-600',
        'to-sky-500', 'to-sky-600',
        'to-violet-600', 'to-violet-700', 'to-violet-800',
        'to-purple-600', 'to-purple-700', 'to-purple-800',
        'to-pink-500', 'to-pink-600',
        'to-indigo-600', 'to-indigo-700', 'to-indigo-800',
        'to-slate-600', 'to-slate-700', 'to-slate-800',
        'to-gray-600', 'to-gray-700', 'to-gray-800',
        // Text colors for subtitles
        'text-blue-100', 'text-blue-200',
        'text-amber-100', 'text-amber-200',
        'text-orange-100', 'text-orange-200',
        'text-rose-100', 'text-rose-200',
        'text-red-100', 'text-red-200',
        'text-emerald-100', 'text-emerald-200',
        'text-green-100', 'text-green-200',
        'text-teal-100', 'text-teal-200',
        'text-cyan-100', 'text-cyan-200',
        'text-sky-100', 'text-sky-200',
        'text-violet-100', 'text-violet-200',
        'text-purple-100', 'text-purple-200',
        'text-pink-100', 'text-pink-200',
        'text-indigo-100', 'text-indigo-200',
        'text-slate-100', 'text-slate-200', 'text-slate-300',
        'text-gray-100', 'text-gray-200', 'text-gray-300',
        // Icon colors
        'text-blue-500', 'text-blue-600',
        'text-amber-500', 'text-orange-500',
        'text-emerald-500', 'text-emerald-600',
        'text-violet-500', 'text-purple-500',
        'text-indigo-500', 'text-indigo-600',
        'text-slate-500', 'text-slate-600',
        'text-gray-500', 'text-gray-600',
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

