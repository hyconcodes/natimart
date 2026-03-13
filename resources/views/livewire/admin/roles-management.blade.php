<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;

new class extends Component {
    public $roles;
    public $permissions;
    public $editingRole = null;
    public $selectedPermissions = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->roles = Role::with('permissions')->get();
        $this->permissions = Permission::all();
    }

    public function editRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $this->editingRole = $role;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    }

    public function saveRole()
    {
        if ($this->editingRole) {
            $this->editingRole->syncPermissions($this->selectedPermissions);
            $this->editingRole = null;
            $this->selectedPermissions = [];
            $this->loadData();
            
            $this->dispatch('toast', 
                text: 'Permissions updated successfully.',
                heading: 'Success',
                variant: 'success',
            );
        }
    }
}; ?>

<div class="space-y-6 pb-20">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Roles & Permissions</flux:heading>
            <flux:subheading>Manage access control levels and assigned capabilities</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($roles as $role)
            <div class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 capitalize">
                        {{ str_replace('_', ' ', $role->name) }}
                    </h3>
                    <flux:badge size="sm" color="green" inset="top">{{ $role->permissions->count() }}</flux:badge>
                </div>

                <div class="flex flex-wrap gap-2 mb-6 min-h-[80px] flex-grow">
                    @forelse ($role->permissions->take(8) as $permission)
                        <span class="text-[10px] px-2 py-0.5 bg-brand-50 dark:bg-brand-950 text-brand-600 dark:text-brand-400 rounded-lg border border-brand-100 dark:border-brand-800 capitalize">
                            {{ str_replace(' ', ' ', $permission->name) }}
                        </span>
                    @empty
                        <span class="text-[10px] text-gray-400 italic italic">No permissions assigned</span>
                    @endforelse
                    
                    @if ($role->permissions->count() > 8)
                        <span class="text-[10px] text-gray-400 font-medium">+{{ $role->permissions->count() - 8 }} more</span>
                    @endif
                </div>

                <div class="mt-auto">
                    <flux:button variant="subtle" size="sm" class="w-full" wire:click="editRole({{ $role->id }})">
                        Edit Permissions
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl">
                <flux:heading>No roles defined</flux:heading>
                <flux:subheading>Run your seeders to initialize application roles.</flux:subheading>
            </div>
        @endforelse
    </div>

    {{-- Edit Modal --}}
    <flux:modal name="edit-role-modal" :open="$editingRole !== null" @close="$wire.editingRole = null" class="w-full max-w-2xl">
        <div class="space-y-6">
            @if($editingRole)
                <div>
                    <flux:heading size="lg">Edit {{ ucfirst(str_replace('_', ' ', $editingRole->name)) }} Role</flux:heading>
                    <flux:subheading>Select the capabilities this role should possess across the platform</flux:subheading>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($permissions as $permission)
                        <label class="flex items-center space-x-3 p-3 border border-brand-100 dark:border-brand-800 rounded-xl hover:bg-brand-50 dark:hover:bg-brand-950 transition-colors cursor-pointer group">
                            <flux:checkbox 
                                wire:model="selectedPermissions" 
                                :value="$permission->name"
                                :id="'perm-'.$permission->id"
                            />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-brand-600 capitalize">
                                {{ $permission->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button variant="ghost" wire:click="$set('editingRole', null)">Cancel</flux:button>
                    <flux:button variant="primary" wire:click="saveRole">Save Changes</flux:button>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
