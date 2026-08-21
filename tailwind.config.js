import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                paper: '#F4F4F0',
                ink: '#000000',
                brutal: {
                    yellow: '#FFDE00',
                    neon: '#00FF66',
                    pink: '#FF003C',
                    blue: '#0066FF',
                    bg: '#F4F4F0',
                    dark: '#121212',
                    card: '#FFFFFF',
                    input: '#EAEAE5',
                },
                // Retain semantic mappings mapped to neo-brutalist high-contrast colors
                navy: {
                    base: '#F4F4F0',
                    soft: '#FFFFFF',
                    deep: '#000000',
                },
                pearl: '#000000',
                azure: {
                    soft: '#0066FF',
                    light: '#00FF66',
                    glow: '#FFDE00',
                },
                amber: {
                    warm: '#FFDE00',
                },
                danger: {
                    red: '#FF003C',
                    soft: '#FF003C',
                },
                success: {
                    green: '#00FF66',
                    soft: '#00FF66',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Space Grotesk', 'Archivo Black', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'VT323', ...defaultTheme.fontFamily.mono],
            },
            borderRadius: {
                none: '0px',
                sm: '2px',
                DEFAULT: '0px',
                md: '4px',
                lg: '4px',
                xl: '4px',
                '2xl': '4px',
                '3xl': '4px',
                '4xl': '0px',
                '5xl': '0px',
                pill: '0px',
            },
            boxShadow: {
                brutal: '5px 5px 0px 0px #000000',
                'brutal-sm': '3px 3px 0px 0px #000000',
                'brutal-lg': '8px 8px 0px 0px #000000',
                'brutal-xl': '12px 12px 0px 0px #000000',
                'brutal-press': '0px 0px 0px 0px #000000',
                'brutal-yellow': '5px 5px 0px 0px #FFDE00',
                'brutal-neon': '5px 5px 0px 0px #00FF66',
                'brutal-pink': '5px 5px 0px 0px #FF003C',
                'brutal-blue': '5px 5px 0px 0px #0066FF',
                // Keep backward compatibility aliases with brutal hard shadows
                glass: '5px 5px 0px 0px #000000',
                spatial: '8px 8px 0px 0px #000000',
                tactile: '4px 4px 0px 0px #000000',
                'tactile-pressed': '0px 0px 0px 0px #000000',
                debossed: 'none',
            },
            borderWidth: {
                DEFAULT: '2px',
                '2': '2px',
                '3': '3px',
                '4': '4px',
                '6': '6px',
                '8': '8px',
            },
        },
    },
    plugins: [],
};

