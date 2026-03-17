<?php

use Livewire\Volt\Component;
use App\Models\Shop;
use App\Models\Product;
use App\Models\User;

new class extends Component {
    public function with()
    {
        return [
            'totalVendors' => Shop::count(),
            'activeProducts' => Product::where('is_approved', true)->count(),
            'pendingVendors' => Shop::where('is_approved', false)->count(),
            'totalUsers' => User::count(),
            'recentApprovals' => Shop::where('is_approved', true)
                ->with('user')
                ->latest('approved_at')
                ->take(4)
                ->get(),
        ];
    }
}; ?>

<div class="space-y-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-hub-green dark:text-hub-accent">
                <flux:icon name="building-storefront" class="size-6" />
            </div>
            <div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Vendors</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $totalVendors }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-blue-600 dark:text-blue-400">
                <flux:icon name="shopping-bag" class="size-6" />
            </div>
            <div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Products</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $activeProducts }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-amber-500 dark:text-amber-400">
                <flux:icon name="clock" class="size-6" />
            </div>
            <div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Shop</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $pendingVendors }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-purple-600 dark:text-purple-400">
                <flux:icon name="users" class="size-6" />
            </div>
            <div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">System Users</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $totalUsers }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-brand-900 rounded-3xl border border-brand-200 dark:border-brand-800 overflow-hidden h-[400px]">
                <div class="p-6 border-b border-brand-100 dark:border-brand-800 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-gray-100">Global Market Performance</h3>
                </div>
                <div class="relative h-full flex items-center justify-center">
                     <x-placeholder-pattern class="absolute inset-0 size-full stroke-brand-900/10 dark:stroke-brand-100/10" />
                     <span class="relative text-gray-400 font-medium">Global analytics visualization</span>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-brand-900 rounded-3xl border border-brand-200 dark:border-brand-800 overflow-hidden h-full">
                <div class="p-6 border-b border-brand-100 dark:border-brand-800">
                    <h3 class="font-bold text-gray-900 dark:text-gray-100">Recent System Activity</h3>
                </div>
                <div class="p-6 space-y-6">
                    @forelse($recentApprovals as $shop)
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-xl bg-brand-50 dark:bg-brand-950 flex-shrink-0 flex items-center justify-center">
                                <flux:icon name="check-badge" class="size-5 text-hub-green" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">{{ $shop->name }}</div>
                                <div class="text-xs text-gray-400">Approved globally</div>
                            </div>
                            <div class="text-xs text-gray-500 whitespace-nowrap">{{ $shop->approved_at->diffForHumans(null, true) }}</div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <flux:text class="text-gray-400">No recent activity</flux:text>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
