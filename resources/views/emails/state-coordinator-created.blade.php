<x-mail::message>
    # Welcome, {{ $user->name }}

    You have been appointed as a **State Coordinator** for the NBTI Hub in the state of
    **{{ ucfirst(str_replace('_', ' ', $user->state)) }}**.

    As a State Coordinator, you are responsible for:
    * Reviewing and verifying vendor registration documents.
    * Approving local products for the NBTI marketplace.
    * Assisting vendors in your state through the onboarding process.

    ### Your Account Credentials
    **Email:** {{ $user->email }}
    **Temporary Password:** `{{ $password }}`

    <x-mail::button :url="config('app.url') . '/login'">
        Login to Dashboard
    </x-mail::button>

    *Please change your password immediately after your first login for security.*

    Best regards,
    The NBTI Hub Team
</x-mail::message>
