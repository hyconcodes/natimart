<?php

use Livewire\Volt\Component;
use App\Models\Shop;
use App\Models\Product;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public $tab = 'shops'; // 'shops' or 'products'
    public $viewingShop = null;
    public $viewingProduct = null;
    public $selectedState = '';

    public function approveShop($shopId)
    {
        $shop = Shop::findOrFail($shopId);
        $shop->update([
            'is_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('toast', text: "Shop '{$shop->name}' has been approved.", heading: 'Shop Approved', variant: 'success');
    }

    public function approveProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_approved' => true]);

        $this->dispatch('toast', text: "Product '{$product->name}' has been approved.", heading: 'Product Approved', variant: 'success');
    }

    public function showShopDetails($shopId)
    {
        $this->viewingShop = Shop::with('verification')->findOrFail($shopId);
        $this->dispatch('modal-show', name: 'shop-details-modal');
    }

    public function closeShopDetails()
    {
        $this->viewingShop = null;
        $this->dispatch('modal-close', name: 'shop-details-modal');
    }

    public function showProductDetails($productId)
    {
        $this->viewingProduct = Product::with('shop')->findOrFail($productId);
        $this->dispatch('modal-show', name: 'product-details-modal');
    }

    public function closeProductDetails()
    {
        $this->viewingProduct = null;
        $this->dispatch('modal-close', name: 'product-details-modal');
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
        } elseif ($this->selectedState) {
            $shops->where('state', $this->selectedState);
            $products->whereHas('shop', fn($q) => $q->where('state', $this->selectedState));
        }

        return [
            'pendingShops' => $shops
                ->where('is_approved', false)
                ->with('verification', 'user')
                ->latest()
                ->paginate(10, ['*'], 'shopsPage'),
            'pendingProducts' => $products
                ->where('is_approved', false)
                ->latest()
                ->paginate(10, ['*'], 'productsPage'),
        ];
    }
}; ?>

<div class="space-y-6">
    <div
        class="flex items-center gap-2 p-1 bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl w-fit">
        <button type="button" wire:click="$set('tab', 'shops')"
            class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $tab === 'shops' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:bg-brand-50' }}">
            <flux:icon name="building-storefront" class="size-4" />
            Pending Shops
        </button>
        <button type="button" wire:click="$set('tab', 'products')"
            class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $tab === 'products' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:bg-brand-50' }}">
            <flux:icon name="shopping-bag" class="size-4" />
            Pending Products
        </button>

        @if (auth()->user()->hasRole('masteradmin'))
            <div class="ms-4 border-s border-brand-100 ps-4">
                <flux:select wire:model.live="selectedState" size="sm" placeholder="Filter by State"
                    class="w-48">
                    <flux:select.option value="">All States</flux:select.option>
                    @php
                        $states = [
                            'abia',
                            'adamawa',
                            'akwa_ibom',
                            'anambra',
                            'bauchi',
                            'bayelsa',
                            'benue',
                            'borno',
                            'cross_river',
                            'delta',
                            'ebonyi',
                            'edo',
                            'ekiti',
                            'enugu',
                            'fct_abuja',
                            'gombe',
                            'imo',
                            'jigawa',
                            'kaduna',
                            'kano',
                            'katsina',
                            'kebbi',
                            'kogi',
                            'kwara',
                            'lagos',
                            'nasarawa',
                            'niger',
                            'ogun',
                            'ondo',
                            'osun',
                            'oyo',
                            'plateau',
                            'rivers',
                            'sokoto',
                            'taraba',
                            'yobe',
                            'zamfara',
                        ];
                    @endphp
                    @foreach ($states as $state)
                        <flux:select.option :value="$state">{{ ucfirst(str_replace('_', ' ', $state)) }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        @endif
    </div>

    <div class="mt-6">
        @if ($tab === 'shops')
            <div
                class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl overflow-hidden shadow-sm">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Shop Details</flux:table.column>
                        <flux:table.column>Location</flux:table.column>
                        <flux:table.column>Progress</flux:table.column>
                        <flux:table.column>Owner</flux:table.column>
                        <flux:table.column align="end">Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($pendingShops as $shop)
                            <flux:table.row :key="'shop-'.$shop->id">
                                <flux:table.cell>
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-gray-900 dark:text-gray-100">{{ $shop->name }}</span>
                                        <span
                                            class="text-xs text-brand-500 font-medium">{{ $shop->slug }}.{{ env('APP_DOMAIN', 'localhost') }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" color="zinc" inset="top" class="uppercase">
                                        {{ $shop->state }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        @if ($shop->verification)
                                            @php
                                                $status = $shop->verification->verification_status ?? [];
                                                $completed = count(array_filter($status, fn($s) => $s === 'completed'));
                                                $progress = floor(($completed / 4) * 100);
                                            @endphp
                                            <div class="flex flex-col gap-1 w-20">
                                                <div
                                                    class="w-full bg-gray-100 dark:bg-brand-950 h-1.5 rounded-full overflow-hidden border border-brand-100 dark:border-brand-800">
                                                    <div class="bg-brand-600 h-full"
                                                        style="width: {{ $progress }}%"></div>
                                                </div>
                                                <span class="text-[9px] font-black text-brand-500">{{ $progress }}%
                                                    Complete</span>
                                            </div>
                                        @else
                                            <flux:badge size="sm" color="red">0% Submitted</flux:badge>
                                        @endif
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="text-sm font-medium">{{ $shop->user->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $shop->user->email }}</div>
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="xs" variant="subtle"
                                            wire:click="showShopDetails({{ $shop->id }})" icon="document-text">
                                            Review Docs</flux:button>
                                        <flux:button size="xs" variant="primary"
                                            wire:click="approveShop({{ $shop->id }})"
                                            wire:confirm="Approve this shop for public listing?"
                                            :disabled="!$shop->verification">Approve</flux:button>
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
                @if ($pendingShops->hasPages())
                    <div class="p-4 border-t border-brand-100 dark:border-brand-800">
                        {{ $pendingShops->links() }}
                    </div>
                @endif
            </div>
        @else
            <div
                class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl overflow-hidden shadow-sm">
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
                                        @if ($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}"
                                                class="size-10 rounded-lg object-cover" />
                                        @else
                                            <div
                                                class="size-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                                <flux:icon name="photo" class="size-5" />
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold text-gray-900 dark:text-gray-100">{{ $product->name }}</span>
                                            <span
                                                class="text-xs text-gray-400 truncate max-w-[200px]">{{ $product->description }}</span>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="text-sm font-medium">{{ $product->shop->name }}</span>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span
                                        class="font-black text-hub-green">₦{{ number_format($product->price) }}</span>
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="xs" variant="subtle"
                                            wire:click="showProductDetails({{ $product->id }})" icon="eye">
                                            View Details</flux:button>
                                        <flux:button size="sm" variant="primary"
                                            wire:click="approveProduct({{ $product->id }})"
                                            wire:confirm="Approve this product for the marketplace?">Approve</flux:button>
                                    </div>
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
                @if ($pendingProducts->hasPages())
                    <div class="p-4 border-t border-brand-100 dark:border-brand-800">
                        {{ $pendingProducts->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Shop Details Modal -->
    <flux:modal name="shop-details-modal" class="w-full max-w-4xl">
        @if ($viewingShop)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Vendor Verification Checklist</flux:heading>
                    <flux:subheading>Review submitted documents for <strong>{{ $viewingShop->name }}</strong>
                    </flux:subheading>
                </div>

                @if (!$viewingShop->verification)
                    <div class="p-6 bg-amber-50 rounded-2xl border border-amber-200 text-amber-800 text-center">
                        <flux:icon name="exclamation-triangle" class="size-8 mx-auto mb-2 text-amber-500" />
                        <p class="font-bold">No documents submitted yet.</p>
                        <p class="text-sm">This vendor hasn't uploaded any verification data yet.</p>
                    </div>
                @else
                    <div
                        class="bg-white dark:bg-brand-950 border border-brand-100 dark:border-brand-800 rounded-2xl overflow-hidden shadow-inner">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-brand-50 dark:bg-brand-900">
                                <tr>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-[10px]">Document/Data
                                    </th>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-[10px]">Value / Status
                                    </th>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-[10px] text-right">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-brand-100 dark:divide-brand-800">
                                <!-- 1. Business Identity -->
                                <tr class="bg-gray-50/50 dark:bg-brand-900/30">
                                    <td colspan="3"
                                        class="px-4 py-2 text-[10px] font-black uppercase text-brand-600">1. Business
                                        Identity</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">CAC Certificate</td>
                                    <td class="px-4 py-3">
                                        @if ($viewingShop->verification->cac_certificate)
                                            <flux:badge size="sm" color="green" icon="check">Uploaded
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="red" icon="x-mark">Missing
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($viewingShop->verification->cac_certificate)
                                            <flux:button size="xs" variant="ghost"
                                                href="{{ asset('storage/' . $viewingShop->verification->cac_certificate) }}"
                                                target="_blank">View</flux:button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">CAC status report</td>
                                    <td class="px-4 py-3">
                                        @if ($viewingShop->verification->cac_status_report)
                                            <flux:badge size="sm" color="green" icon="check">Uploaded
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="red" icon="x-mark">Missing
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($viewingShop->verification->cac_status_report)
                                            <flux:button size="xs" variant="ghost"
                                                href="{{ asset('storage/' . $viewingShop->verification->cac_status_report) }}"
                                                target="_blank">View</flux:button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Tax ID (TIN)</td>
                                    <td class="px-4 py-3 font-mono font-bold text-hub-green text-xs">
                                        {{ $viewingShop->verification->tin_number ?? 'Not Provided' }}
                                    </td>
                                    <td class="px-4 py-3 text-right"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Owner ID Card</td>
                                    <td class="px-4 py-3">
                                        @if ($viewingShop->verification->owner_id_card)
                                            <flux:badge size="sm" color="green" icon="check">Uploaded
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="red" icon="x-mark">Missing
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($viewingShop->verification->owner_id_card)
                                            <flux:button size="xs" variant="ghost"
                                                href="{{ asset('storage/' . $viewingShop->verification->owner_id_card) }}"
                                                target="_blank">View</flux:button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- 2. Quality & Safety -->
                                <tr class="bg-gray-50/50 dark:bg-brand-900/30">
                                    <td colspan="3"
                                        class="px-4 py-2 text-[10px] font-black uppercase text-brand-600">2. Quality &
                                        Safety</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">NAFDAC Number</td>
                                    <td class="px-4 py-3 font-mono font-bold text-blue-600 text-xs">
                                        {{ $viewingShop->verification->nafdac_number ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-right"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">SON / MANCAP Certificate</td>
                                    <td class="px-4 py-3">
                                        @if ($viewingShop->verification->son_mancap_cert)
                                            <flux:badge size="sm" color="green" icon="check">Uploaded
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="red" icon="x-mark">Missing
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($viewingShop->verification->son_mancap_cert)
                                            <flux:button size="xs" variant="ghost"
                                                href="{{ asset('storage/' . $viewingShop->verification->son_mancap_cert) }}"
                                                target="_blank">View</flux:button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Lab Test Report</td>
                                    <td class="px-4 py-3">
                                        @if ($viewingShop->verification->lab_test_report)
                                            <flux:badge size="sm" color="green" icon="check">Uploaded
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="red" icon="x-mark">Missing
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($viewingShop->verification->lab_test_report)
                                            <flux:button size="xs" variant="ghost"
                                                href="{{ asset('storage/' . $viewingShop->verification->lab_test_report) }}"
                                                target="_blank">View</flux:button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Trademark Certificate</td>
                                    <td class="px-4 py-3">
                                        @if ($viewingShop->verification->trademark_cert)
                                            <flux:badge size="sm" color="green" icon="check">Uploaded
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="zinc" icon="minus">Optional
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($viewingShop->verification->trademark_cert)
                                            <flux:button size="xs" variant="ghost"
                                                href="{{ asset('storage/' . $viewingShop->verification->trademark_cert) }}"
                                                target="_blank">View</flux:button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- 3. Operational -->
                                <tr class="bg-gray-50/50 dark:bg-brand-900/30">
                                    <td colspan="3"
                                        class="px-4 py-2 text-[10px] font-black uppercase text-brand-600">3.
                                        Operational Data</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Physical Address</td>
                                    <td class="px-4 py-3 text-[11px] italic text-gray-600 dark:text-gray-400">
                                        {{ $viewingShop->verification->production_address ?? 'Not Provided' }}
                                    </td>
                                    <td class="px-4 py-3 text-right"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Weekly Capacity</td>
                                    <td class="px-4 py-3 text-xs">
                                        {{ $viewingShop->verification->production_capacity ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-right"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Logistics SLA / MOU</td>
                                    <td class="px-4 py-3">
                                        @if ($viewingShop->verification->logistics_sla)
                                            <flux:badge size="sm" color="green" icon="check">Uploaded
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="red" icon="x-mark">Missing
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($viewingShop->verification->logistics_sla)
                                            <flux:button size="xs" variant="ghost"
                                                href="{{ asset('storage/' . $viewingShop->verification->logistics_sla) }}"
                                                target="_blank">View</flux:button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- 4. Marketing -->
                                <tr class="bg-gray-50/50 dark:bg-brand-900/30">
                                    <td colspan="3"
                                        class="px-4 py-2 text-[10px] font-black uppercase text-brand-600">4. Digital &
                                        Marketing Assets</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Product Descriptions</td>
                                    <td class="px-4 py-3 text-[11px] italic text-gray-600 dark:text-gray-400">
                                        @if ($viewingShop->verification->product_descriptions_standard)
                                            {{ str($viewingShop->verification->product_descriptions_standard)->limit(100) }}
                                        @else
                                            <span class="text-red-400">Missing</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Product Photo Samples</td>
                                    <td class="px-4 py-3">
                                        @if ($viewingShop->verification->product_images_sample)
                                            <flux:badge size="sm" color="green" icon="check">Uploaded
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="red" icon="x-mark">Missing
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($viewingShop->verification->product_images_sample)
                                            <flux:button size="xs" variant="ghost"
                                                href="{{ asset('storage/' . $viewingShop->verification->product_images_sample) }}"
                                                target="_blank">Download</flux:button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-xs">Wholesale/Retail Price List</td>
                                    <td class="px-4 py-3">
                                        @if ($viewingShop->verification->pricing_list)
                                            <flux:badge size="sm" color="green" icon="check">Uploaded
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="red" icon="x-mark">Missing
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($viewingShop->verification->pricing_list)
                                            <flux:button size="xs" variant="ghost"
                                                href="{{ asset('storage/' . $viewingShop->verification->pricing_list) }}"
                                                target="_blank">View File</flux:button>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-4 border-t border-brand-100 dark:border-brand-800">
                    <flux:button variant="ghost" wire:click="closeShopDetails">Close</flux:button>
                    @if ($viewingShop->verification)
                        <flux:button variant="primary" wire:click="approveShop({{ $viewingShop->id }})"
                            wire:confirm="Everything looks good? This will make the shop public.">Approve Shop Now
                        </flux:button>
                    @endif
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Product Details Modal -->
    <flux:modal name="product-details-modal" class="w-full max-w-2xl">
        @if ($viewingProduct)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Product Approval Review</flux:heading>
                    <flux:subheading>From <strong>{{ $viewingProduct->shop->name }}</strong></flux:subheading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @if ($viewingProduct->image_path)
                        <div class="aspect-square rounded-2xl overflow-hidden border border-brand-100 dark:border-brand-800">
                            <img src="{{ asset('storage/' . $viewingProduct->image_path) }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="aspect-square rounded-2xl bg-gray-50 flex items-center justify-center border border-dashed border-gray-200">
                             <flux:icon name="photo" class="size-12 text-gray-300" />
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <flux:text class="text-[10px] font-black uppercase tracking-widest text-brand-600">Product Name</flux:text>
                            <flux:heading size="md" class="mt-1">{{ $viewingProduct->name }}</flux:heading>
                        </div>

                        <div>
                            <flux:text class="text-[10px] font-black uppercase tracking-widest text-brand-600">Market Price</flux:text>
                            <div class="text-2xl font-black text-hub-green mt-1">₦{{ number_format($viewingProduct->price) }}</div>
                        </div>

                        <div>
                            <flux:text class="text-[10px] font-black uppercase tracking-widest text-brand-600">State of Origin</flux:text>
                            <flux:badge size="sm" color="zinc" class="mt-1 uppercase">{{ $viewingProduct->shop->state }}</flux:badge>
                        </div>
                    </div>
                </div>

                <div>
                    <flux:text class="text-[10px] font-black uppercase tracking-widest text-brand-600">Product Description</flux:text>
                    <div class="mt-2 p-4 bg-gray-50 dark:bg-brand-950 rounded-2xl text-sm border border-brand-100 dark:border-brand-800 italic text-gray-600 dark:text-gray-400">
                        {{ $viewingProduct->description ?: 'No description provided.' }}
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-brand-100 dark:border-brand-800">
                    <flux:button variant="ghost" wire:click="closeProductDetails">Close</flux:button>
                    <flux:button variant="primary" 
                        wire:click="approveProduct({{ $viewingProduct->id }})"
                        wire:confirm="Everything looks good? This product will be listed for sale.">Approve Product Now</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
