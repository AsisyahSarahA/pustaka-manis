<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ setting('app_name', 'PustakaManis') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen overflow-x-hidden bg-brutal-bg font-sans text-black antialiased">
    <div id="top-progress-bar"></div>

    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        {{-- Sidebar (Fixed Panel with 4px border-right) --}}
        <aside
            x-cloak
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="layout-sidebar border-r-4 border-black bg-brutal-bg"
        >
            <div class="mb-6 flex items-center gap-3 px-2 border-b-4 border-black pb-4">
                @if ($logo = setting('app_logo'))
                    <img src="{{ asset($logo) }}" alt="{{ setting('app_name', 'PustakaManis') }}" class="h-10 w-10 border-2 border-black bg-white object-contain p-1 shadow-brutal-sm">
                @else
                    <div class="flex h-10 w-10 items-center justify-center border-2 border-black bg-brutal-yellow text-black font-extrabold shadow-brutal-sm">
                        <x-icon name="library" class="h-6 w-6" />
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="font-heading font-black tracking-wider text-black text-sm uppercase truncate">{{ setting('app_name', 'PustakaManis') }}</p>
                    <p class="font-mono text-[10px] font-bold uppercase tracking-widest text-black/60 truncate">{{ setting('school_name', 'SMP') }}</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1.5 overflow-y-auto pr-1">
                @if (Auth::check())
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'/></svg>">Dashboard</x-sidebar-link>

                    <p class="mt-4 mb-1 px-2 font-mono text-[10px] font-extrabold uppercase tracking-widest text-black/70">/// Katalogs</p>
                    <x-sidebar-link :href="route('books.index')" :active="request()->routeIs('books.*')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'/></svg>">Katalog Buku</x-sidebar-link>
                    <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'/></svg>">Kategori</x-sidebar-link>
                    <x-sidebar-link :href="route('members.index')" :active="request()->routeIs('members.*')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'/></svg>">Anggota</x-sidebar-link>

                    <p class="mt-4 mb-1 px-2 font-mono text-[10px] font-extrabold uppercase tracking-widest text-black/70">/// Sirkulasi</p>
                    <x-sidebar-link :href="route('loans.borrow')" :active="request()->routeIs('loans.borrow')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'/></svg>">Peminjaman</x-sidebar-link>
                    <x-sidebar-link :href="route('loans.return')" :active="request()->routeIs('loans.return')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3'/></svg>">Pengembalian</x-sidebar-link>
                    <x-sidebar-link :href="route('fines.index')" :active="request()->routeIs('fines.*')"
                        icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v9.75a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5zm10.5 6a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z'/></svg>">Kasir Denda</x-sidebar-link>

                    <p class="mt-4 mb-1 px-2 font-mono text-[10px] font-extrabold uppercase tracking-widest text-black/70">/// Lainnya</p>
                    @if (setting('module_visitor_enabled', true))
                        <x-sidebar-link :href="route('kiosk.index')" :active="request()->routeIs('kiosk.*')" data-no-spa
                            icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z'/></svg>">Buku Tamu</x-sidebar-link>
                    @endif
                    @if (setting('module_report_enabled', true))
                        <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')"
                            icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'/></svg>">Laporan</x-sidebar-link>
                    @endif

                    @if (Auth::user()?->isAdmin())
                        <p class="mt-4 mb-1 px-2 font-mono text-[10px] font-extrabold uppercase tracking-widest text-black/70">/// Admin</p>
                        <x-sidebar-link :href="route('settings.index')" :active="request()->routeIs('settings.*')"
                            icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z'/><path stroke-linecap='round' stroke-linejoin='round' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z'/></svg>">Pengaturan</x-sidebar-link>
                        <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')"
                            icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'/></svg>">Manajemen User</x-sidebar-link>
                    @endif
                @endif
            </nav>

            <div class="mt-4 border-t-3 border-black pt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button type="submit" variant="danger" class="w-full" data-no-spa>
                        <span class="h-4 w-4"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2.5' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75'/></svg></span>
                        LOGOUT [X]
                    </x-button>
                </form>
            </div>
        </aside>

        {{-- Mobile Backdrop --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            class="fixed inset-0 z-30 bg-black/80 brutal-backdrop lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Main Container --}}
        <div class="layout-main">
            {{-- Header (Separated by border-b-4 border-black) --}}
            <header class="sticky top-0 z-20 bg-brutal-bg border-b-4 border-black px-6 py-4 flex items-center justify-between gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="btn-skeuo border-2 border-black bg-brutal-yellow p-2 text-black lg:hidden">
                    <span class="block h-5 w-5"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2.5' stroke='currentColor' class='h-5 w-5'><path stroke-linecap='round' stroke-linejoin='round' d='M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'/></svg></span>
                </button>

                <div class="flex-1" id="page-header-title">
                    @hasSection('page_title')
                        <h1 class="text-xl font-heading font-black text-black tracking-wide">@yield('page_title')</h1>
                    @else
                        <h1 class="text-xl font-heading font-black text-black tracking-wide">{{ setting('app_name', 'PustakaManis') }}</h1>
                    @endif
                </div>

                <div class="hidden md:block">
                    <x-search-global />
                </div>

                {{-- Notification Bell Component (Terminal Log Dropdown) --}}
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
                        class="btn-skeuo relative border-2 border-black bg-white p-2.5 text-black"
                        title="System Logs"
                    >
                        <span class="block h-5 w-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </span>
                        <template x-if="unreadCount > 0">
                            <span
                                x-text="unreadCount"
                                class="absolute -right-2 -top-2 flex h-6 min-w-6 items-center justify-center border-2 border-black bg-brutal-pink px-1 font-mono text-[11px] font-black text-white shadow-brutal-sm"
                            ></span>
                        </template>
                    </button>

                    {{-- Terminal Log Dropdown Mini-Modal --}}
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-cloak
                        class="absolute right-0 top-14 z-50 w-80 border-3 border-black bg-black p-4 text-white shadow-brutal-lg md:w-96"
                    >
                        <div class="mb-3 flex items-center justify-between border-b-2 border-white/30 pb-2 font-mono text-xs uppercase tracking-widest text-brutal-yellow">
                            <span class="flex items-center gap-2 font-bold">
                                > SYSTEM LOGS
                                <span x-show="unreadCount > 0" class="border border-brutal-yellow bg-brutal-yellow/20 px-1.5 py-0.5 text-[10px] text-brutal-yellow" x-text="unreadCount + ' NEW'"></span>
                            </span>
                            <button @click="fetchNotifications()" class="text-brutal-neon hover:underline text-[10px]">[REFRESH]</button>
                        </div>

                        <div class="max-h-72 space-y-2 overflow-y-auto font-mono text-xs">
                            <template x-for="item in notifications" :key="item.id">
                                <a
                                    :href="item.url"
                                    class="block border-2 border-white/20 bg-white/10 p-2.5 transition-transform duration-75 ease-linear hover:translate-x-1 hover:border-brutal-yellow hover:bg-white/20"
                                >
                                    <div class="flex items-start gap-2">
                                        <span class="shrink-0 text-brutal-neon" x-text="item.icon || '❯'"></span>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold uppercase tracking-wider text-brutal-yellow text-[11px]" x-text="item.title"></p>
                                            <p class="mt-0.5 text-[10px] text-white/90 leading-tight" x-text="item.message"></p>
                                        </div>
                                    </div>
                                </a>
                            </template>

                            <template x-if="notifications.length === 0">
                                <div class="py-6 text-center text-xs font-mono text-white/50">
                                    <p class="text-lg">NO_LOGS_FOUND</p>
                                    <p class="mt-1 text-[10px]">[0 UNREAD NOTIFICATIONS]</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                @auth
                    <div class="flex items-center gap-3 border-2 border-black bg-white px-3 py-1.5 shadow-brutal-sm">
                        <div class="hidden text-right sm:block">
                            <p class="text-xs font-heading font-bold uppercase tracking-wider text-black leading-none">{{ Auth::user()->name }}</p>
                            <p class="font-mono text-[10px] font-bold uppercase tracking-widest text-black/60">{{ Auth::user()->role }}</p>
                        </div>
                        <div class="flex h-8 w-8 items-center justify-center border-2 border-black bg-brutal-neon font-mono text-xs font-black text-black">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                @endauth
            </header>

            {{-- Content SPA Wrapper --}}
            <div id="main-content" class="flex-1 p-6">
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    <x-toast />

    <script>
        function getSwalTheme() {
            return Swal.mixin({
                background: '#FFFFFF',
                color: '#000000',
                confirmButtonColor: '#00FF66',
                cancelButtonColor: '#FF003C',
                customClass: {
                    popup: 'border-4 border-black shadow-brutal-xl rounded-none p-6 text-black',
                    confirmButton: 'btn-brutal bg-brutal-neon border-3 border-black text-black px-6 py-2.5 font-bold uppercase shadow-brutal',
                    cancelButton: 'btn-brutal bg-brutal-pink border-3 border-black text-white px-6 py-2.5 font-bold uppercase shadow-brutal',
                }
            });
        }

        function confirmDelete(event, itemName = 'data ini') {
            event.preventDefault();
            const form = event.target.closest('form');
            getSwalTheme().fire({
                title: 'HAPUS ' + itemName.toUpperCase() + '?',
                text: 'DATA YANG DIHAPUS TIDAK BISA DIKEMBALIKAN!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'YA, HAPUS NOW!',
                cancelButtonText: 'BATALKAN',
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
                    title: 'SUCCESS',
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
                    title: 'SYSTEM ERROR',
                    text: @js(session('error')),
                    timer: 4000,
                    showConfirmButton: true
                });
            @endif
            @if (session()->has('warning'))
                getSwalTheme().fire({
                    icon: 'warning',
                    title: 'WARNING',
                    text: @js(session('warning')),
                    timer: 4000,
                    showConfirmButton: true
                });
            @endif
            @if (session()->has('toast'))
                getSwalTheme().fire({
                    icon: @js(session('toast.type', 'info')),
                    title: @js(session('toast.type') === 'error' ? 'SYSTEM ERROR' : (session('toast.type') === 'success' ? 'TRANSACTION SUCCESS' : 'NOTIFICATION')),
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
