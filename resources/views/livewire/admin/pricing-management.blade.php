<?php

use Livewire\Volt\Component;
use App\Models\PricingPlan;

new class extends Component {
    public $plans;
    public $editingPlan = null;

    // Form fields
    public $name = '';
    public $monthlyPrice = 0;
    public $annualPrice = 0;
    public $featuresText = '';

    public function mount()
    {
        $this->loadPlans();
    }

    public function loadPlans()
    {
        $this->plans = PricingPlan::all();
    }

    public function edit($id)
    {
        $plan = PricingPlan::findOrFail($id);
        $this->editingPlan = $plan->id;
        $this->name = $plan->name;
        $this->monthlyPrice = $plan->monthly_price;
        $this->annualPrice = $plan->annual_price;

        $features = json_decode($plan->features) ?? [];
        $this->featuresText = implode(', ', $features);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'monthlyPrice' => 'required|numeric|min:0',
            'annualPrice' => 'required|numeric|min:0',
            'featuresText' => 'nullable|string',
        ]);

        $plan = PricingPlan::findOrFail($this->editingPlan);

        // Convert comma-separated text back to JSON array
        $featuresArray = array_map('trim', explode(',', $this->featuresText));
        $featuresArray = array_filter($featuresArray); // Remove empty values

        $plan->update([
            'name' => $this->name,
            'monthly_price' => $this->monthlyPrice,
            'annual_price' => $this->annualPrice,
            'features' => json_encode(array_values($featuresArray)),
        ]);

        $this->editingPlan = null;
        $this->loadPlans();
        $this->dispatch('toast', text: 'Pricing plan updated successfully.', variant: 'success');
    }

    public function cancel()
    {
        $this->editingPlan = null;
    }
}; ?>

<div class="space-y-10">
    <div>
        <flux:heading size="xl">Platform Pricing</flux:heading>
        <flux:subheading>Manage vendor subscription plans and platform fees.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 gap-6">
        @foreach ($plans as $plan)
            <div
                class="bg-white dark:bg-brand-900 rounded-3xl border border-brand-200 dark:border-brand-800 p-8 shadow-sm">
                @if ($editingPlan === $plan->id)
                    <!-- Edit Mode -->
                    <form wire:submit="save" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <flux:input wire:model="name" label="Plan Name" required />
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input type="number" wire:model="monthlyPrice" label="Monthly (₦)" required />
                                <flux:input type="number" wire:model="annualPrice" label="Annual (₦)" required />
                            </div>
                        </div>

                        <flux:textarea wire:model="featuresText" label="Features (Comma separated)" rows="3"
                            placeholder="Feature 1, Feature 2, Feature 3..." />

                        <div class="flex gap-4">
                            <flux:button type="submit" variant="primary">Update Pricing</flux:button>
                            <flux:button wire:click="cancel" variant="ghost">Cancel</flux:button>
                        </div>
                    </form>
                @else
                    <!-- View Mode -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                        <div class="space-y-4 max-w-2xl">
                            <div>
                                <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                                <div class="flex gap-4 mt-2">
                                    <div class="text-sm font-bold text-hub-green uppercase tracking-widest">
                                        ₦{{ number_format($plan->monthly_price) }} / mo</div>
                                    <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">
                                        ₦{{ number_format($plan->annual_price) }} / yr</div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @php $features = json_decode($plan->features) ?? []; @endphp
                                @foreach ($features as $feature)
                                    <flux:badge size="sm" inset="top" color="indigo" class="font-bold">
                                        {{ $feature }}</flux:badge>
                                @endforeach
                            </div>
                        </div>

                        <flux:button wire:click="edit({{ $plan->id }})" variant="subtle" size="sm"
                            icon="pencil-square">Edit Pricing</flux:button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="bg-amber-50 dark:bg-amber-950/20 p-6 rounded-3xl border border-amber-100 dark:border-amber-900/50">
        <div class="flex gap-4">
            <div
                class="size-10 rounded-2xl bg-amber-100 dark:bg-amber-900 flex items-center justify-center shrink-0 mt-1">
                <flux:icon name="exclamation-triangle" class="size-5 text-amber-600 dark:text-amber-500" />
            </div>
            <div class="space-y-1">
                <flux:heading size="sm" class="text-amber-900 dark:text-amber-100">Managing Subscriptions
                </flux:heading>
                <flux:subheading size="sm" class="text-amber-700/80 dark:text-amber-500/80">Updating pricing here
                    won't affect existing active subscriptions immediately but will be applied on their next renewal
                    cycle.</flux:subheading>
            </div>
        </div>
    </div>
</div>
