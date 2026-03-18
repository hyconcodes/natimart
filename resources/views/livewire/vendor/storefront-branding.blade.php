<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $logo;
    public $primaryColor = '#15803d';
    public $shop;

    // Product form
    public $productName = '';
    public $productPrice = '';
    public $productDescription = '';
    public $productImage;
    public $isAddingProduct = false;

    public function mount()
    {
        $this->shop = Auth::user()->shop;
        $this->primaryColor = $this->shop->primary_color ?? '#15803d';
    }

    public function saveBranding()
    {
        $this->validate([
            'logo' => 'nullable|image|max:1024',
            'primaryColor' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        $data = ['primary_color' => $this->primaryColor];

        if ($this->logo) {
            $data['logo_path'] = $this->logo->store('logos', 'public');
        }

        $this->shop->update($data);
        $this->dispatch('toast', text: 'Storefront branding updated successfully.', variant: 'success');
    }
}; ?>

<div class="space-y-10 pb-20">
    <div class="flex justify-between items-end">
        <div>
            <flux:heading size="xl">Storefront Identity</flux:heading>
            <flux:subheading>Customize your storefront look and color scheme.</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Branding Options -->
        <div class="lg:col-span-1 space-y-6">
            <div
                class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-200 dark:border-brand-800 shadow-sm space-y-6">
                <flux:heading size="lg">Identity & Colors</flux:heading>

                <form wire:submit="saveBranding" class="space-y-6">
                    <!-- Logo Upload -->
                    <div class="space-y-3">
                        <flux:label>Business Logo</flux:label>
                        <div class="flex items-center gap-4">
                            <div
                                class="size-20 rounded-2xl border-2 border-dashed border-brand-200 dark:border-brand-800 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-brand-950">
                                @if ($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif ($shop->logo_path)
                                    <img src="{{ asset('storage/' . $shop->logo_path) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <flux:icon name="photo" class="size-8 text-gray-300" />
                                @endif
                            </div>
                            <div class="flex-1">
                                <flux:input type="file" wire:model="logo" size="sm" />
                                <flux:text size="xs" class="mt-1">PNG, JPG up to 1MB</flux:text>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Color -->
                    <div class="space-y-3">
                        <flux:label>Brand Primary Color</flux:label>
                        <div class="flex items-center gap-3">
                            <input type="color" wire:model.live="primaryColor"
                                class="size-12 rounded-lg cursor-pointer border-0 p-0 bg-transparent">
                            <flux:input wire:model.live="primaryColor" class="flex-1 font-mono" />
                        </div>
                        <flux:text size="xs">This color will be used for buttons and accents on your storefront.
                        </flux:text>
                    </div>

                    <flux:button type="submit" variant="primary" class="w-full">Save Branding</flux:button>
                </form>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-brand-900 p-8 rounded-3xl border border-brand-200 dark:border-brand-800 shadow-sm space-y-6">
                <flux:heading size="sm" class="uppercase tracking-widest text-[10px] text-gray-400">Storefront Live
                    Preview</flux:heading>

                <div
                    class="rounded-[2.5rem] border-[8px] border-gray-900 dark:border-brand-800 overflow-hidden bg-gray-50 dark:bg-brand-950 shadow-2xl relative aspect-[16/10] max-w-lg mx-auto">
                    <!-- Preview Header -->
                    <div class="bg-white dark:bg-brand-900 h-14 border-b border-brand-100 flex items-center px-4 gap-3">
                        <div
                            class="size-8 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden border border-brand-100">
                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif ($shop->logo_path)
                                <img src="{{ asset('storage/' . $shop->logo_path) }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="font-bold text-[10px]" style="color: {{ $primaryColor }}">NB</div>
                            @endif
                        </div>
                        <div class="h-2 w-24 bg-gray-100 rounded-full"></div>
                        <div class="ml-auto size-6 rounded-full" style="background-color: {{ $primaryColor }}"></div>
                    </div>

                    <!-- Preview Body -->
                    <div class="p-6 space-y-4">
                        <div class="h-4 w-1/3 bg-gray-200 dark:bg-brand-800 rounded-full"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="aspect-square bg-white dark:bg-brand-900 rounded-2xl border border-brand-100 p-2 space-y-2 group">
                                <div class="h-20 bg-gray-50 dark:bg-brand-800 rounded-xl"></div>
                                <div class="h-2 w-1/2 bg-gray-100 rounded-full"></div>
                                <div class="h-4 w-1/3 bg-blue-50 rounded-full"
                                    style="background-color: color-mix(in srgb, {{ $primaryColor }}, white 90%); border: 1px solid {{ $primaryColor }}">
                                </div>
                            </div>
                            <div
                                class="aspect-square bg-white dark:bg-brand-900 rounded-2xl border border-brand-100 p-2 space-y-2">
                                <div class="h-20 bg-gray-50 dark:bg-brand-800 rounded-xl"></div>
                                <div class="h-2 w-1/2 bg-gray-100 rounded-full"></div>
                                <div class="h-4 w-1/3 bg-blue-50 rounded-full"
                                    style="background-color: color-mix(in srgb, {{ $primaryColor }}, white 90%); border: 1px solid {{ $primaryColor }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Label Overlay -->
                    <div class="absolute inset-0 bg-brand-50/10 flex items-center justify-center pointer-events-none">
                        <div class="rotate-[-15deg] font-black text-4xl text-gray-200/40 uppercase tracking-tighter">
                            Live Preview</div>
                    </div>
                </div>

                <div class="text-center pt-4">
                    <flux:button :href="route('vendor.store', ['shop_slug' => $shop->slug])" target="_blank"
                        variant="subtle" icon-trailing="arrow-top-right-on-square">View Real Storefront</flux:button>
                </div>
            </div>
        </div>
    </div>
</div>
