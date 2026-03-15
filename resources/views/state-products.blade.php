<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $stateName = \Illuminate\Support\Str::title(str_replace('-', ' ', $state));
        @endphp

        <title>Products from {{ $stateName }} | NBTI Market Hub</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FAFAFA] font-sans antialiased text-gray-900 min-h-screen flex flex-col">
        <!-- Navigation -->
        <x-navbar />

        <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
            
            <!-- Back Link -->
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-500 hover:text-hub-green font-medium transition-colors mb-6">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to all states
            </a>

            <!-- Page Title -->
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-8">
                Products from {{ $stateName }}
            </h1>

            <!-- Empty State Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 sm:p-20 text-center flex flex-col items-center justify-center">
                
                <!-- Star Icon Container -->
                <div class="w-20 h-20 rounded-full bg-[#E8F3EE] flex items-center justify-center mb-6 ring-8 ring-white shadow-sm">
                    <svg class="w-10 h-10 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385c.148.621-.531 1.073-1.091.777l-4.898-2.585a.562.562 0 0 0-.527 0l-4.898 2.585c-.56.296-1.239-.156-1.091-.777l1.285-5.385a.563.563 0 0 0-.182-.557l-4.204-3.602a.563.563 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                    </svg>
                </div>

                <!-- Text Content -->
                <h3 class="text-xl font-bold text-gray-900 mb-3">Coming Soon</h3>
                <p class="text-base text-gray-500 max-w-md mx-auto mb-8 leading-relaxed font-medium">
                    No approved products in {{ $stateName }} yet. Be the first entrepreneur to showcase your products here!
                </p>

                <!-- Call to Action -->
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-[#166534] hover:bg-[#14532d] text-white font-semibold rounded-lg transition-transform transform hover:scale-105 shadow-md">
                    Join as a Vendor
                </a>

            </div>

        </main>

        <!-- Footer -->
        <x-footer />

        @fluxScripts
    </body>
</html>
