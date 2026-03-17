<?php

use Livewire\Volt\Component;
use App\Models\Shop;
use App\Models\Product;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public $tab = 'shops'; // 'shops' or 'products'

    public function approveShop($shopId)
    {
        $shop = Shop::findOrFail($shopId);
        $shop->update([
            'is_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('toast', 
            text: "Shop '{$shop->name}' has been approved.",
            heading: 'Shop Approved',
            variant: 'success'
        );
    }

    public function approveProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_approved' => true]);

        $this->dispatch('toast', 
            text: "Product '{$product->name}' has been approved.",
            heading: 'Product Approved',
            variant: 'success'
        );
    }

    public function with()
    {
        $user = Auth::user();
        $isMaster = $user->hasRole('masteradmin');
        $state = $user->state;

        $shops = Shop::query();
        $products = Product::query()->with('shop');

        if (!$isMaster) {
            $shops->where('state', $state);
            $products->whereHas('shop', fn($q) => $q->where('state', $state));
        }

        return [
            'pendingShops' => $shops->where('is_approved', false)->latest()->paginate(10, ['*'], 'shopsPage'),
            'pendingProducts' => $products->where('is_approved', false)->latest()->paginate(10, ['*'], 'productsPage'),
        ];
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center gap-2 p-1 bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl w-fit">
        <button 
            type="button" 
            wire:click="$set('tab', 'shops')" 
            class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $tab === 'shops' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:bg-brand-50' }}"
        >
            <flux:icon name="building-storefront" class="size-4" />
            Pending Shops
        </button>
        <button 
            type="button" 
            wire:click="$set('tab', 'products')" 
            class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $tab === 'products' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:bg-brand-50' }}"
        >
            <flux:icon name="shopping-bag" class="size-4" />
            Pending Products
        </button>
    </div>

    <div class="mt-6">
        @if($tab === 'shops')
            <div class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl overflow-hidden shadow-sm">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Shop Details</flux:table.column>
                        <flux:table.column>Location</flux:table.column>
                        <flux:table.column>Owner</flux:table.column>
                        <flux:table.column align="end">Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($pendingShops as $shop)
                            <flux:table.row :key="'shop-'.$shop->id">
                                <flux:table.cell>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ $shop->name }}</span>
                                        <span class="text-xs text-brand-500 font-medium">{{ $shop->slug }}.{{ env('APP_DOMAIN', 'localhost') }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" color="zinc" inset="top" class="uppercase">{{ $shop->state }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="text-sm font-medium">{{ $shop->user->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $shop->user->email }}</div>
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" variant="subtle" href="http://{{ $shop->slug }}.{{ env('APP_DOMAIN', 'localhost') }}:8000" target="_blank" icon="eye">Preview</flux:button>
                                        <flux:button size="sm" variant="primary" wire:click="approveShop({{ $shop->id }})" wire:confirm="Approve this shop for public listing?">Approve</flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="py-20 text-center">
                                    <flux:heading size="lg">No pending shops</flux:heading>
                                    <flux:subheading>All shop applications have been processed.</flux:subheading>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
                @if($pendingShops->hasPages())
                    <div class="p-4 border-t border-brand-100 dark:border-brand-800">
                        {{ $pendingShops->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl overflow-hidden shadow-sm">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Product</flux:table.column>
                        <flux:table.column>Shop</flux:table.column>
                        <flux:table.column>Price</flux:table.column>
                        <flux:table.column align="end">Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($pendingProducts as $product)
                            <flux:table.row :key="'product-'.$product->id">
                                <flux:table.cell>
                                    <div class="flex items-center gap-3">
                                        @if($product->image_path)
                                            <img src="{{ asset('storage/'.$product->image_path) }}" class="size-10 rounded-lg object-cover" />
                                        @else
                                            <div class="size-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                                <flux:icon name="photo" class="size-5" />
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ $product->name }}</span>
                                            <span class="text-xs text-gray-400 truncate max-w-[200px]">{{ $product->description }}</span>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="text-sm font-medium">{{ $product->shop->name }}</span>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="font-black text-hub-green">₦{{ number_format($product->price) }}</span>
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <flux:button size="sm" variant="primary" wire:click="approveProduct({{ $product->id }})" wire:confirm="Approve this product for the marketplace?">Approve</flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="py-20 text-center">
                                    <flux:heading size="lg">No pending products</flux:heading>
                                    <flux:subheading>All product submissions have been reviewed.</flux:subheading>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
                @if($pendingProducts->hasPages())
                    <div class="p-4 border-t border-brand-100 dark:border-brand-800">
                        {{ $pendingProducts->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
