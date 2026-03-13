<div
    x-data="{
        toasts: [],
        show(event) {
            // Handle both Flux detail structure and standard Livewire event structure
            const detail = event.detail || {};
            const slots = detail.slots || detail;
            const dataset = detail.dataset || detail;
            
            const id = Date.now();
            const toast = {
                id,
                text: slots.text || detail.text || (typeof detail === 'string' ? detail : ''),
                heading: slots.heading || detail.heading || '',
                variant: dataset.variant || detail.variant || 'success',
                show: false
            };

            if (!toast.text && !toast.heading) return;

            this.toasts.push(toast);
            
            // Animation timing
            requestAnimationFrame(() => {
                setTimeout(() => {
                    const index = this.toasts.findIndex(t => t.id === id);
                    if (index > -1) this.toasts[index].show = true;
                }, 10);
            });

            // Auto-dismiss
            setTimeout(() => {
                this.dismiss(id);
            }, detail.duration || 5000);
        },
        dismiss(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index > -1) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 500);
            }
        },
        init() {
            // Check for session-based toasts on load
            @if(session('toast'))
                setTimeout(() => {
                    this.show({ 
                        detail: { 
                            text: '{{ session('toast.text') }}', 
                            heading: '{{ session('toast.heading') }}', 
                            variant: '{{ session('toast.variant', 'success') }}' 
                        } 
                    });
                }, 500);
            @endif
        }
    }"
    @toast-show.window="show($event)"
    @toast.window="show($event)"
    class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-3 w-[90%] sm:w-auto sm:min-w-[400px] pointer-events-none items-center"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 -translate-y-10 scale-90 blur-sm"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100 blur-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95 blur-sm"
            class="pointer-events-auto w-full bg-white/80 dark:bg-brand-900/80 backdrop-blur-xl border border-brand-200 dark:border-brand-800 shadow-[0_20px_50px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2rem] p-5 flex items-start gap-4 ring-1 ring-black/5 dark:ring-white/5"
        >
            <!-- Icon Container -->
            <div class="flex-shrink-0 mt-0.5">
                <template x-if="toast.variant === 'success'">
                    <div class="rounded-2xl bg-green-500/10 dark:bg-green-500/20 p-2 text-green-600 dark:text-hub-accent">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </template>
                <template x-if="toast.variant === 'danger' || toast.variant === 'error'">
                    <div class="rounded-2xl bg-red-500/10 dark:bg-red-500/20 p-2 text-red-600 dark:text-red-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                </template>
                <template x-if="toast.variant === 'warning'">
                    <div class="rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 p-2 text-amber-600 dark:text-amber-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                </template>
                <template x-if="!['success', 'danger', 'error', 'warning'].includes(toast.variant)">
                    <div class="rounded-2xl bg-brand-500/10 dark:bg-brand-500/20 p-2 text-brand-600 dark:text-brand-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                    </div>
                </template>
            </div>
            
            <!-- Text Content -->
            <div class="flex-1 min-w-0 pr-2">
                <h4 x-show="toast.heading" x-text="toast.heading" class="text-sm font-black text-gray-900 dark:text-gray-100 mb-0.5 tracking-tight uppercase"></h4>
                <p x-text="toast.text" class="text-sm text-gray-600 dark:text-gray-300 leading-snug font-bold"></p>
            </div>

            <!-- Close Button -->
            <button @click="dismiss(toast.id)" class="flex-shrink-0 text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-all p-1.5 -mr-1 hover:bg-black/5 dark:hover:bg-white/5 rounded-xl">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>
        </div>
    </template>
</div>
