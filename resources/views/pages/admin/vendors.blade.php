<x-layouts::app :title="__('Vendor Approval')">
    <div class="space-y-6">
        <div>
            <flux:heading size="xl" level="1">Vendor Approval</flux:heading>
            <flux:subheading>Review and approve vendor applications and products</flux:subheading>
        </div>

        <livewire:admin.approval-management />
    </div>
</x-layouts::app>
