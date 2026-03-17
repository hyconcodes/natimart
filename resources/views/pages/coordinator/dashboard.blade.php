<x-layouts::app :title="__('Coordinator Dashboard')">
    <div class="space-y-8">
        <!-- Header Section -->
        <div>
            <flux:heading size="xl" level="1">State Coordination</flux:heading>
            <flux:subheading>Manage and approve vendor clusters in your region.</flux:subheading>
        </div>

        <livewire:dashboard.coordinator-stats />
    </div>
</x-layouts::app>
