<x-layouts::app :title="__('Vendor Approval')">
    <div class="space-y-6">
        <div>
            <flux:heading size="xl" level="1">Vendor Approval</flux:heading>
            <flux:subheading>Review and approve vendor applications and products</flux:subheading>
        </div>

        <div class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl p-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-brand-50 dark:bg-brand-950 rounded-full flex items-center justify-center mb-6">
                <flux:icon name="building-storefront" class="size-8 text-brand-600" />
            </div>
            <flux:heading size="lg">No pending approvals</flux:heading>
            <flux:subheading>Vendor and product management logic will be implemented in the next phase.</flux:subheading>
        </div>
    </div>
</x-layouts::app>
