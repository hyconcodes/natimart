<x-layouts::app :title="__('Coordinator Dashboard')">
    <div class="space-y-8">
        <!-- Header Section -->
        <div>
            <flux:heading size="xl" level="1">State Coordination</flux:heading>
            <flux:subheading>Manage and approve vendor clusters in your region.</flux:subheading>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-hub-green dark:text-hub-accent">
                    <flux:icon name="building-storefront" class="size-6" />
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Vendors In State</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-gray-100">45</div>
                </div>
            </div>

            <div class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-amber-500 dark:text-amber-400">
                    <flux:icon name="clock" class="size-6" />
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Approvals</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-gray-100">12</div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Large Chart/Placeholder -->
                <div class="bg-white dark:bg-brand-900 rounded-3xl border border-brand-200 dark:border-brand-800 overflow-hidden h-[400px]">
                    <div class="p-6 border-b border-brand-100 dark:border-brand-800 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100">Regional Performance</h3>
                    </div>
                    <div class="relative h-full flex items-center justify-center">
                         <x-placeholder-pattern class="absolute inset-0 size-full stroke-brand-900/10 dark:stroke-brand-100/10" />
                         <span class="relative text-gray-400 font-medium">Regional analytics here</span>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Recent Activity -->
                <div class="bg-white dark:bg-brand-900 rounded-3xl border border-brand-200 dark:border-brand-800 overflow-hidden h-full">
                    <div class="p-6 border-b border-brand-100 dark:border-brand-800">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100">Actions needed</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        @for($i = 1; $i <= 3; $i++)
                            <div class="flex items-center gap-4">
                                <div class="size-10 rounded-xl bg-amber-50 flex-shrink-0 flex items-center justify-center border border-amber-200">
                                    <flux:icon name="exclamation-circle" class="size-5 text-amber-500" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">Aba Leather Works</div>
                                    <div class="text-xs text-gray-400">Needs your approval</div>
                                </div>
                                <div class="text-xs text-amber-600 font-bold whitespace-nowrap">
                                    <a href="{{ route('admin.vendors') }}">Review</a>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
