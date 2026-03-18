<?php

use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

new class extends Component {
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $selectedRole = '';

    #[Url(history: true)]
    public $selectedState = '';

    public $editingUser = null;

    public $editName = '';

    public $editEmail = '';

    public $editState = '';

    public $editRoles = [];

    // Create Coordinator Fields
    public $newName = '';
    public $newEmail = '';
    public $newState = '';

    public function toggleUserStatus($userId)
    {
        if ($userId === auth()->id()) {
            $this->dispatch('toast', text: 'You cannot disable your own account.', heading: 'Action Denied', variant: 'danger');

            return;
        }

        $user = User::findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();

        $this->dispatch('toast', text: 'User account has been ' . ($user->is_active ? 'enabled' : 'disabled') . '.', heading: 'Status Updated', variant: 'success');
    }

    public function openCreateModal()
    {
        $this->newName = '';
        $this->newEmail = '';
        $this->newState = '';
        $this->dispatch('modal-show', name: 'create-coordinator-modal');
    }

    public function createCoordinator()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newEmail' => 'required|email|unique:users,email',
            'newState' => 'required|string',
        ]);

        $password = \Illuminate\Support\Str::random(12);

        $user = User::create([
            'name' => $this->newName,
            'email' => $this->newEmail,
            'state' => $this->newState,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'is_active' => true,
        ]);

        if (Spatie\Permission\Models\Role::where('name', 'state_coordinator')->exists()) {
            $user->assignRole('state_coordinator');
        }

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\StateCoordinatorCreated($user, $password));

        $this->dispatch('modal-close', name: 'create-coordinator-modal');
        $this->dispatch('toast', text: 'State Coordinator account created and email sent.', variant: 'success');
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUser = $user;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editState = $user->state;
        $this->editRoles = $user->roles->pluck('name')->toArray();

        $this->dispatch('modal-show', name: 'edit-user-modal');
    }

    public function updateUser()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $this->editingUser->id,
            'editRoles' => 'required|array|min:1',
        ]);

        $this->editingUser->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'state' => $this->editState,
        ]);

        $this->editingUser->syncRoles($this->editRoles);

        $this->editingUser = null;

        $this->dispatch('modal-close', name: 'edit-user-modal');

        $this->dispatch('toast', text: 'User information updated successfully.', heading: 'Success', variant: 'success');
    }

    public function with()
    {
        $query = User::query()
            ->with('roles')
            ->where('id', '!=', auth()->id())
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'masteradmin');
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedRole) {
            $query->role($this->selectedRole);
        }

        if ($this->selectedState) {
            $query->where('state', $this->selectedState);
        }

        return [
            'users' => $query->latest()->paginate(10),
            'allRoles' => Role::where('name', '!=', 'masteradmin')->get(),
        ];
    }
}; ?>

<div class="space-y-6 pb-20">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">User Management</flux:heading>
            <flux:subheading>Manage application users, roles and access status</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
            Create State Coordinator
        </flux:button>
    </div>

    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.300ms="search"
                placeholder="Search by name or email..." />
        </div>
        <div class="w-full md:w-64">
            <flux:select wire:model.live="selectedRole" placeholder="Filter by Role">
                <flux:select.option value="">All Roles</flux:select.option>
                @foreach ($allRoles as $role)
                    <flux:select.option :value="$role->name">{{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full md:w-64">
            <flux:select wire:model.live="selectedState" placeholder="Filter by State">
                <flux:select.option value="">All States</flux:select.option>
                @php
                    $states = [
                        'abia',
                        'adamawa',
                        'akwa_ibom',
                        'anambra',
                        'bauchi',
                        'bayelsa',
                        'benue',
                        'borno',
                        'cross_river',
                        'delta',
                        'ebonyi',
                        'edo',
                        'ekiti',
                        'enugu',
                        'fct_abuja',
                        'gombe',
                        'imo',
                        'jigawa',
                        'kaduna',
                        'kano',
                        'katsina',
                        'kebbi',
                        'kogi',
                        'kwara',
                        'lagos',
                        'nasarawa',
                        'niger',
                        'ogun',
                        'ondo',
                        'osun',
                        'oyo',
                        'plateau',
                        'rivers',
                        'sokoto',
                        'taraba',
                        'yobe',
                        'zamfara',
                    ];
                @endphp
                @foreach ($states as $state)
                    <flux:select.option :value="$state">{{ ucfirst(str_replace('_', ' ', $state)) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <div
        class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto min-w-full">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column class="hidden sm:table-cell">Roles</flux:table.column>
                    <flux:table.column>State</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column class="hidden md:table-cell">Registered</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($users as $user)
                        <flux:table.row :key="$user->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$user->name" initials="{{ $user->initials() }}"
                                        size="sm" class="flex-shrink-0" />
                                    <div class="flex flex-col min-w-0">
                                        <span
                                            class="font-medium text-gray-900 dark:text-gray-100 truncate max-w-[150px] sm:max-w-xs">{{ $user->name }}</span>
                                        <span
                                            class="text-xs text-gray-400 truncate max-w-[150px] sm:max-w-xs">{{ $user->email }}</span>
                                        <div class="sm:hidden mt-1 flex flex-wrap gap-1">
                                            @foreach ($user->roles as $role)
                                                <span
                                                    class="text-[10px] px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded uppercase font-bold tracking-wider text-zinc-600 dark:text-zinc-400">
                                                    {{ str_replace('_', ' ', $role->name) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="hidden sm:table-cell">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->roles as $role)
                                        <flux:badge size="sm" color="zinc" inset="top" class="capitalize">
                                            {{ str_replace('_', ' ', $role->name) }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if ($user->state)
                                    <span
                                        class="text-xs font-bold uppercase tracking-tighter text-gray-500">{{ str_replace('_', ' ', $user->state) }}</span>
                                @else
                                    <span class="text-[10px] text-gray-300 italic">Global</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                @if ($user->is_active)
                                    <flux:badge size="sm" color="green" inset="top">Active</flux:badge>
                                @else
                                    <flux:badge size="sm" color="red" inset="top">Disabled</flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell class="hidden md:table-cell text-xs text-gray-500 whitespace-nowrap">
                                {{ $user->created_at->format('M d, Y') }}
                            </flux:table.cell>

                            <flux:table.cell align="end">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />

                                    <flux:menu>
                                        <flux:menu.item icon="pencil-square" wire:click="editUser({{ $user->id }})">
                                            Edit Details</flux:menu.item>

                                        @if ($user->id !== auth()->id())
                                            <flux:menu.separator />
                                            @if ($user->is_active)
                                                <flux:menu.item icon="no-symbol" variant="danger"
                                                    wire:click="toggleUserStatus({{ $user->id }})"
                                                    wire:confirm="Are you sure you want to disable this user? They will be logged out immediately.">
                                                    Disable User
                                                </flux:menu.item>
                                            @else
                                                <flux:menu.item icon="check-circle"
                                                    wire:click="toggleUserStatus({{ $user->id }})">
                                                    Enable User
                                                </flux:menu.item>
                                            @endif
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="py-12">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="mb-4 rounded-full bg-zinc-50 dark:bg-zinc-800 p-3">
                                        <flux:icon name="magnifying-glass" class="size-6 text-zinc-400" />
                                    </div>
                                    <flux:heading size="lg">No users found</flux:heading>
                                    <flux:subheading>Try adjusting your search or filters to find what you're looking
                                        for.</flux:subheading>
                                    @if ($search || $selectedRole)
                                        <flux:button variant="subtle" size="sm" class="mt-4"
                                            wire:click="$set('search', ''); $set('selectedRole', '')">
                                            Clear all filters
                                        </flux:button>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-brand-100 dark:border-brand-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Create Coordinator Modal --}}
    <flux:modal name="create-coordinator-modal" class="w-full max-w-lg">
        <form wire:submit="createCoordinator" class="space-y-6">
            <div>
                <flux:heading size="lg">Assign New State Coordinator</flux:heading>
                <flux:subheading>This will create a new account with the **State Coordinator** role and email them their
                    credentials.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input label="Full Name" wire:model="newName" placeholder="e.g. John Smith" />
                <flux:input label="Email Address" wire:model="newEmail" type="email"
                    placeholder="official@email.com" />

                <flux:select label="Assigned State" wire:model="newState" placeholder="Select state of jurisdiction">
                    <flux:select.option value="abia">Abia</flux:select.option>
                    <flux:select.option value="adamawa">Adamawa</flux:select.option>
                    <flux:select.option value="akwa_ibom">Akwa Ibom</flux:select.option>
                    <flux:select.option value="anambra">Anambra</flux:select.option>
                    <flux:select.option value="bauchi">Bauchi</flux:select.option>
                    <flux:select.option value="bayelsa">Bayelsa</flux:select.option>
                    <flux:select.option value="benue">Benue</flux:select.option>
                    <flux:select.option value="borno">Borno</flux:select.option>
                    <flux:select.option value="cross_river">Cross River</flux:select.option>
                    <flux:select.option value="delta">Delta</flux:select.option>
                    <flux:select.option value="ebonyi">Ebonyi</flux:select.option>
                    <flux:select.option value="edo">Edo</flux:select.option>
                    <flux:select.option value="ekiti">Ekiti</flux:select.option>
                    <flux:select.option value="enugu">Enugu</flux:select.option>
                    <flux:select.option value="fct_abuja">FCT Abuja</flux:select.option>
                    <flux:select.option value="gombe">Gombe</flux:select.option>
                    <flux:select.option value="imo">Imo</flux:select.option>
                    <flux:select.option value="jigawa">Jigawa</flux:select.option>
                    <flux:select.option value="kaduna">Kaduna</flux:select.option>
                    <flux:select.option value="kano">Kano</flux:select.option>
                    <flux:select.option value="katsina">Katsina</flux:select.option>
                    <flux:select.option value="kebbi">Kebbi</flux:select.option>
                    <flux:select.option value="kogi">Kogi</flux:select.option>
                    <flux:select.option value="kwara">Kwara</flux:select.option>
                    <flux:select.option value="lagos">Lagos</flux:select.option>
                    <flux:select.option value="nasarawa">Nasarawa</flux:select.option>
                    <flux:select.option value="niger">Niger</flux:select.option>
                    <flux:select.option value="ogun">Ogun</flux:select.option>
                    <flux:select.option value="ondo">Ondo</flux:select.option>
                    <flux:select.option value="osun">Osun</flux:select.option>
                    <flux:select.option value="oyo">Oyo</flux:select.option>
                    <flux:select.option value="plateau">Plateau</flux:select.option>
                    <flux:select.option value="rivers">Rivers</flux:select.option>
                    <flux:select.option value="sokoto">Sokoto</flux:select.option>
                    <flux:select.option value="taraba">Taraba</flux:select.option>
                    <flux:select.option value="yobe">Yobe</flux:select.option>
                    <flux:select.option value="zamfara">Zamfara</flux:select.option>
                </flux:select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Create Account</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit User Modal --}}
    <flux:modal name="edit-user-modal" class="w-full max-w-lg">
        <form wire:submit="updateUser" class="space-y-6">
            <div>
                <flux:heading size="lg">Edit User Account</flux:heading>
                <flux:subheading>Update details and permissions for this user</flux:subheading>
            </div>

            <flux:input label="Full Name" wire:model="editName" autocomplete="name" />
            <flux:input label="Email Address" wire:model="editEmail" type="email" autocomplete="email" />

            <flux:select label="State (For Coordinators)" wire:model="editState" placeholder="Select a state">
                <flux:select.option value="abia">Abia</flux:select.option>
                <flux:select.option value="adamawa">Adamawa</flux:select.option>
                <flux:select.option value="akwa_ibom">Akwa Ibom</flux:select.option>
                <flux:select.option value="anambra">Anambra</flux:select.option>
                <flux:select.option value="bauchi">Bauchi</flux:select.option>
                <flux:select.option value="bayelsa">Bayelsa</flux:select.option>
                <flux:select.option value="benue">Benue</flux:select.option>
                <flux:select.option value="borno">Borno</flux:select.option>
                <flux:select.option value="cross_river">Cross River</flux:select.option>
                <flux:select.option value="delta">Delta</flux:select.option>
                <flux:select.option value="ebonyi">Ebonyi</flux:select.option>
                <flux:select.option value="edo">Edo</flux:select.option>
                <flux:select.option value="ekiti">Ekiti</flux:select.option>
                <flux:select.option value="enugu">Enugu</flux:select.option>
                <flux:select.option value="fct_abuja">FCT Abuja</flux:select.option>
                <flux:select.option value="gombe">Gombe</flux:select.option>
                <flux:select.option value="imo">Imo</flux:select.option>
                <flux:select.option value="jigawa">Jigawa</flux:select.option>
                <flux:select.option value="kaduna">Kaduna</flux:select.option>
                <flux:select.option value="kano">Kano</flux:select.option>
                <flux:select.option value="katsina">Katsina</flux:select.option>
                <flux:select.option value="kebbi">Kebbi</flux:select.option>
                <flux:select.option value="kogi">Kogi</flux:select.option>
                <flux:select.option value="kwara">Kwara</flux:select.option>
                <flux:select.option value="lagos">Lagos</flux:select.option>
                <flux:select.option value="nasarawa">Nasarawa</flux:select.option>
                <flux:select.option value="niger">Niger</flux:select.option>
                <flux:select.option value="ogun">Ogun</flux:select.option>
                <flux:select.option value="ondo">Ondo</flux:select.option>
                <flux:select.option value="osun">Osun</flux:select.option>
                <flux:select.option value="oyo">Oyo</flux:select.option>
                <flux:select.option value="plateau">Plateau</flux:select.option>
                <flux:select.option value="rivers">Rivers</flux:select.option>
                <flux:select.option value="sokoto">Sokoto</flux:select.option>
                <flux:select.option value="taraba">Taraba</flux:select.option>
                <flux:select.option value="yobe">Yobe</flux:select.option>
                <flux:select.option value="zamfara">Zamfara</flux:select.option>
            </flux:select>

            <div class="space-y-3">
                <flux:label>Assigned Roles</flux:label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($allRoles as $role)
                        <div
                            class="flex items-center space-x-2 p-2 rounded-lg border border-zinc-100 dark:border-zinc-800">
                            <flux:checkbox wire:model="editRoles" :value="$role->name" :id="'edit-role-'.$role->id"
                                :label="ucfirst(str_replace('_', ' ', $role->name))" />
                        </div>
                    @endforeach
                </div>
                @error('editRoles')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Update User</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
