<?php

use App\Models\PricingPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;

new class extends Component
{
    public $plans;

    public $shop;

    public function mount()
    {
        $this->plans = PricingPlan::where('is_active', true)->get();
        $this->shop = Auth::user()->shop;
    }

    public function selectPlan($planId)
    {
        $plan = PricingPlan::findOrFail($planId);

        // Direct DB update to guarantee persistence
        DB::table('shops')
            ->where('id', $this->shop->id)
            ->update([
                'pricing_plan_id' => $plan->id,
                'subscription_status' => 'active',
                'subscription_expires_at' => $plan->monthly_price > 0 ? now()->addMonth() : null,
                'updated_at' => now(),
            ]);

        $this->dispatch('toast', text: "Plan updated to {$plan->name}!", variant: 'success');

        return redirect()->route('vendor.dashboard', ['shop_slug' => $this->shop->slug]);
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="text-center mb-16 px-4">
        <flux:heading size="xl" class="mb-4 text-3xl md:text-5xl font-black tracking-tight">Fuel Your Growth
        </flux:heading>
        @if (!$shop->pricing_plan_id)
            <flux:subheading class="max-w-2xl mx-auto text-base md:text-lg">Welcome to NBTI Market Hub! Please select a
                plan to continue to your dashboard.</flux:subheading>
        @else
            <flux:subheading class="max-w-2xl mx-auto text-base md:text-lg">Upgrade your plan to unlock more products and
                premium placement on the hub.</flux:subheading>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
        @foreach ($plans as $plan)
            @php
                $isCurrent = $shop->pricing_plan_id === $plan->id;
            @endphp
            <div class="relative group h-full">
                <!-- Sparkle effect for Enterprise -->
                @if ($plan->slug === 'enterprise-gold' && !$isCurrent)
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-amber-400 to-yellow-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
                    </div>
                @endif

                <div
                    class="relative h-full bg-white dark:bg-brand-900 border {{ $isCurrent ? 'border-hub-green/50 ring-4 ring-hub-green/5' : 'border-brand-200 dark:border-brand-800' }} rounded-[2rem] p-8 flex flex-col shadow-sm hover:shadow-xl transition-all duration-300 {{ $isCurrent ? 'opacity-75 grayscale-[0.5]' : '' }}">
                    @if ($isCurrent)
                        <div
                            class="absolute inset-0 bg-white/10 dark:bg-brand-950/20 backdrop-blur-[2px] rounded-[2rem] flex items-center justify-center z-10">
                            <flux:badge color="green" size="sm"
                                class="uppercase tracking-widest font-black shadow-lg">Your Current Plan</flux:badge>
                        </div>
                    @endif

                    <div class="mb-8">
                        @if ($plan->slug === 'enterprise-gold')
                            <flux:badge color="amber" size="sm" class="mb-4 uppercase tracking-widest font-black">
                                Most Popular</flux:badge>
                        @endif
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">{{ $plan->name }}</h3>
                        <div class="flex items-baseline gap-1 mt-4">
                            <span
                                class="text-4xl font-black text-gray-900 dark:text-white">₦{{ number_format($plan->monthly_price) }}</span>
                            <span class="text-gray-500 font-medium">/month</span>
                        </div>
                    </div>

                    <div class="space-y-4 mb-10 flex-grow">
                        @php $features = json_decode($plan->features) ?? []; @endphp
                        @foreach ($features as $feature)
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-1 size-5 rounded-full bg-brand-50 dark:bg-brand-800 flex items-center justify-center shrink-0">
                                    <flux:icon name="check" class="size-3 text-hub-green" variant="micro" />
                                </div>
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-300 font-medium">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if (!$isCurrent)
                        <flux:button wire:click="selectPlan({{ $plan->id }})"
                            variant="{{ $plan->slug === 'enterprise-gold' ? 'primary' : 'subtle' }}"
                            class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-xs">
                            {{ $shop->pricing_plan_id ? 'Upgrade to ' . $plan->name : 'Select ' . $plan->name }}
                        </flux:button>
                    @else
                        <div class="h-12"></div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div
        class="mt-20 text-center text-gray-500 max-w-lg mx-auto bg-white/50 dark:bg-brand-900/50 p-6 rounded-3xl border border-brand-100 dark:border-brand-800">
        <flux:text size="sm" class="italic">"We focus on supporting local incubation. These prices are designed to
            help you grow without being a barrier."</flux:text>
    </div>
</div>
