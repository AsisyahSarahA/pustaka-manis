<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PustakaManis') — {{ setting('app_name', 'PustakaManis') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-navy-base font-sans text-pearl antialiased">
    <div class="fixed inset-0 -z-10 bg-mesh-gradient"></div>

    <button
        type="button"
        x-data="themeToggle()"
        @click="toggle()"
        class="fixed right-4 top-4 z-50 rounded-2xl p-2.5 text-pearl/70 transition hover:bg-white/5 hover:text-pearl"
        :title="theme === 'light' ? 'Mode Gelap' : 'Mode Terang'"
    >
        <template x-if="theme === 'light'">
            <span class="block h-5 w-5"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z'/></svg></span>
        </template>
        <template x-if="theme !== 'light'">
            <span class="block h-5 w-5"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z'/></svg></span>
        </template>
    </button>

    <main class="flex min-h-screen items-center justify-center p-4">
        {{ $slot }}
    </main>
    <x-toast />
</body>
</html>
