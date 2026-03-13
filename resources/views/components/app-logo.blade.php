@props([
    'sidebar' => false,
    'withTagline' => true,
])

@if($sidebar)
    <flux:sidebar.brand name="NBTI Market Hub" {{ $attributes }}>
        <x-slot name="logo">
            <img src="{{ asset('assets/natihublogo.png') }}" alt="NBTI Market Hub" class="size-8">
        </x-slot>
    </flux:sidebar.brand>
@else
    <a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'flex items-center gap-3']) }} wire:navigate>
        <img src="{{ asset($withTagline ? 'assets/natihublogowithtagline.png' : 'assets/natihublogo.png') }}" 
             alt="{{ config('app.name') }}" 
             class="{{ $withTagline ? 'h-12 w-auto' : 'h-10 w-auto' }}">
    </a>
@endif
