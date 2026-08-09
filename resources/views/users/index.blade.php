<x-layouts.app>
    @section('page_title', 'Manajemen User')

    <div x-data="liveTable('{{ route('users.index') }}')" class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-pearl">Manajemen Pengguna</h2>
                <p class="mt-1 text-sm text-pearl/50">Kelola akun pustakawan dan administrator.</p>
            </div>
            <x-button href="{{ route('users.create') }}" variant="primary">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah User
            </x-button>
        </div>

        <form data-live-form method="GET" class="glass-panel rounded-4xl p-4" @submit.prevent="reload()">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_200px_auto]">
                <x-input
                    name="search"
                    placeholder="Cari nama atau username..."
                    :value="request('search')"
                    @input.debounce.300ms="reload()"
                />
                <select name="role" @change="reload()" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm">
                    <option value="">Semua Role</option>
                    @foreach (['admin', 'pustakawan', 'viewer'] as $role)
                        <option value="{{ $role }}" @selected(request('role') == $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="primary"><x-icon name="search" class="h-4 w-4" /> Cari</x-button>
            </div>
        </form>

        <div x-show="loading" x-cloak class="flex items-center justify-center gap-2 py-4 text-sm text-pearl/50">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-azure-soft border-t-transparent"></span>
            Memuat...
        </div>

        <div data-live-results>
            @include('users.partials.results', ['users' => $users])
        </div>
    </div>
</x-layouts.app>