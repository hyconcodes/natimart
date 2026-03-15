<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>About NBTI | NBTI Market Hub</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FAFAFA] font-sans antialiased text-gray-900 min-h-screen flex flex-col">
        <!-- Navigation -->
        <x-navbar />

        <main class="flex-grow flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl w-full mx-auto space-y-10">
                
                <!-- About NBTI Section -->
                <section>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">About NBTI</h1>
                    <p class="text-base sm:text-lg text-gray-600 leading-relaxed font-medium">
                        The National Board for Technology Incubation (NBTI) is a federal government agency 
                        under the Federal Ministry of Science, Technology and Innovation. NBTI was established 
                        to nurture new and small enterprises to maturity through the provision of comprehensive 
                        business support services.
                    </p>
                </section>

                <!-- Our Mission Section -->
                <section>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">Our Mission</h2>
                    <p class="text-base sm:text-lg text-gray-600 leading-relaxed font-medium">
                        To generate employment opportunities, new businesses and products, accelerate 
                        industrialisation, and create wealth through the commercialisation of technologies, 
                        innovations, and inventions.
                    </p>
                </section>

                <!-- The Market Hub Section -->
                <section>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">The Market Hub</h2>
                    <p class="text-base sm:text-lg text-gray-600 leading-relaxed font-medium">
                        The NBTI Market Hub is a digital marketplace connecting entrepreneurs from incubation 
                        centres across Nigeria's 36 states and FCT with buyers nationwide. Products are 
                        organised by state, making it easy to discover and support local innovation. Every order 
                        goes directly through the vendor's WhatsApp for a seamless buying experience.
                    </p>
                </section>

            </div>
        </main>

        <!-- Footer -->
        <x-footer />
        
        @fluxScripts
    </body>
</html>
