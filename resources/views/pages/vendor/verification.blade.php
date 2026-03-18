<x-layouts::app :title="__('Store Verification')">
    <div class="space-y-8">
        <div>
            <flux:heading size="xl" level="1">Store Verification</flux:heading>
            <flux:subheading>Submit your business and product regulatory documents for review.</flux:subheading>
        </div>

        <div class="max-w-4xl">
            <livewire:vendor.verification-form />
        </div>
    </div>
</x-layouts::app>
