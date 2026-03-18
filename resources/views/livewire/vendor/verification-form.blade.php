<?php

use App\Models\ShopVerification;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $tab = 'identity'; // identity, quality, operations, marketing

    public $shop;

    public $verification;

    // Identity Fields
    public $cac_certificate;

    public $tin_number;

    public $cac_status_report;

    public $owner_id_card;

    // Quality Fields
    public $nafdac_number;

    public $son_mancap_cert;

    public $lab_test_report;

    public $trademark_cert;

    // Operations Fields
    public $logistics_sla;

    public $production_address;

    public $production_capacity;

    // Marketing Fields
    public $product_images_sample;
    public $product_descriptions_standard;
    public $pricing_list;

    public function mount()
    {
        $this->shop = Auth::user()->shop;
        $this->verification = $this->shop->verification ?? new ShopVerification(['shop_id' => $this->shop->id]);

        $fields = ['tin_number', 'nafdac_number', 'production_address', 'production_capacity', 'product_descriptions_standard'];

        foreach ($fields as $field) {
            $this->$field = $this->verification->$field;
        }
    }

    public function getProgressProperty()
    {
        $status = $this->verification->verification_status ?? [];
        $steps = ['identity', 'quality', 'operations', 'marketing'];
        $completedCount = 0;

        foreach ($steps as $step) {
            if (isset($status[$step]) && $status[$step] === 'completed') {
                $completedCount++;
            }
        }

        return floor(($completedCount / count($steps)) * 100);
    }

    public function saveIdentity()
    {
        $this->validate([
            'cac_certificate' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'tin_number' => 'nullable|string|max:50',
            'cac_status_report' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'owner_id_card' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $data = ['tin_number' => $this->tin_number];

        if ($this->cac_certificate) {
            $data['cac_certificate'] = $this->cac_certificate->store('verifications/cac', 'public');
        }
        if ($this->cac_status_report) {
            $data['cac_status_report'] = $this->cac_status_report->store('verifications/status', 'public');
        }
        if ($this->owner_id_card) {
            $data['owner_id_card'] = $this->owner_id_card->store('verifications/ids', 'public');
        }

        $this->updateVerification($data);
        $this->markStepCompleted('identity');
        $this->tab = 'quality';
        $this->dispatch('toast', text: 'Identity documents saved.', variant: 'success');
    }

    public function saveQuality()
    {
        $this->validate([
            'nafdac_number' => 'nullable|string|max:100',
            'son_mancap_cert' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'lab_test_report' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'trademark_cert' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $data = ['nafdac_number' => $this->nafdac_number];

        if ($this->son_mancap_cert) {
            $data['son_mancap_cert'] = $this->son_mancap_cert->store('verifications/son', 'public');
        }
        if ($this->lab_test_report) {
            $data['lab_test_report'] = $this->lab_test_report->store('verifications/lab', 'public');
        }
        if ($this->trademark_cert) {
            $data['trademark_cert'] = $this->trademark_cert->store('verifications/trademark', 'public');
        }

        $this->updateVerification($data);
        $this->markStepCompleted('quality');
        $this->tab = 'operations';
        $this->dispatch('toast', text: 'Quality certifications saved.', variant: 'success');
    }

    public function saveOperations()
    {
        $this->validate([
            'logistics_sla' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'production_address' => 'nullable|string',
            'production_capacity' => 'nullable|string|max:255',
        ]);

        $data = [
            'production_address' => $this->production_address,
            'production_capacity' => $this->production_capacity,
        ];

        if ($this->logistics_sla) {
            $data['logistics_sla'] = $this->logistics_sla->store('verifications/logistics', 'public');
        }

        $this->updateVerification($data);
        $this->markStepCompleted('operations');
        $this->tab = 'marketing';
        $this->dispatch('toast', text: 'Operational data saved.', variant: 'success');
    }

    public function saveMarketing()
    {
        $this->validate([
            'product_images_sample' => 'nullable|file|mimes:pdf,jpg,png,zip|max:10240',
            'product_descriptions_standard' => 'nullable|string',
            'pricing_list' => 'nullable|file|mimes:pdf,jpg,png,xlsx,csv|max:5120',
        ]);

        $data = [
            'product_descriptions_standard' => $this->product_descriptions_standard,
        ];

        if ($this->product_images_sample) {
            $data['product_images_sample'] = $this->product_images_sample->store('verifications/marketing/images', 'public');
        }
        if ($this->pricing_list) {
            $data['pricing_list'] = $this->pricing_list->store('verifications/marketing/pricing', 'public');
        }

        $this->updateVerification($data);
        $this->markStepCompleted('marketing');

        $missing = $this->missingItems;
        if (count($missing) > 0) {
            $this->dispatch('modal-show', name: 'missing-items-modal');
        } else {
            $this->dispatch('toast', text: 'Verification submitted successfully!', variant: 'success');
        }
    }

    public function getMissingItemsProperty()
    {
        $missing = [];
        $v = $this->verification;

        // Identity (Check both DB and local state for the final submission)
        if (!$this->cac_certificate && !$v->cac_certificate) {
            $missing[] = 'CAC Certificate';
        }
        if (!$this->cac_status_report && !$v->cac_status_report) {
            $missing[] = 'CAC status report';
        }
        if (!$this->tin_number) {
            $missing[] = 'Tax Identification Number (TIN)';
        }
        if (!$this->owner_id_card && !$v->owner_id_card) {
            $missing[] = 'Valid Means of ID';
        }

        // Quality
        if (!$this->nafdac_number) {
            $missing[] = 'NAFDAC Registration Number';
        }
        if (!$this->son_mancap_cert && !$v->son_mancap_cert) {
            $missing[] = 'SON / MANCAP Certificate';
        }
        if (!$this->lab_test_report && !$v->lab_test_report) {
            $missing[] = 'Recent Lab Test Report';
        }

        // Operations
        if (!$this->production_address) {
            $missing[] = 'Production Address';
        }
        if (!$this->production_capacity) {
            $missing[] = 'Weekly Production Capacity';
        }
        if (!$this->logistics_sla && !$v->logistics_sla) {
            $missing[] = 'Logistics SLA / MOU';
        }

        // Marketing
        if (!$this->product_images_sample && !$v->product_images_sample) {
            $missing[] = 'Product Photos Sample';
        }
        if (!$this->pricing_list && !$v->pricing_list) {
            $missing[] = 'Price List (Wholesale/Retail)';
        }
        if (!$this->product_descriptions_standard) {
            $missing[] = 'Standardized Product Descriptions';
        }

        return $missing;
    }

    protected function updateVerification($data)
    {
        if (!$this->verification->exists) {
            $this->verification->shop_id = $this->shop->id;
        }
        $this->verification->fill($data);
        $this->verification->save();
    }

    protected function markStepCompleted($step)
    {
        $status = $this->verification->verification_status ?? [];
        $status[$step] = 'completed';
        $this->verification->update(['verification_status' => $status]);
    }
}; ?>
<main>

    <!-- Approval Banner for Vendors -->
    @if ($shop->is_approved)
        <div
            class="bg-hub-green/10 border border-hub-green/20 rounded-2xl p-6 flex items-center gap-4 text-hub-green mb-8">
            <div class="p-3 bg-hub-green rounded-xl text-white">
                <flux:icon name="check-badge" class="size-6" />
            </div>
            <div>
                <h4 class="font-black italic uppercase tracking-wider text-sm">Store Fully Verified</h4>
                <p class="text-xs opacity-80">Your business documents have been approved by the State Coordinator. You
                    can now list products and sell globally.</p>
            </div>
        </div>
    @endif

    <!-- Progress Bar -->
    <div
        class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl p-4 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-black uppercase tracking-widest text-brand-600">Verification Progress</span>
            <span class="text-xs font-black text-gray-900 dark:text-gray-100">{{ $this->progress }}%</span>
        </div>
        <div
            class="w-full bg-brand-100 dark:bg-brand-950 h-2 rounded-full overflow-hidden border border-brand-100 dark:border-brand-800">
            <div class="bg-brand-600 h-full transition-all duration-500" style="width: {{ $this->progress }}%"></div>
        </div>
    </div>
    <!-- Tab Switcher -->
    <div
        class="mt-4 flex items-center gap-2 p-1 bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-2xl w-full sm:w-fit overflow-x-auto no-scrollbar">
        <button type="button" wire:click="$set('tab', 'identity')"
            class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'identity' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:bg-brand-50' }}">
            <flux:icon name="identification" class="size-4" />
            1. Identity
        </button>
        <button type="button" wire:click="$set('tab', 'quality')"
            class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'quality' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:bg-brand-50' }}">
            <flux:icon name="shield-check" class="size-4" />
            2. Quality
        </button>
        <button type="button" wire:click="$set('tab', 'operations')"
            class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'operations' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:bg-brand-50' }}">
            <flux:icon name="truck" class="size-4" />
            3. Operations
        </button>
        <button type="button" wire:click="$set('tab', 'marketing')"
            class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'marketing' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-400 hover:bg-brand-50' }}">
            <flux:icon name="photo" class="size-4" />
            4. Digital Assets
        </button>
    </div>

    <!-- Form Sections -->
    <div
        class="bg-white dark:bg-brand-900 border border-brand-200 dark:border-brand-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        @if ($tab === 'identity')
            <form wire:submit="saveIdentity" class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 italic">Business Identity & Legal
                        Status</h3>
                    <p class="text-xs text-gray-500">Upload your legal business registration documents for NBTI
                        verification.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input label="TIN (Tax Identification Number)" wire:model="tin_number"
                        placeholder="Enter your 11-digit TIN" />

                    <div class="space-y-1">
                        <flux:input type="file" label="CAC Certificate of Registration"
                            wire:model="cac_certificate" />
                        @if ($verification->cac_certificate)
                            <div
                                class="flex items-center gap-2 px-3 py-1 bg-brand-50 rounded-lg text-[10px] text-brand-600 font-bold">
                                <flux:icon name="document-check" class="size-3" />
                                Current: {{ basename($verification->cac_certificate) }}
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <flux:input type="file" label="CAC status report" wire:model="cac_status_report" />
                        @if ($verification->cac_status_report)
                            <div
                                class="flex items-center gap-2 px-3 py-1 bg-brand-50 rounded-lg text-[10px] text-brand-600 font-bold">
                                <flux:icon name="document-check" class="size-3" />
                                Current: {{ basename($verification->cac_status_report) }}
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <flux:input type="file" label="Valid Means of ID (Owner)" wire:model="owner_id_card" />
                        @if ($verification->owner_id_card)
                            <div
                                class="flex items-center gap-2 px-3 py-1 bg-brand-50 rounded-lg text-[10px] text-brand-600 font-bold">
                                <flux:icon name="document-check" class="size-3" />
                                Current: {{ basename($verification->owner_id_card) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <flux:button type="submit" variant="primary" icon-trailing="chevron-right">Save & Continue
                    </flux:button>
                </div>
            </form>
        @endif

        @if ($tab === 'quality')
            <form wire:submit="saveQuality" class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 italic">Product Quality & Safety
                        Certifications</h3>
                    <p class="text-xs text-gray-500">Provide NAFDAC, SON, or other regulatory compliance documents.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input label="NAFDAC Registration Number" wire:model="nafdac_number"
                        placeholder="Enter NAFDAC ID" />

                    <div class="space-y-1">
                        <flux:input type="file" label="SON / MANCAP Certificate" wire:model="son_mancap_cert" />
                        @if ($verification->son_mancap_cert)
                            <p class="text-[10px] font-bold text-brand-600">Current:
                                {{ basename($verification->son_mancap_cert) }}</p>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <flux:input type="file" label="Lab Test Report (Recent)" wire:model="lab_test_report" />
                        @if ($verification->lab_test_report)
                            <p class="text-[10px] font-bold text-brand-600">Current:
                                {{ basename($verification->lab_test_report) }}</p>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <flux:input type="file" label="Trademark Certificate (Optional)"
                            wire:model="trademark_cert" />
                        @if ($verification->trademark_cert)
                            <p class="text-[10px] font-bold text-brand-600">Current:
                                {{ basename($verification->trademark_cert) }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <flux:button variant="subtle" wire:click="$set('tab', 'identity')">Back</flux:button>
                    <flux:button type="submit" variant="primary" icon-trailing="chevron-right">Save & Continue
                    </flux:button>
                </div>
            </form>
        @endif

        @if ($tab === 'operations')
            <form wire:submit="saveOperations" class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 italic">Operational & Logistics Data
                    </h3>
                    <p class="text-xs text-gray-500">Provide details about your production capacity and delivery
                        partnerships.</p>
                </div>

                <div class="space-y-6">
                    <flux:textarea label="Warehouse or Production Address" wire:model="production_address"
                        placeholder="Full physical address as per CAC documents" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input label="Weekly Production Capacity" wire:model="production_capacity"
                            placeholder="e.g., 500 units per week" />
                        <flux:input type="file" label="Logistics SLA / MOU Document" wire:model="logistics_sla" />
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <flux:button variant="subtle" wire:click="$set('tab', 'quality')">Back</flux:button>
                    <flux:button type="submit" variant="primary" icon-trailing="chevron-right">Save & Continue
                    </flux:button>
                </div>
            </form>
        @endif

        @if ($tab === 'marketing')
            <form wire:submit="saveMarketing" class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 italic">Digital & Marketing Assets
                    </h3>
                    <p class="text-xs text-gray-500">Ensure your products look "Premium" with high-quality images and
                        descriptions.</p>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <flux:input type="file" label="High-Resolution Product Photos (Zip or PDF Sample)"
                                wire:model="product_images_sample" />
                            @if ($verification->product_images_sample)
                                <p class="text-[10px] font-bold text-brand-600">Current:
                                    {{ basename($verification->product_images_sample) }}</p>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <flux:input type="file" label="Wholesale vs Retail Price List"
                                wire:model="pricing_list" />
                            @if ($verification->pricing_list)
                                <p class="text-[10px] font-bold text-brand-600">Current:
                                    {{ basename($verification->pricing_list) }}</p>
                            @endif
                        </div>
                    </div>

                    <flux:textarea label="Standardized Product Descriptions"
                        wire:model="product_descriptions_standard"
                        placeholder="Include ingredients, weight/volume (e.g., 1.75L), instructions, and safety warnings."
                        rows="5" />
                </div>

                <div class="flex justify-between pt-4">
                    <flux:button variant="subtle" wire:click="$set('tab', 'operations')">Back</flux:button>
                    <flux:button type="submit" variant="primary">Finish Submission</flux:button>
                </div>
            </form>
        @endif
    </div>
    {{-- </div> --}}

    <!-- Missing Items Modal -->
    <flux:modal name="missing-items-modal" class="max-w-lg">
        <div class="space-y-6">
            <div class="flex items-center gap-4 text-amber-600 bg-amber-50 p-6 rounded-2xl border border-amber-200">
                <div class="p-3 bg-amber-600 rounded-xl text-white">
                    <flux:icon name="exclamation-triangle" class="size-6" />
                </div>
                <div>
                    <h4 class="font-black italic uppercase tracking-wider text-sm">Incomplete Submission</h4>
                    <p class="text-xs opacity-80">You still have some required fields or documents missing before you
                        can finalize your verification.</p>
                </div>
            </div>

            <div class="space-y-4">
                <h5 class="text-xs font-black uppercase tracking-widest text-gray-400">Missing Requirements:</h5>
                <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto pr-2">
                    @foreach ($this->missingItems as $item)
                        <div
                            class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-brand-950 rounded-xl border border-gray-100 dark:border-brand-800">
                            <div class="size-2 bg-red-400 rounded-full animate-pulse"></div>
                            <span
                                class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $item }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="text-[10px] text-gray-400 text-center italic">Please navigate back to the relevant tabs and
                complete these requirements.</p>

            <div class="flex justify-center">
                <flux:button variant="primary" x-on:click="$dispatch('modal-close', { name: 'missing-items-modal' })">
                    I'll complete them now
                </flux:button>
            </div>
        </div>
    </flux:modal>
</main>
