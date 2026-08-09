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
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                navy: {
                    base: 'rgb(var(--navy-base) / <alpha-value>)',
                    soft: 'rgb(var(--navy-soft) / <alpha-value>)',
                    deep: 'rgb(var(--navy-deep) / <alpha-value>)',
                },
                pearl: 'rgb(var(--pearl) / <alpha-value>)',
                azure: {
                    soft: 'rgb(var(--azure-soft) / <alpha-value>)',
                    light: 'rgb(var(--azure-light) / <alpha-value>)',
                    glow: 'rgba(151, 221, 233, 0.4)',
                },
                amber: {
                    warm: 'rgb(var(--amber-warm) / <alpha-value>)',
                },
                danger: {
                    red: 'rgb(var(--danger-red) / <alpha-value>)',
                    soft: 'rgba(248, 113, 113, 0.2)',
                },
                success: {
                    green: 'rgb(var(--azure-soft) / <alpha-value>)',
                    soft: 'rgba(151, 221, 233, 0.2)',
                },
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
                pill: '9999px',
            },
            boxShadow: {
                // Liquid Glass Shadow
                glass: '0 8px 32px 0 rgba(0, 0, 0, 0.3), inset 0 1px 0 0 rgba(255, 255, 255, 0.1)',
                // Spatial Floating Shadow
                spatial: '0 25px 50px -12px rgba(0, 0, 0, 0.5)',
                // Skeuomorphic Tactile Button (Press effect)
                tactile:
                    '0 4px 6px rgba(0,0,0,0.2), inset 0 2px 4px rgba(255,255,255,0.2), inset 0 -2px 4px rgba(0,0,0,0.1)',
                'tactile-pressed':
                    'inset 0 4px 8px rgba(0,0,0,0.3), inset 0 2px 4px rgba(255,255,255,0.1)',
                // Debossed Input
                debossed:
                    'inset 0 2px 8px rgba(0, 0, 0, 0.4), inset 0 1px 2px rgba(0,0,0,0.2)',
            },
            backgroundImage: {
                'mesh-gradient':
                    'radial-gradient(at 20% 20%, #2A3B54 0px, transparent 50%), radial-gradient(at 80% 80%, #1A2B42 0px, transparent 50%)',
                'glass-edge':
                    'linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 50%, rgba(255,255,255,0.05) 100%)',
            },
            backdropBlur: {
                xs: '2px',
                liquid: '12px',
            },
        },
    },
    plugins: [],
};
