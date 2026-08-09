<x-layouts.app>
    @section('page_title', 'Edit Anggota')

    <div class="mx-auto max-w-3xl">
        <div class="glass-panel rounded-4xl p-6">
            <div class="mb-5 flex items-center gap-3">
                <x-badge variant="azure">{{ $member->member_code }}</x-badge>
                <span class="text-sm text-pearl/50">{{ $member->type_label }}</span>
            </div>

            <form method="POST" action="{{ route('members.update', $member) }}" class="space-y-5" x-data="{ type: '{{ old('type', $member->type) }}' }">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-medium text-pearl/80">Tipe Anggota</label>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach (['siswa' => 'Siswa', 'guru' => 'Guru', 'staf' => 'Staf', 'eksternal' => 'Eksternal'] as $value => $label)
                            <label
                                class="cursor-pointer rounded-2xl border px-4 py-3 text-center text-sm font-medium transition-all duration-200"
                                :class="type === '{{ $value }}' ? 'border-azure-soft/50 bg-azure-soft/15 text-azure-soft shadow-[0_0_16px_rgba(151,221,233,0.15)]' : 'border-white/10 bg-white/5 text-pearl/60 hover:border-white/20'"
                            >
                                <input type="radio" name="type" value="{{ $value }}" x-model="type" class="hidden" @if (old('type', $member->type) === $value) checked @endif>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('type')
                        <p class="mt-1 text-xs text-danger-red">{{ $message }}</p>
                    @enderror
                </div>

                <x-input
                    name="name"
                    label="Nama Lengkap"
                    :value="old('name', $member->name)"
                    :error="$errors->first('name')"
                    required
                />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div x-show="type !== 'eksternal'">
                        <x-input
                            name="identity_number"
                            label="NIS / NIP / NIK"
                            :value="old('identity_number', $member->identity_number)"
                            :error="$errors->first('identity_number')"
                        />
                    </div>
                    <x-input
                        name="department_class"
                        label="Kelas / Jabatan"
                        :value="old('department_class', $member->department_class)"
                        :error="$errors->first('department_class')"
                    />
                </div>

                <x-input
                    name="phone"
                    label="Nomor Telepon (opsional)"
                    :value="old('phone', $member->phone)"
                    :error="$errors->first('phone')"
                />

                <label class="flex items-center gap-2 text-sm text-pearl/60">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member->is_active)) class="rounded border-white/20 bg-white/10 text-azure-soft focus:ring-azure-soft">
                    Anggota aktif (dapat meminjam buku)
                </label>

                <div class="flex items-center justify-end gap-3">
                    <x-button href="{{ route('members.show', $member) }}" variant="secondary">Batal</x-button>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
