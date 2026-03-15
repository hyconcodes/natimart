<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-brand-50 dark:bg-brand-950">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-brand-200 bg-white dark:border-brand-800 dark:bg-brand-900">
            <flux:sidebar.nav>
            <flux:sidebar.header>
                @php
                    $dashboardRoute = auth()->user()->hasRole('vendor') && auth()->user()->shop
                        ? route('vendor.dashboard', ['shop_slug' => auth()->user()->shop->slug])
                        : route('dashboard');
                @endphp
                <x-app-logo :sidebar="true" href="{{ $dashboardRoute }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>
                
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="$dashboardRoute" :current="request()->routeIs('dashboard') || request()->routeIs('vendor.dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @hasanyrole('masteradmin|state_coordinator')
                    <flux:sidebar.group :heading="__('Admin')" class="grid">
                        @hasrole('masteradmin')
                            <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                                {{ __('User Management') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="shield-check" :href="route('admin.roles')" :current="request()->routeIs('admin.roles')" wire:navigate>
                                {{ __('Roles & Permissions') }}
                            </flux:sidebar.item>
                        @endhasrole
                        
                        <flux:sidebar.item icon="building-storefront" :href="route('admin.vendors')" :current="request()->routeIs('admin.vendors')" wire:navigate>
                            {{ __('Vendor Approval') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endhasanyrole
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav class="hidden">
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />
                    <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                        <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                        <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                    </flux:radio.group>
                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}
        
        <x-toast-container />

        @fluxScripts
    </body>
</html>
