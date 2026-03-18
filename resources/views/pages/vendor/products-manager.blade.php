<x-layouts::app :title="__('Manage Products')">
    <div class="space-y-8">
        <div>
            <flux:heading size="xl" level="1">Storefront Catalog</flux:heading>
            <flux:subheading>Manage your products and their availability on the hub.</flux:subheading>
        </div>

        <livewire:vendor.product-management />
    </div>
</x-layouts::app>
