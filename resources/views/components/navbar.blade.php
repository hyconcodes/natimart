<nav x-data="{ open: false }" class="bg-white/80 dark:bg-brand-950/80 backdrop-blur-md border-b border-brand-100 dark:border-brand-800 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <!-- Logo Section -->
            <div class="flex items-center">
                <x-app-logo />
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex md:items-center md:space-x-8">
                <a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-300 hover:text-hub-green dark:hover:text-hub-accent font-medium transition-colors">Home</a>
                <a href="{{ route('about') }}" class="text-gray-600 dark:text-gray-300 hover:text-hub-green dark:hover:text-hub-accent font-medium transition-colors">About</a>
                <a href="{{ route('register') }}" class="text-gray-600 dark:text-gray-300 hover:text-hub-green dark:hover:text-hub-accent font-medium transition-colors">Join the Cluster</a>
                
                @if (Route::has('login'))
                    <div class="flex items-center space-x-3 ml-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="group relative px-6 py-2.5 bg-hub-green text-white font-semibold rounded-xl transition-all hover:bg-green-900 active:scale-95 shadow-lg shadow-green-900/20">
                                <span class="relative z-10">Dashboard</span>
                                <div class="absolute inset-0 rounded-xl bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-300 hover:text-hub-green font-medium transition-colors">Log In</a>
                            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-hub-green text-white font-semibold rounded-xl transition-all hover:bg-green-900 shadow-lg shadow-green-900/20">
                                Start Selling
                            </a>
                        @endauth
                    </div>
                @endif
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-xl text-gray-500 hover:text-hub-green hover:bg-brand-50 dark:hover:bg-brand-900 transition-all">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden bg-white dark:bg-brand-950 border-b border-brand-100 dark:border-brand-800"
    >
        <div class="px-4 pt-2 pb-6 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-brand-50 dark:hover:bg-brand-900 hover:text-hub-green">Home</a>
            <a href="{{ route('about') }}" class="block px-3 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-brand-50 dark:hover:bg-brand-900 hover:text-hub-green">About</a>
            <a href="{{ route('register') }}" class="block px-3 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-brand-50 dark:hover:bg-brand-900 hover:text-hub-green">Join the Cluster</a>
            
            <div class="pt-4 border-t border-brand-100 dark:border-brand-800 flex flex-col gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center px-4 py-3 bg-hub-green text-white font-bold rounded-xl shadow-lg">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center px-4 py-3 text-gray-700 dark:text-gray-200 font-medium">Log In</a>
                    <a href="{{ route('register') }}" class="w-full text-center px-4 py-3 bg-hub-green text-white font-bold rounded-xl shadow-lg">Start Selling</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
