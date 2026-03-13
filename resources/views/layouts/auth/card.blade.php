<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gray-50 antialiased dark:bg-zinc-950">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-6">
                <x-app-logo class="justify-center" :withTagline="false" />

                <div class="flex flex-col gap-6">
                    <div class="rounded-2xl border border-gray-100 bg-white dark:bg-zinc-900 shadow-xl overflow-hidden">
                        <div class="px-10 py-12">{{ $slot }}</div>
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
