<x-layouts::app :title="__('Vendor Dashboard')">
    <div class="space-y-8">
        <!-- Header Section -->
        <div>
            <flux:heading size="xl" level="1">Welcome back, {{ auth()->user()->name }}!</flux:heading>
            <flux:subheading>Manage your products and storefront at NBTI Market Hub.</flux:subheading>
        </div>

        @if(auth()->user()->shop && !auth()->user()->shop->is_approved)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl p-4 flex items-start gap-4 shadow-sm">
            <div class="bg-amber-100 p-2 rounded-full text-amber-600 mt-0.5">
                <flux:icon name="clock" class="size-5" />
            </div>
            <div>
                <h3 class="font-bold text-amber-900 leading-tight mb-1">Storefront Under Review</h3>
                <p class="text-sm font-medium text-amber-800/80">
                    Your store <strong>{{ auth()->user()->shop->name }}</strong> is currently under review by the {{ ucfirst(auth()->user()->shop->state) }} State Coordinator. Your storefront 
                    (<a href="http://{{ auth()->user()->shop->slug }}.{{ env('APP_DOMAIN', 'localhost') }}:8000" class="underline hover:text-amber-900" target="_blank">{{ auth()->user()->shop->slug }}.{{ env('APP_DOMAIN', 'localhost') }}</a>) 
                    and products will not be publicly visible until approved.
                </p>
            </div>
        </div>
        @elseif(auth()->user()->shop && auth()->user()->shop->is_approved)
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-2xl p-4 flex items-start gap-4 shadow-sm">
            <div class="bg-green-100 p-2 rounded-full text-green-600 mt-0.5">
                <flux:icon name="check-badge" class="size-5" />
            </div>
            <div>
                <h3 class="font-bold text-green-900 leading-tight mb-1">Storefront Approved</h3>
                <p class="text-sm font-medium text-green-800/80">
                    Your store is publicly accessible at 
                    <a href="http://{{ auth()->user()->shop->slug }}.{{ env('APP_DOMAIN', 'localhost') }}:8000" class="underline hover:text-green-900" target="_blank">{{ auth()->user()->shop->slug }}.{{ env('APP_DOMAIN', 'localhost') }}</a>. Keep your products updated!
                </p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-blue-600 dark:text-blue-400">
                    <flux:icon name="shopping-bag" class="size-6" />
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">My Products</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ auth()->user()->shop ? auth()->user()->shop->products()->count() : 0 }}</div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-hub-green dark:text-hub-accent">
                    <flux:icon name="check-badge" class="size-6" />
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Store Status</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-gray-100">
                        {{ auth()->user()->shop && auth()->user()->shop->is_approved ? 'Active' : 'Pending Review' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
