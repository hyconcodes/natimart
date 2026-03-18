<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $shop;

    // Product form
    public $productName = '';
    public $productPrice = '';
    public $productDescription = '';
    public $productImage;
    public $isAddingProduct = false;

    public function mount()
    {
        $this->shop = Auth::user()->shop->load('products');
    }

    public function saveProduct()
    {
        if (!$this->shop->is_approved) {
            $this->dispatch('toast', text: 'Your shop is not yet approved by a State Coordinator. You cannot add more products until approval.', variant: 'danger');
            return;
        }

        $this->validate([
            'productName' => 'required|string|max:255',
            'productPrice' => 'required|numeric|min:0',
            'productDescription' => 'nullable|string',
            'productImage' => 'required|image|max:2048',
        ]);

        $imagePath = $this->productImage->store('products', 'public');

        Product::create([
            'shop_id' => $this->shop->id,
            'name' => $this->productName,
            'slug' => Str::slug($this->productName),
            'description' => $this->productDescription,
            'price' => $this->productPrice,
            'image_path' => $imagePath,
            'is_approved' => false, // New products also need approval
        ]);

        $this->shop->load('products'); // Refresh list
        $this->reset(['productName', 'productPrice', 'productDescription', 'productImage', 'isAddingProduct']);
        $this->dispatch('toast', text: 'Product added successfully and is pending approval.', variant: 'success');
    }
}; ?>

<div class="space-y-6">
    <div
        class="bg-white dark:bg-brand-900 rounded-3xl border border-brand-200 dark:border-brand-800 shadow-sm overflow-hidden">
        <div
            class="p-6 border-b border-brand-100 dark:border-brand-800 flex justify-between items-center bg-gray-50/50 dark:bg-brand-950/50">
            <div>
                <flux:heading size="lg">Manage Products</flux:heading>
                <flux:subheading>Add new products to your digital catalog.</flux:subheading>
            </div>
            <flux:button icon="plus" variant="primary" size="sm" wire:click="$set('isAddingProduct', true)">Add New
                Product</flux:button>
        </div>

        @if ($isAddingProduct)
            <div class="p-6 bg-brand-50/30 dark:bg-brand-950/30 border-b border-brand-100 dark:border-brand-800">
                <form wire:submit="saveProduct" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <flux:input wire:model="productName" :label="__('Product Name')"
                            placeholder="e.g. Premium Hub Shea Butter" required />
                        <flux:input wire:model="productPrice" :label="__('Price (₦)')" type="number" required />
                        <flux:textarea wire:model="productDescription" :label="__('Description')" rows="4"
                            placeholder="Standardized description..." />
                    </div>

                    <div class="space-y-4">
                        <flux:label>Product Main Image</flux:label>
                        <div
                            class="aspect-video rounded-2xl border-2 border-dashed border-brand-200 dark:border-brand-800 flex flex-col items-center justify-center overflow-hidden bg-white dark:bg-brand-900 relative">
                            @if ($productImage)
                                <img src="{{ $productImage->temporaryUrl() }}" class="w-full h-full object-cover">
                            @else
                                <flux:icon name="photo" class="size-10 text-gray-200 mb-2" />
                                <flux:text size="xs">Click to upload image</flux:text>
                                <input type="file" wire:model="productImage"
                                    class="absolute inset-0 opacity-0 cursor-pointer">
                            @endif
                        </div>
                        <flux:text size="xs">High resolution, neutral background recommended.</flux:text>

                        <div class="flex gap-3 pt-4">
                            <flux:button type="submit" variant="primary" class="flex-1">Submit for Approval
                            </flux:button>
                            <flux:button variant="ghost" wire:click="$set('isAddingProduct', false)">Cancel
                            </flux:button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <div class="divide-y divide-brand-100 dark:divide-brand-800">
            @forelse($shop->products as $product)
                <div class="p-4 flex items-center gap-4 hover:bg-gray-50 dark:hover:bg-brand-950 transition-colors">
                    <div
                        class="size-16 rounded-xl overflow-hidden bg-gray-100 border border-brand-100 dark:border-brand-800">
                        @if ($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}"
                                class="w-full h-full object-cover">
                        @else
                            <flux:icon name="photo" class="size-6 text-gray-300 m-auto mt-5" />
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-gray-900 dark:text-gray-100 truncate">{{ $product->name }}</div>
                        <div class="text-xs text-gray-400">₦{{ number_format($product->price) }}</div>
                    </div>
                    <div>
                        @if ($product->is_approved)
                            <flux:badge size="sm" color="green" inset="top">Live</flux:badge>
                        @else
                            <flux:badge size="sm" color="amber" inset="top">Pending Review</flux:badge>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-20 text-center">
                    <flux:icon name="shopping-bag" class="size-12 mx-auto text-gray-200 mb-4" />
                    <flux:heading size="lg">No products yet</flux:heading>
                    <flux:subheading>Start building your catalog to reach customers.</flux:subheading>
                </div>
            @endforelse
        </div>
    </div>
</div>
