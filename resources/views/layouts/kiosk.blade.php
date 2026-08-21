<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arcade Kiosk Terminal — {{ setting('app_name', 'PustakaManis') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-screen overflow-hidden bg-brutal-bg font-sans text-black antialiased">

    {{-- Return Button --}}
    <a href="{{ route('dashboard') }}" class="btn-brutal fixed top-6 left-6 z-50 flex items-center gap-2 border-3 border-black bg-brutal-yellow px-5 py-2.5 text-xs font-black uppercase text-black shadow-brutal">
        <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='3' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18'/></svg>
        [ESC] DASHBOARD
    </a>

    <main
        class="flex h-screen w-full items-center justify-center p-6"
        x-data="kioskApp()"
        x-init="init()"
        x-trap.noscroll="true"
    >
        {{ $slot }}
    </main>
</body>
</html>