<x-layouts::app :title="__('Masteradmin Dashboard')">
    <div class="space-y-8">
        <!-- Header Section -->
        <div>
            <flux:heading size="xl" level="1">Master Administration</flux:heading>
            <flux:subheading>Global overview of the NBTI Market Hub.</flux:subheading>
        </div>

        <livewire:dashboard.admin-stats />
    </div>
</x-layouts::app>
