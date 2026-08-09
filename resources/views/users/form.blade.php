<x-layouts.app>
    @section('page_title', $user->exists ? 'Edit User' : 'Tambah User')

    <div class="mx-auto max-w-2xl">
        <div class="glass-panel rounded-4xl p-6">
            <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="space-y-5">
                @csrf
                @if ($user->exists)
                    @method('PUT')
                @endif

                <x-input
                    name="name"
                    label="Nama Lengkap"
                    :value="old('name', $user->name)"
                    :error="$errors->first('name')"
                    required
                />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input
                        name="username"
                        label="Username"
                        :value="old('username', $user->username)"
                        :error="$errors->first('username')"
                        required
                    />
                    <x-input
                        name="email"
                        label="Email (opsional)"
                        :value="old('email', $user->email)"
                        :error="$errors->first('email')"
                    />
                </div>

                <div>
                    <label for="role" class="mb-2 block text-sm font-medium text-pearl/80">Role</label>
                    <select name="role" id="role" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm" required>
                        @foreach (['admin' => 'Super Admin', 'pustakawan' => 'Pustakawan', 'viewer' => 'Viewer / Kepala Sekolah'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('role', $user->role) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-xs text-danger-red">{{ $message }}</p>
                    @enderror
                </div>

                <x-input
                    type="password"
                    name="password"
                    label="{{ $user->exists ? 'Password Baru (kosongkan jika tetap)' : 'Password' }}"
                    placeholder="{{ $user->exists ? '••••••••' : 'Minimal 6 karakter' }}"
                    :error="$errors->first('password')"
                    autocomplete="new-password"
                />

                <label class="flex items-center gap-2 text-sm text-pearl/60">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true)) class="rounded border-white/20 bg-white/10 text-azure-soft focus:ring-azure-soft">
                    Akun aktif
                </label>

                <div class="flex items-center justify-end gap-3">
                    <x-button href="{{ route('users.index') }}" variant="secondary">Batal</x-button>
                    <x-button type="submit" variant="primary">Simpan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>