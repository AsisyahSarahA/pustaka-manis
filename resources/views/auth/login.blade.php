<x-layouts.guest title="Masuk">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            @if ($logo = setting('app_logo'))
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center overflow-hidden rounded-4xl bg-azure-soft/10 shadow-[0_0_40px_rgba(151,221,233,0.3)]"><img src="{{ asset($logo) }}" alt="{{ setting('app_name', 'PustakaManis') }}" class="h-14 w-14 object-contain"></div>
            @else
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-4xl bg-azure-soft/15 text-azure-soft shadow-[0_0_40px_rgba(151,221,233,0.3)]"><x-icon name="library" class="h-8 w-8" /></div>
            @endif
            <h1 class="text-2xl font-bold text-pearl">{{ setting('app_name', 'PustakaManis') }}</h1>
            <p class="mt-1 text-sm text-pearl/50">Perpustakaan {{ setting('school_name', 'SMP') }} — Sistem Digital</p>
        </div>

        <div class="glass-panel rounded-5xl p-8">
            <h2 class="mb-1 text-lg font-bold text-pearl">Selamat datang kembali!</h2>
            <p class="mb-6 text-sm text-pearl/50">Masuk untuk melanjutkan ke ruang kerja pustakawan.</p>

            @if ($errors->any())
                <div class="mb-5 space-y-2">
                    @foreach ($errors->all() as $error)
                        <x-alert type="error">{{ $error }}</x-alert>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <x-input
                    name="username"
                    label="Username"
                    placeholder="Masukkan username"
                    :value="old('username')"
                    required
                    autofocus
                    icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'/></svg>"
                />

                <x-input
                    type="password"
                    name="password"
                    label="Password"
                    placeholder="••••••••"
                    required
                    icon="<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z'/></svg>"
                />

                <label class="flex items-center gap-2 text-sm text-pearl/60">
                    <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/10 text-azure-soft focus:ring-azure-soft">
                    Ingat saya di perangkat ini
                </label>

                <x-button type="submit" class="w-full text-base">
                    Masuk
                    <span class="h-4 w-4"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3'/></svg></span>
                </x-button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-pearl/30">
            © {{ date('Y') }} {{ setting('app_name', 'PustakaManis') }} · Berjalan penuh offline di jaringan lokal sekolah
        </p>
    </div>
</x-layouts.guest>
