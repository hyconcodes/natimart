<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Join the Cluster | NBTI Market Hub</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FAFAFA] font-sans antialiased text-gray-900 min-h-screen flex flex-col">
        <!-- Navigation -->
        <x-navbar />

        <main class="flex-grow flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl w-full mx-auto space-y-10">
                
                <!-- Header -->
                <div class="text-center space-y-4">
                    <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Join the Cluster</h1>
                    <p class="text-base sm:text-lg text-gray-500 max-w-lg mx-auto leading-relaxed font-medium">
                        Register as a vendor and submit your best-selling product to the NBTI Market Hub.
                    </p>
                </div>

                <!-- Session Status / Errors -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- Business Details Card -->
                    <div class="bg-white rounded-[2rem] border border-brand-100 shadow-sm p-8 sm:p-10 space-y-8">
                        <h2 class="text-xl font-bold text-gray-900">Business Details</h2>
                        
                        <div class="space-y-6">
                            <!-- Business Name -->
                            <flux:input 
                                name="business_name" 
                                label="Business Name" 
                                :value="old('business_name')" 
                                placeholder="E.g. Aba Leather Works" 
                                required 
                                autofocus 
                            />

                            <!-- Your Name -->
                            <flux:input 
                                name="name" 
                                label="Your Name" 
                                :value="old('name')" 
                                placeholder="Full name" 
                                required 
                            />

                            <!-- Email -->
                            <flux:input 
                                name="email" 
                                type="email" 
                                label="Email" 
                                :value="old('email')" 
                                placeholder="you@example.com" 
                                required 
                            />

                            <!-- Password Fields (Not in UI mockup, but requested by User) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <flux:input 
                                    name="password" 
                                    type="password" 
                                    label="Password" 
                                    placeholder="Create password" 
                                    required 
                                    viewable 
                                />
                                <flux:input 
                                    name="password_confirmation" 
                                    type="password" 
                                    label="Confirm Password" 
                                    placeholder="Confirm password" 
                                    required 
                                    viewable 
                                />
                            </div>

                            <!-- WhatsApp Number -->
                            <flux:input 
                                name="whatsapp_number" 
                                label="WhatsApp Number" 
                                :value="old('whatsapp_number')" 
                                placeholder="2348012345678" 
                                required 
                            />

                            <!-- State -->
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
                            <flux:select name="state" label="State (Incubation Centre)" placeholder="Select state" required>
                                @foreach($states as $state)
                                    <flux:select.option value="{{ strtolower($state) }}">{{ $state }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <!-- Best-Selling Product Card -->
                    <div class="bg-white rounded-[2rem] border border-brand-100 shadow-sm p-8 sm:p-10 space-y-8">
                        <h2 class="text-xl font-bold text-gray-900">Your Best-Selling Product</h2>
                        
                        <div class="space-y-6">
                            <!-- Product Name -->
                            <flux:input 
                                name="product_name" 
                                label="Product Name" 
                                :value="old('product_name')" 
                                placeholder="E.g. Handcrafted Leather Bag" 
                                required 
                            />

                            <!-- Description -->
                            <flux:textarea 
                                name="product_description" 
                                label="Description (optional)" 
                                placeholder="Describe your product in as much detail as you'd like — ingredients, sizes, colours, what makes it special..." 
                                rows="4" 
                            >{{ old('product_description') }}</flux:textarea>

                            <!-- Product Image Upload (Styled to match design) -->
                            <div class="space-y-2">
                                <flux:label>Product Image (optional)</flux:label>
                                <div class="relative w-full border-2 border-dashed border-gray-200 hover:border-hub-green rounded-xl p-8 text-center bg-gray-50/50 hover:bg-green-50/30 transition-colors group cursor-pointer focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-hub-green">
                                    <input type="file" name="product_image" accept="image/jpeg, image/png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="flex flex-col items-center justify-center space-y-3 pointer-events-none text-gray-500 group-hover:text-hub-green transition-colors">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        <div class="text-sm font-semibold">Click to upload product photo</div>
                                        <div class="text-[11px] font-medium tracking-wide uppercase text-gray-400">JPG, PNG up to 5MB</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Price -->
                            <flux:input 
                                name="product_price" 
                                type="number" 
                                label="Price (₦)" 
                                :value="old('product_price')" 
                                placeholder="5000" 
                                min="0" 
                                step="100" 
                                required 
                            />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full h-14 bg-[#166534] hover:bg-[#14532d] active:scale-[0.98] text-white font-bold rounded-xl transition-all shadow-md text-base tracking-wide flex items-center justify-center gap-2 relative overflow-hidden group">
                        <span class="relative z-10">Submit Application</span>
                        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl"></div>
                    </button>
                    
                    <div class="text-center pt-2">
                        <span class="text-sm text-gray-500 font-medium tracking-tight">Already part of the cluster?</span>
                        <a href="{{ route('login') }}" class="text-sm text-hub-green font-bold hover:underline ml-1 tracking-tight">Log in</a>
                    </div>
                </form>

            </div>
        </main>

        <!-- Footer -->
        <x-footer />
        
        <!-- Global Toast -->
        <x-toast-container />
        @fluxScripts
    </body>
</html>
