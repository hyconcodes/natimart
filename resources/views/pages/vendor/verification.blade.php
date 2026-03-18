<x-layouts::app :title="__('Store Verification')">
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <flux:heading size="xl" level="1">Store Verification</flux:heading>
                <flux:subheading>Submit your business and product regulatory documents for review.</flux:subheading>
            </div>

            <flux:button href="{{ route('vendor.verification.status', ['shop_slug' => auth()->user()->shop->slug]) }}"
                variant="subtle" icon="shield-check" wire:navigate>
                Check Submissions status
            </flux:button>
        </div>

        <div class="max-w-4xl">
            <livewire:vendor.verification-form />
        </div>
    </div>
</x-layouts::app>
