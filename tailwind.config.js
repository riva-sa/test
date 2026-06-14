/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/Livewire/**/*.php',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50:  '#fafafa',
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                    300: '#d4d4d8',
                    400: '#a1a1aa',
                    500: '#71717a',
                    600: '#52525b',
                    700: '#3f3f46',
                    800: '#27272a',
                    900: '#18181b',
                },
            },
            fontFamily: {
                sans: ['IBM Plex Sans Arabic', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
