<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ setting('app_name', 'PustakaManis') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body class="min-h-screen overflow-x-hidden bg-navy-base font-sans text-pearl antialiased">
    <div id="top-progress-bar"></div>
    <div class="fixed inset-0 -z-10 bg-mesh-gradient"></div>

    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        {{-- Sidebar / Dock (fixed, tidak ikut scroll) --}}
        <aside
            x-cloak
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-[calc(100%+2rem)] lg:translate-x-0'"
            class="layout-sidebar"
        >
            <div class="mb-6 flex items-center gap-3 px-2">
                @if ($logo = setting('app_logo'))
                    <img src="{{ asset($logo) }}" alt="{{ setting('app_name', 'PustakaManis') }}" class="h-11 w-11 rounded-2xl bg-white/10 object-contain p-1.5 shadow-[0_0_24px_rgba(151,221,233,0.15)]">
                @else
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-azure-soft/15 text-azure-soft shadow-[0_0_24px_rgba(151,221,233,0.25)]"><x-icon name="library" class="h-6 w-6" /></div>
                @endif
                <div>
                    <p class="font-bold leading-tight text-pearl">{{ setting('app_name', 'PustakaManis') }}</p>
                    <p class="text-xs text-pearl/40">{{ setting('school_name', 'SMP') }}</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto">
                @if (Auth::check())
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'/></svg>">Dashboard</x-sidebar-link>

                    <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-pearl/30">Katalog</p>
                    <x-sidebar-link :href="route('books.index')" :active="request()->routeIs('books.*')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'/></svg>">Katalog Buku</x-sidebar-link>
                    <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'/></svg>">Kategori</x-sidebar-link>
                    <x-sidebar-link :href="route('members.index')" :active="request()->routeIs('members.*')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'/></svg>">Anggota</x-sidebar-link>

                    <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-pearl/30">Sirkulasi</p>
                    <x-sidebar-link :href="route('loans.borrow')" :active="request()->routeIs('loans.borrow')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'/></svg>">Peminjaman</x-sidebar-link>
                    <x-sidebar-link :href="route('loans.return')" :active="request()->routeIs('loans.return')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3'/></svg>">Pengembalian</x-sidebar-link>
                    <x-sidebar-link :href="route('fines.index')" :active="request()->routeIs('fines.*')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v9.75a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5zm10.5 6a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z'/></svg>">Kasir Denda</x-sidebar-link>

                    <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-pearl/30">Lainnya</p>
                    @if (setting('module_visitor_enabled', true))
                        <x-sidebar-link :href="route('kiosk.index')" :active="request()->routeIs('kiosk.*')" data-no-spa
                            icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z'/></svg>">Buku Tamu</x-sidebar-link>
                    @endif
                    @if (setting('module_report_enabled', true))
                        <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')"
                            icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'/></svg>">Laporan</x-sidebar-link>
                    @endif

                    @if (Auth::user()?->isAdmin())
                        <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-pearl/30">Administrasi</p>
                        <x-sidebar-link :href="route('settings.index')" :active="request()->routeIs('settings.*')"
                            icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z'/><path stroke-linecap='round' stroke-linejoin='round' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z'/></svg>">Pengaturan</x-sidebar-link>
                        <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')"
                            icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'/></svg>">Manajemen User</x-sidebar-link>
                    @endif
                @endif
            </nav>

            <div class="mt-4 border-t border-white/10 pt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button type="submit" variant="secondary" class="w-full" data-no-spa>
                        <span class="h-4 w-4"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75'/></svg></span>
                        Keluar
                    </x-button>
                </form>
            </div>
        </aside>

        {{-- Overlay mobile --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-30 bg-navy-deep/60 backdrop-blur-sm lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Main Container --}}
        <div class="layout-main">
            {{-- Header --}}
            <header class="glass-panel-blur sticky top-4 lg:top-6 z-20 mx-4 mt-4 lg:mt-6 lg:mr-6 lg:ml-0 flex items-center gap-4 rounded-4xl px-5 py-3">
                <button @click="sidebarOpen = !sidebarOpen" class="rounded-2xl p-2 text-pearl/70 transition hover:bg-white/5 hover:text-pearl lg:hidden">
                    <span class="block h-5 w-5"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'/></svg></span>
                </button>

                <div class="flex-1" id="page-header-title">
                    @hasSection('page_title')
                        <h1 class="text-base font-bold text-pearl">@yield('page_title')</h1>
                    @else
                        <h1 class="text-base font-bold text-pearl">{{ setting('app_name', 'PustakaManis') }}</h1>
                    @endif
                </div>

                <div class="hidden md:block">
                    <x-search-global />
                </div>

                {{-- Toggle Tema --}}
                <button
                    type="button"
                    x-data="themeToggle()"
                    @click="toggle()"
                    class="rounded-2xl p-2.5 text-pearl/70 transition hover:bg-white/5 hover:text-pearl"
                    :title="theme === 'light' ? 'Mode Gelap' : 'Mode Terang'"
                >
                    <template x-if="theme === 'light'">
                        <span class="block h-5 w-5"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z'/></svg></span>
                    </template>
                    <template x-if="theme !== 'light'">
                        <span class="block h-5 w-5"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z'/></svg></span>
                    </template>
                </button>

                {{-- Notification Bell Component --}}
                <div
                    x-data="{
                        open: false,
                        unreadCount: 0,
                        notifications: [],
                        fetchNotifications() {
                            fetch('{{ route('api.notifications') }}')
                                .then(res => res.json())
                                .then(data => {
                                    this.unreadCount = data.total_unread;
                                    this.notifications = data.notifications;
                                })
                                .catch(err => console.warn('Error fetching notifications:', err));
                        },
                        init() {
                            this.fetchNotifications();
                            setInterval(() => this.fetchNotifications(), 60000);
                        }
                    }"
                    class="relative"
                >
                    <button
                        type="button"
                        @click="open = !open"
                        class="relative rounded-2xl p-2.5 text-pearl/70 transition hover:bg-white/5 hover:text-pearl"
                        title="Notifikasi Internal"
                    >
                        <span class="block h-5 w-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </span>
                        <template x-if="unreadCount > 0">
                            <span
                                x-text="unreadCount"
                                class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[10px] font-extrabold text-slate-950 shadow-[0_0_12px_rgba(245,158,11,0.6)] animate-pulse"
                            ></span>
                        </template>
                    </button>

                    {{-- Liquid Glass Dropdown Mini-Modal --}}
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                        class="absolute right-0 top-14 z-50 w-80 rounded-3xl border border-white/10 bg-navy-dark/95 p-4 shadow-2xl backdrop-blur-2xl md:w-96"
                    >
                        <div class="mb-3 flex items-center justify-between border-b border-white/10 pb-2">
                            <h3 class="text-sm font-bold text-pearl flex items-center gap-2">
                                🔔 Notifikasi System
                                <span x-show="unreadCount > 0" class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[10px] text-amber-300" x-text="unreadCount + ' baru'"></span>
                            </h3>
                            <button @click="fetchNotifications()" class="text-[11px] text-azure-soft hover:underline">Refresh</button>
                        </div>

                        <div class="max-h-72 space-y-2 overflow-y-auto">
                            <template x-for="item in notifications" :key="item.id">
                                <a
                                    :href="item.url"
                                    class="block rounded-2xl border border-white/5 bg-white/5 p-3 transition hover:bg-white/10"
                                >
                                    <div class="flex items-start gap-2.5">
                                        <span class="text-lg shrink-0" x-text="item.icon"></span>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold text-pearl" x-text="item.title"></p>
                                            <p class="mt-0.5 text-[11px] text-pearl/70 leading-tight" x-text="item.message"></p>
                                        </div>
                                    </div>
                                </a>
                            </template>

                            <template x-if="notifications.length === 0">
                                <div class="py-6 text-center text-xs text-pearl/50">
                                    <p class="text-xl">✨</p>
                                    <p class="mt-1">Tidak ada notifikasi baru saat ini.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                @auth
                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-pearl">{{ Auth::user()->name }}</p>
                            <p class="text-xs capitalize text-pearl/40">{{ Auth::user()->role }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-azure-soft/15 text-sm font-bold text-azure-soft shadow-[0_0_20px_rgba(151,221,233,0.25)] ring-2 ring-azure-soft/30">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                @endauth
            </header>

            {{-- Content SPA Wrapper --}}
            <div id="main-content" class="flex-1 px-4 py-4 lg:py-6 lg:pr-6 lg:pl-0">
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    <x-toast />

    <script>
        function getSwalTheme() {
            const isLight = document.documentElement.classList.contains('light');
            return Swal.mixin({
                background: isLight ? '#ffffff' : '#1c283c',
                color: isLight ? '#162032' : '#F4F7F6',
                confirmButtonColor: '#0ea5b4',
                cancelButtonColor: isLight ? '#cbd5e1' : 'rgba(255, 255, 255, 0.15)',
                customClass: {
                    popup: 'rounded-4xl shadow-spatial border border-white/10',
                    confirmButton: 'btn-skeuo rounded-pill px-6 py-2.5 text-sm font-bold text-white',
                    cancelButton: 'btn-skeuo rounded-pill px-6 py-2.5 text-sm font-semibold text-pearl',
                }
            });
        }

        function confirmDelete(event, itemName = 'data ini') {
            event.preventDefault();
            const form = event.target.closest('form');
            getSwalTheme().fire({
                title: 'Hapus ' + itemName + '?',
                text: 'Data yang sudah dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        document.addEventListener('DOMContentLoaded', () => {
            @if (session()->has('success'))
                getSwalTheme().fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @js(session('success')),
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif
            @if (session()->has('error'))
                getSwalTheme().fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @js(session('error')),
                    timer: 4000,
                    showConfirmButton: true
                });
            @endif
            @if (session()->has('warning'))
                getSwalTheme().fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: @js(session('warning')),
                    timer: 4000,
                    showConfirmButton: true
                });
            @endif
            @if (session()->has('toast'))
                getSwalTheme().fire({
                    icon: @js(session('toast.type', 'info')),
                    title: @js(session('toast.type') === 'error' ? 'Gagal' : (session('toast.type') === 'success' ? 'Berhasil' : 'Informasi')),
                    text: @js(session('toast.message')),
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
