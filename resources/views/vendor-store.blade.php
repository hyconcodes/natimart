<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $shop->name }} | NBTI Market Hub</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 font-sans antialiased text-gray-900 min-h-screen flex flex-col">
        <!-- Minimalistic Header for Vendor Store -->
        <header class="bg-white border-b border-brand-100 dark:bg-brand-900 dark:border-brand-800 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center gap-4">
                        <x-app-logo class="w-10 h-10" />
                        <div>
                            <h1 class="font-black text-xl text-gray-900 dark:text-white">{{ $shop->name }}</h1>
                            <p class="text-xs text-gray-500 font-medium">Verified Vendor in {{ ucfirst($shop->state) }}</p>
                        </div>
                    </div>
                    <div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_number) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-[#25D366] hover:bg-[#128C7E] text-white font-bold rounded-lg transition-colors gap-2 text-sm shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.022.014-.463.209-.463.209s-.041.015-.082 0l-1.483-.717c-.033-.016-.052-.047-.052-.083v-1.789c0-.036.02-.067.052-.083l1.483-.717c.041-.015.082 0 .082 0s.441.195.463.209c.022.014.032.046.032.083V14.3c0 .037-.01.069-.032.082zm-9.172 0c.022.014.463.209.463.209s.041.015.082 0l1.483-.717c.033-.016.052-.047.052-.083v-1.789c0-.036.02-.067-.052-.083l-1.483-.717c-.041-.015-.082 0-.082 0s-.441.195-.463.209C8.3 11.213 8.29 11.245 8.29 11.282V14.3c0 .037.01.069.032.082zM12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/>
                            </svg>
                            Contact Store
                        </a>
                    </div>
                </div>
            </div>
            
            @if(!$shop->is_approved)
                <div class="bg-amber-500 text-white text-center py-1.5 text-xs font-bold uppercase tracking-wide">
                    Preview Mode: This storefront is currently pending review and is not visible to the public.
                </div>
            @endif
        </header>

        <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
            <div class="mb-10 flex justify-between items-end border-b border-gray-200 pb-4">
                <h2 class="text-2xl font-black text-gray-900">All Products</h2>
                <span class="text-gray-500 font-medium">{{ $shop->products->count() }} Items</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($shop->products as $product)
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow border border-gray-100 group flex flex-col h-full">
                        <div class="aspect-square relative overflow-hidden bg-gray-100 flex-shrink-0">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 leading-tight">{{ $product->name }}</h3>
                            <div class="text-xl font-black text-hub-green mb-4">₦{{ number_format($product->price) }}</div>
                            @if($product->description)
                                <p class="text-gray-600 text-sm mb-6 line-clamp-2">{{ $product->description }}</p>
                            @endif
                            <div class="mt-auto">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_number) }}?text=Hello%20{{ urlencode($shop->name) }},%20I%20am%20interested%20in%20your%20product:%20{{ urlencode($product->name) }}%20(₦{{ number_format($product->price) }})" target="_blank" class="flex items-center justify-center w-full h-11 bg-brand-50 hover:bg-brand-100 text-hub-green font-bold rounded-xl transition-colors gap-2 text-sm border border-brand-200">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.022.014-.463.209-.463.209s-.041.015-.082 0l-1.483-.717c-.033-.016-.052-.047-.052-.083v-1.789c0-.036.02-.067.052-.083l1.483-.717c.041-.015.082 0 .082 0s.441.195.463.209c.022.014.032.046.032.083V14.3c0 .037-.01.069-.032.082zm-9.172 0c.022.014.463.209.463.209s.041.015.082 0l1.483-.717c.033-.016.052-.047.052-.083v-1.789c0-.036.02-.067-.052-.083l-1.483-.717c-.041-.015-.082 0-.082 0s-.441.195-.463.209C8.3 11.213 8.29 11.245 8.29 11.282V14.3c0 .037.01.069.032.082zM12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/>
                                    </svg>
                                    Order via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No products yet</h3>
                        <p class="text-gray-500">This vendor hasn't added any products to their public storefront.</p>
                    </div>
                @endforelse
            </div>
        </main>

        <footer class="bg-white border-t border-brand-100 mt-auto py-8">
            <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500 font-medium tracking-wide">
                Powered by NBTI Market Hub &copy; {{ date('Y') }}
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
