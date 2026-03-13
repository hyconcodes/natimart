<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>NBTI Market Hub - Discover Made-in-Nigeria Products</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 font-sans antialiased text-gray-900">
        {{-- Header --}}
        <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center gap-3">
                            <div class="w-10 h-10 bg-hub-green rounded-full flex items-center justify-center text-white font-bold text-xl">
                                N
                            </div>
                            <span class="text-xl font-bold text-gray-900 tracking-tight">NBTI Market Hub</span>
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:space-x-8">
                        <a href="#" class="text-gray-600 hover:text-hub-green font-medium transition-colors">Home</a>
                        <a href="#" class="text-gray-600 hover:text-hub-green font-medium transition-colors">About</a>
                        <a href="#" class="text-gray-600 hover:text-hub-green font-medium transition-colors">Join the Cluster</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-hub-green hover:bg-green-900 transition-all shadow-sm">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-hub-green hover:bg-green-900 transition-all shadow-sm">
                                    Admin
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <main>
            {{-- Hero Section --}}
            <section class="bg-hub-green py-20 lg:py-32 relative overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="max-w-3xl">
                        <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold text-white mb-8 leading-tight">
                            Discover Made-in-Nigeria Products & Innovation
                        </h1>
                        <p class="text-lg sm:text-xl text-green-50 mb-10 leading-relaxed opacity-90">
                            Explore products from technology incubation centres across Nigeria's 36 states. Support local entrepreneurs and order directly via WhatsApp.
                        </p>
                        <a href="#" class="inline-flex items-center px-8 py-4 bg-hub-yellow hover:bg-yellow-500 text-gray-900 font-bold rounded-xl transition-all transform hover:scale-105 shadow-xl group">
                            Join the Cluster
                            <svg class="ml-2 w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                {{-- Subtle background decoration --}}
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-green-700/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-96 h-96 bg-green-700/20 rounded-full blur-3xl"></div>
            </section>

            {{-- Shop by State --}}
            <section class="py-20 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-10">Shop by State</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @php
                            $states = [
                                'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa',
                                'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo',
                                'Ekiti', 'Enugu', 'FCT Abuja', 'Gombe', 'Imo', 'Jigawa',
                                'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara',
                                'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun',
                                'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
                            ];
                        @endphp
                        @foreach ($states as $state)
                            <a href="#" class="group flex items-center p-4 bg-white border border-gray-100 rounded-xl hover:border-hub-accent hover:shadow-md transition-all">
                                <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center mr-3 group-hover:bg-hub-accent/10 transition-colors">
                                    <svg class="w-4 h-4 text-hub-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-700 group-hover:text-hub-green transition-colors">{{ $state }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Latest Products --}}
            <section class="py-20 bg-gray-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-end mb-12">
                        <h2 class="text-3xl font-bold text-gray-900">Latest Products</h2>
                        <a href="#" class="text-hub-green font-semibold hover:underline">View all products</a>
                    </div>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {{-- Product Card 1 --}}
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow border border-gray-100 group">
                            <div class="aspect-square relative overflow-hidden bg-gray-100">
                                <img src="/assets/hair-care.png" alt="Hair Care Products" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">Zerves Hair Care Products</h3>
                                    <span class="text-lg font-bold text-hub-green">₦10,400</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-6 line-clamp-2">
                                    Gemini said Zerves hair care products are professional-grade, indigenous formulas designed for all hair types.
                                </p>
                                <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-50">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Akzerves Global Services</span>
                                        <span class="px-2.5 py-0.5 rounded-full bg-hub-yellow/20 text-yellow-800 text-[10px] font-bold uppercase tracking-wider">Oyo</span>
                                    </div>
                                    <a href="#" class="flex items-center justify-center h-12 px-6 bg-[#166534] hover:bg-[#14532d] text-white font-semibold rounded-lg transition-colors gap-2 text-sm">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.022.014-.463.209-.463.209s-.041.015-.082 0l-1.483-.717c-.033-.016-.052-.047-.052-.083v-1.789c0-.036.02-.067.052-.083l1.483-.717c.041-.015.082 0 .082 0s.441.195.463.209c.022.014.032.046.032.083V14.3c0 .037-.01.069-.032.082zm-9.172 0c.022.014.463.209.463.209s.041.015.082 0l1.483-.717c.033-.016.052-.047.052-.083v-1.789c0-.036.02-.067-.052-.083l-1.483-.717c-.041-.015-.082 0-.082 0s-.441.195-.463.209C8.3 11.213 8.29 11.245 8.29 11.282V14.3c0 .037.01.069.032.082zM12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/>
                                        </svg>
                                        Order via WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Product Card 2 --}}
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow border border-gray-100 group">
                            <div class="aspect-square relative overflow-hidden bg-gray-100">
                                <img src="/assets/household.png" alt="Household Cleaning" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">Zerves Household Cleaning</h3>
                                    <span class="text-lg font-bold text-hub-green">₦13,500</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-6 line-clamp-2">
                                    A comprehensive, high-performance collection designed for the modern home. Tough on stains, gentle on hands.
                                </p>
                                <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-50">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Akzerves Global Services</span>
                                        <span class="px-2.5 py-0.5 rounded-full bg-hub-yellow/20 text-yellow-800 text-[10px] font-bold uppercase tracking-wider">Oyo</span>
                                    </div>
                                    <a href="#" class="flex items-center justify-center h-12 px-6 bg-[#166534] hover:bg-[#14532d] text-white font-semibold rounded-lg transition-colors gap-2 text-sm">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.022.014-.463.209-.463.209s-.041.015-.082 0l-1.483-.717c-.033-.016-.052-.047-.052-.083v-1.789c0-.036.02-.067.052-.083l1.483-.717c.041-.015.082 0 .082 0s.441.195.463.209c.022.014.032.046.032.083V14.3c0 .037-.01.069-.032.082zm-9.172 0c.022.014.463.209.463.209s.041.015.082 0l1.483-.717c.033-.016.052-.047.052-.083v-1.789c0-.036.02-.067-.052-.083l-1.483-.717c-.041-.015-.082 0-.082 0s-.441.195-.463.209C8.3 11.213 8.29 11.245 8.29 11.282V14.3c0 .037.01.069.032.082zM12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/>
                                        </svg>
                                        Order via WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-100 pt-20 pb-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8 mb-16">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-hub-green rounded-full flex items-center justify-center text-white font-bold text-sm">
                            N
                        </div>
                        <span class="text-lg font-bold text-gray-900 tracking-tight">NBTI Market Hub</span>
                    </div>
                    <div class="flex items-center space-x-10 text-sm font-medium text-gray-500">
                        <a href="#" class="hover:text-hub-green transition-colors">About NBTI</a>
                        <a href="#" class="hover:text-hub-green transition-colors">Join the Cluster</a>
                    </div>
                    <div class="text-sm text-gray-400 font-medium">
                        &copy; {{ date('Y') }} National Board for Technology Incubation
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
