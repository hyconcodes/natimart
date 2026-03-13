<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gray-50 antialiased dark:bg-zinc-950">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-hub-green relative hidden h-full flex-col p-10 text-white lg:flex">
                <div class="absolute inset-0 bg-hub-green/90"></div>
                <x-app-logo class="relative z-20 text-white !gap-4" />

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-4">
                        <p class="text-2xl font-medium leading-relaxed italic text-green-50">&ldquo;{{ trim($message) }}&rdquo;</p>
                        <footer><cite class="text-lg font-semibold text-white not-italic">— {{ trim($author) }}</cite></footer>
                    </blockquote>
                </div>

                {{-- Subtle background decoration --}}
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-green-700/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-40 left-0 -ml-20 w-80 h-80 bg-green-700/30 rounded-full blur-3xl"></div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <x-app-logo class="justify-center" :withTagline="false" />
                    <div class="bg-white dark:bg-zinc-900 p-8 rounded-2xl shadow-xl lg:shadow-none lg:bg-transparent border border-gray-100 lg:border-none">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
