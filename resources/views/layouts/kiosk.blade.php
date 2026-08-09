<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buku Tamu — {{ setting('app_name', 'PustakaManis') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-screen overflow-hidden bg-navy-base font-sans text-pearl antialiased">
    <div class="fixed inset-0 -z-10 bg-mesh-gradient"></div>

    {{-- Tombol Kembali ke Dashboard --}}
    <a href="{{ route('dashboard') }}" class="btn-skeuo glass-panel fixed top-6 left-6 z-50 flex items-center gap-2 rounded-pill px-5 py-2.5 text-sm font-semibold text-pearl transition hover:bg-white/10 shadow-glass">
        <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18'/></svg>
        Kembali ke Dashboard
    </a>

    <main
        class="flex h-screen w-full items-center justify-center"
        x-data="kioskApp()"
        x-init="init()"
        x-trap.noscroll="true"
    >
        {{ $slot }}
    </main>
</body>
</html>