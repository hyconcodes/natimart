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

        @if(auth()->user()->shop && !auth()->user()->shop->is_approved)
            <div class="space-y-6">
                <div class="bg-brand-950 p-8 rounded-[2.5rem] relative overflow-hidden text-white border-2 border-brand-800 shadow-2xl">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <flux:icon name="shield-check" class="size-32" />
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-3xl font-black italic mb-2">Verification Required</h2>
                        <p class="text-brand-300 max-w-lg mb-8 leading-relaxed">Your store is currently in "Draft" mode. Submit your business credentials to get verified by your State Coordinator and start selling.</p>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <flux:button href="{{ route('vendor.verification', ['shop_slug' => auth()->user()->shop->slug]) }}" variant="primary" icon="arrow-up-tray" wire:navigate>
                                Start Uploading Documents
                            </flux:button>
                            
                            @if(auth()->user()->shop->verification)
                                <div class="flex items-center gap-3">
                                    <div class="w-32 h-2 bg-brand-900 rounded-full overflow-hidden border border-brand-800">
                                        @php
                                            $v = auth()->user()->shop->verification;
                                            $status = $v->verification_status ?? [];
                                            $completed = count(array_filter($status, fn($s) => $s === 'completed'));
                                            $progress = floor(($completed / 3) * 100);
                                        @endphp
                                        <div class="bg-brand-600 h-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-brand-400">{{ $progress }}% Complete</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>
