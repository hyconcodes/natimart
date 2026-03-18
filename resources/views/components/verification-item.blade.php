@props(['label', 'file' => null, 'value' => null])

<div class="space-y-2">
    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $label }}</div>
    <div
        class="bg-brand-50 dark:bg-brand-950 p-4 rounded-2xl border border-brand-100 dark:border-brand-800 flex items-center justify-between group overflow-hidden relative">
        @if ($file)
            <div class="flex items-center gap-3">
                <div
                    class="size-8 bg-brand-100 dark:bg-brand-900 rounded-lg flex items-center justify-center text-brand-600">
                    <flux:icon name="document" class="size-4" />
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold text-gray-900 dark:text-gray-100 truncate max-w-[120px]">
                        {{ basename($file) }}</div>
                    <div class="text-[9px] text-gray-400">PDF / Image</div>
                </div>
            </div>
            <flux:button size="xs" variant="ghost" href="{{ asset('storage/' . $file) }}" target="_blank"
                class="hover:bg-brand-100">
                View
            </flux:button>
        @elseif($value)
            <div class="text-xs font-black text-brand-600 italic tracking-wide truncate max-w-full">
                {{ $value }}
            </div>
        @else
            <div class="text-xs font-bold text-red-400 flex items-center gap-2">
                <flux:icon name="exclamation-circle" class="size-3" />
                Missing
            </div>
        @endif
    </div>
</div>
