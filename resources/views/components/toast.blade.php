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
        @if (session()->has('warning'))
            addToast(@js(session('warning')), 'warning')
        @endif
    "
    @toast.window="addToast($event.detail.message, $event.detail.type)"
    class="fixed right-6 top-6 z-50 flex w-88 flex-col gap-3"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            :class="{
                'bg-brutal-neon text-black': toast.type === 'success',
                'bg-brutal-pink text-white': toast.type === 'error',
                'bg-brutal-yellow text-black': toast.type === 'warning' || toast.type === 'info'
            }"
            class="flex flex-col border-3 border-black p-4 shadow-brutal-lg transition-transform duration-75 ease-linear"
        >
            <div class="flex items-center justify-between border-b-2 border-black/30 pb-1.5 font-mono text-[11px] font-black uppercase tracking-wider">
                <span x-text="toast.type === 'error' ? '🚨 SYSTEM ERROR' : (toast.type === 'success' ? '⚡ TRANSACTION SUCCESS' : '⚠️ SYSTEM ALERT')"></span>
                <button @click="removeToast(toast.id)" class="border border-black bg-black px-1.5 py-0.5 text-[9px] font-black text-white hover:bg-white hover:text-black">[X]</button>
            </div>
            <p x-text="toast.message" class="mt-2 font-mono text-xs font-bold leading-relaxed"></p>
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
                setTimeout(() => this.removeToast(id), 4000);
            },
            removeToast(id) {
                const toast = this.toasts.find((t) => t.id === id);
                if (!toast) return;
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter((t) => t.id !== id);
                }, 100);
            },
        };
    }
</script>

