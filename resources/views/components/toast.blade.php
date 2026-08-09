<div
    x-data="toastManager()"
    x-init="
        @if (session()->has('toast'))
            addToast(@js(session('toast.message')), @js(session('toast.type')))
        @endif
        @if (session()->has('success'))
            addToast(@js(session('success')), 'success')
        @endif
        @if (session()->has('error'))
            addToast(@js(session('error')), 'error')
        @endif
    "
    @toast.window="addToast($event.detail.message, $event.detail.type)"
    class="fixed right-4 top-4 z-50 flex w-80 flex-col gap-2"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="glass-panel flex items-start gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-pearl"
        >
            <template x-if="toast.type === 'success'">
                <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-success-soft text-success-green"><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round' class='h-3.5 w-3.5'><polyline points='20 6 9 17 4 12'/></svg></span>
            </template>
            <template x-if="toast.type === 'error'">
                <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-danger-soft text-danger-red"><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round' class='h-3.5 w-3.5'><path d='M18 6 6 18M6 6l12 12'/></svg></span>
            </template>
            <template x-if="toast.type === 'warning'">
                <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-warm/20 text-amber-warm"><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round' class='h-3.5 w-3.5'><path d='m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z'/><path d='M12 9v4'/><path d='M12 17h.01'/></svg></span>
            </template>
            <span x-text="toast.message" class="flex-1 leading-snug"></span>
            <button @click="removeToast(toast.id)" class="text-pearl/40 hover:text-pearl"><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='h-4 w-4'><path d='M18 6 6 18M6 6l12 12'/></svg></button>
        </div>
    </template>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            addToast(message, type = 'success') {
                const id = Date.now() + Math.random();
                this.toasts.push({ id, message, type, visible: true });
                setTimeout(() => this.removeToast(id), 3000);
            },
            removeToast(id) {
                const toast = this.toasts.find((t) => t.id === id);
                if (!toast) return;
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter((t) => t.id !== id);
                }, 200);
            },
        };
    }
</script>
