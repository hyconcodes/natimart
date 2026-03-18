<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public $shop;

    public $verification;

    public function mount()
    {
        $this->shop = Auth::user()->shop;
        $this->verification = $this->shop->verification;
    }
}; ?>

<div class="space-y-8">
    <!-- Header with Status -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <flux:heading size="xl" level="1">Verification Review</flux:heading>
            <flux:subheading>Review your submitted documents and verification status.</flux:subheading>
        </div>

        @if ($shop->is_approved)
            <div
                class="bg-hub-green/10 border border-hub-green/20 rounded-2xl px-6 py-3 flex items-center gap-3 text-hub-green">
                <flux:icon name="check-circle" class="size-6" />
                <span class="font-black uppercase tracking-widest text-sm italic">Approved & Live</span>
            </div>
        @else
            <div
                class="bg-amber-50 border border-amber-200 rounded-2xl px-6 py-3 flex items-center gap-3 text-amber-600">
                <flux:icon name="clock" class="size-6" />
                <span class="font-black uppercase tracking-widest text-sm italic">Pending Review</span>
            </div>
        @endif
    </div>

    @if (!$verification)
        <div
            class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-[2.5rem] p-12 text-center">
            <div
                class="size-20 bg-brand-50 dark:bg-brand-950 rounded-full flex items-center justify-center mx-auto mb-6">
                <flux:icon name="document-text" class="size-10 text-brand-400" />
            </div>
            <h3 class="text-2xl font-black italic mb-2">No Documents Found</h3>
            <p class="text-gray-500 max-w-sm mx-auto mb-8">You haven't started the verification process yet. Please head
                to the submission page to upload your credentials.</p>
            <flux:button href="{{ route('vendor.verification', ['shop_slug' => $shop->slug]) }}" variant="primary"
                icon="arrow-up-tray" wire:navigate>
                Go to Upload Page
            </flux:button>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Summary Card -->
            <div class="lg:col-span-1 space-y-6">
                <div
                    class="bg-brand-950 rounded-[2.5rem] p-8 text-white border-2 border-brand-800 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <flux:icon name="shield-check" class="size-32" />
                    </div>
                    <div class="relative z-10">
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-brand-500 mb-2">Registration
                            Status</div>
                        <h4 class="text-3xl font-black italic mb-6">NBTI Hub Verification</h4>

                        <div class="space-y-4">
                            @php
                                $status = $verification->verification_status ?? [];
                                $steps = [
                                    ['id' => 'identity', 'label' => 'Business Identity'],
                                    ['id' => 'quality', 'label' => 'Product Safety'],
                                    ['id' => 'operations', 'label' => 'Operational Ops'],
                                    ['id' => 'marketing', 'label' => 'Digital Assets'],
                                ];
                            @endphp

                            @foreach ($steps as $step)
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-6 rounded-full flex items-center justify-center {{ isset($status[$step['id']]) ? 'bg-hub-green' : 'bg-brand-800' }}">
                                        @if (isset($status[$step['id']]))
                                            <flux:icon name="check" class="size-3 text-white" />
                                        @else
                                            <div class="size-1.5 rounded-full bg-brand-600"></div>
                                        @endif
                                    </div>
                                    <span
                                        class="text-xs font-bold {{ isset($status[$step['id']]) ? 'text-white' : 'text-brand-500' }}">{{ $step['label'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 pt-8 border-t border-brand-800">
                            <flux:button href="{{ route('vendor.verification', ['shop_slug' => $shop->slug]) }}"
                                variant="subtle" full-width
                                class="!bg-brand-900 !text-white !border-brand-700 font-black italic" wire:navigate>
                                Update Information
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Grid -->
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-[2.5rem] overflow-hidden shadow-sm">
                    <div class="p-8 border-b border-brand-100 dark:border-brand-800">
                        <h3 class="font-black italic text-gray-900 dark:text-gray-100">Submitted Credentials</h3>
                    </div>

                    <div class="divide-y divide-brand-100 dark:divide-brand-800">
                        <!-- Identity Docs -->
                        <div class="p-8">
                            <h5 class="text-[10px] font-black uppercase tracking-widest text-brand-600 mb-6">1. Business
                                Identity</h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <x-verification-item label="CAC Certificate" :file="$verification->cac_certificate" />
                                <x-verification-item label="CAC status report" :file="$verification->cac_status_report" />
                                <x-verification-item label="Tax ID (TIN)" :value="$verification->tin_number" />
                                <x-verification-item label="Owner ID Card" :file="$verification->owner_id_card" />
                            </div>
                        </div>

                        <!-- Quality Docs -->
                        <div class="p-8">
                            <h5 class="text-[10px] font-black uppercase tracking-widest text-brand-600 mb-6">2. Quality
                                & Safety</h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <x-verification-item label="NAFDAC ID" :value="$verification->nafdac_number" />
                                <x-verification-item label="SON / MANCAP" :file="$verification->son_mancap_cert" />
                                <x-verification-item label="Lab Report" :file="$verification->lab_test_report" />
                                <x-verification-item label="Trademark" :file="$verification->trademark_cert" />
                            </div>
                        </div>

                        <!-- Logistics -->
                        <div class="p-8">
                            <h5 class="text-[10px] font-black uppercase tracking-widest text-brand-600 mb-6">3.
                                Operational Data</h5>
                            <div class="grid grid-cols-1 gap-6">
                                <x-verification-item label="Physical Address" :value="$verification->production_address" />
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <x-verification-item label="Weekly Capacity" :value="$verification->production_capacity" />
                                    <x-verification-item label="Logistics MOU" :file="$verification->logistics_sla" />
                                </div>
                            </div>
                        </div>

                        <!-- Marketing Assets -->
                        <div class="p-8">
                            <h5 class="text-[10px] font-black uppercase tracking-widest text-brand-600 mb-6">4. Digital
                                & Marketing Assets</h5>
                            <div class="grid grid-cols-1 gap-6">
                                <x-verification-item label="Product Descriptions" :value="$verification->product_descriptions_standard" />
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <x-verification-item label="Product Photos Sample" :file="$verification->product_images_sample" />
                                    <x-verification-item label="Price List (Wholesale/Retail)" :file="$verification->pricing_list" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
