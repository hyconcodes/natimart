<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public string $businessName = '';
    public string $whatsappNumber = '';
    public string $state = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->state = $user->state ?? '';

        if ($user->hasRole('vendor') && $user->shop) {
            $this->businessName = $user->shop->name;
            $this->whatsappNumber = $user->shop->whatsapp_number;
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $rules = $this->profileRules($user->id);

        if ($user->hasRole('vendor')) {
            $rules['businessName'] = ['required', 'string', 'max:255'];
            $rules['whatsappNumber'] = ['required', 'string', 'max:20'];
        }

        $validated = $this->validate($rules);

        $user->name = $validated['name'];
        // Email is disabled in UI but we check if it's there
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->hasRole('vendor') && $user->shop) {
            $user->shop->update([
                'name' => $this->businessName,
                'slug' => \Illuminate\Support\Str::slug($this->businessName),
                'whatsapp_number' => $this->whatsappNumber,
            ]);
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Your Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email Address')" type="email" disabled autocomplete="email" />
                <flux:text size="xs" class="mt-1" class="text-yellow-600">Login email cannot be changed. Contact support for assistance.</flux:text>
            </div>

            @if(Auth::user()->hasRole('vendor'))
                <flux:separator />
                <flux:heading size="sm">Business Identity</flux:heading>
                
                <flux:input wire:model="businessName" :label="__('Business Name')" type="text" required />
                
                <flux:input wire:model="whatsappNumber" :label="__('WhatsApp Number')" type="text" required />

                <div class="space-y-2">
                    <flux:input wire:model="state" :label="__('State (Incubation Centre)')" type="text" disabled />
                    <div class="flex items-start gap-2 p-3 bg-brand-50/50 dark:bg-brand-950/50 rounded-xl border border-brand-100 dark:border-brand-800">
                        <flux:icon name="information-circle" variant="mini" class="size-4 text-brand-600 mt-0.5" />
                        <flux:text size="xs" class="text-yellow-600">
                            <strong>Strict Notice:</strong> Your incubation centre assignment is fixed based on your CAC registration. To request a relocation, please contact your <strong>State Coordinator</strong>.
                        </flux:text>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
