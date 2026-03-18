<?php

use Livewire\Volt\Component;
use App\Models\Shop;
use App\Models\Product;
use App\Models\User;

new class extends Component {
    public $selectedMonth;

    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    public function updatedSelectedMonth()
    {
        $this->dispatch('stats-updated', labels: $this->getChartData()['labels'], data: $this->getChartData()['data']);
    }

    public function getChartData()
    {
        $date = \Carbon\Carbon::parse($this->selectedMonth);
        $daysInMonth = $date->daysInMonth;

        $vendorGrowth = Shop::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->selectRaw('DAY(created_at) as day, COUNT(*) as count')->groupBy('day')->get()->pluck('count', 'day')->toArray();

        $labels = [];
        $data = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = $i;
            $data[] = $vendorGrowth[$i] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function with()
    {
        $chartData = $this->getChartData();

        $availableMonths = Shop::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")->distinct()->orderBy('month', 'desc')->get()->pluck('month')->toArray();

        if (!in_array(now()->format('Y-m'), $availableMonths)) {
            array_unshift($availableMonths, now()->format('Y-m'));
        }

        return [
            'labels' => $chartData['labels'],
            'chartData' => $chartData['data'],
            'availableMonths' => array_unique($availableMonths),
            'totalVendors' => Shop::count(),
            'activeProducts' => Product::where('is_approved', true)->count(),
            'pendingVendors' => Shop::where('is_approved', false)->count(),
            'totalUsers' => User::count(),
            'recentApprovals' => Shop::where('is_approved', true)->with('user')->latest('approved_at')->take(4)->get(),
        ];
    }
}; ?>

<div class="space-y-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
            class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-hub-green dark:text-hub-accent">
                <flux:icon name="building-storefront" class="size-6" />
            </div>
            <div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Vendors</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $totalVendors }}</div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-blue-600 dark:text-blue-400">
                <flux:icon name="shopping-bag" class="size-6" />
            </div>
            <div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Products</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $activeProducts }}</div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-amber-500 dark:text-amber-400">
                <flux:icon name="clock" class="size-6" />
            </div>
            <div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Shop</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $pendingVendors }}</div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-brand-900 p-6 rounded-3xl border border-brand-100 dark:border-brand-800 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-brand-50 dark:bg-brand-950 rounded-2xl text-purple-600 dark:text-purple-400">
                <flux:icon name="users" class="size-6" />
            </div>
            <div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">System Users</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $totalUsers }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div
                class="bg-white dark:bg-brand-900 rounded-3xl border border-brand-200 dark:border-brand-800 overflow-hidden min-h-[450px]">
                <div
                    class="p-6 border-b border-brand-100 dark:border-brand-800 flex justify-between items-center bg-gray-50/50 dark:bg-brand-950/50">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-gray-100">Vendor Acquisition Growth</h3>
                        <p class="text-xs text-gray-400">Total vendors joining the ecosystem daily</p>
                    </div>
                    <div class="w-48">
                        <flux:select wire:model.live="selectedMonth" size="sm">
                            @foreach ($availableMonths as $month)
                                <flux:select.option :value="$month">
                                    {{ \Carbon\Carbon::parse($month)->format('F Y') }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
                <div class="p-6 h-[320px] relative" x-data="vendorGrowthChart({ labels: @js($labels), data: @js($chartData) })"
                    x-on:stats-updated.window="updateChart($event.detail.labels, $event.detail.data)" wire:ignore>
                    <canvas id="vendorGrowthChart" x-ref="canvas"></canvas>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function vendorGrowthChart(initialData) {
                return {
                    chart: null,
                    init() {
                        const ctx = this.$refs.canvas.getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: initialData.labels,
                                datasets: [{
                                    label: 'New Vendors',
                                    data: initialData.data,
                                    fill: true,
                                    backgroundColor: 'rgba(21, 128, 61, 0.1)',
                                    borderColor: '#15803d',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#15803d',
                                    pointBorderColor: '#fff',
                                    pointHoverRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: 'rgba(0,0,0,0.05)'
                                        },
                                        ticks: {
                                            stepSize: 1,
                                            color: '#9ca3af'
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            color: '#9ca3af'
                                        }
                                    }
                                }
                            }
                        });
                    },
                    updateChart(labels, data) {
                        if (this.chart) {
                            this.chart.data.labels = labels;
                            this.chart.data.datasets[0].data = data;
                            this.chart.update();
                        }
                    }
                }
            }
        </script>

        <div class="space-y-6">
            <div
                class="bg-white dark:bg-brand-900 rounded-3xl border border-brand-200 dark:border-brand-800 overflow-hidden h-full">
                <div class="p-6 border-b border-brand-100 dark:border-brand-800">
                    <h3 class="font-bold text-gray-900 dark:text-gray-100">Recent System Activity</h3>
                </div>
                <div class="p-6 space-y-6">
                    @forelse($recentApprovals as $shop)
                        <div class="flex items-center gap-4">
                            <div
                                class="size-10 rounded-xl bg-brand-50 dark:bg-brand-950 flex-shrink-0 flex items-center justify-center">
                                <flux:icon name="check-badge" class="size-5 text-hub-green" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">
                                    {{ $shop->name }}</div>
                                <div class="text-xs text-gray-400">Approved globally</div>
                            </div>
                            <div class="text-xs text-gray-500 whitespace-nowrap">
                                {{ $shop->approved_at->diffForHumans(null, true) }}</div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <flux:text class="text-gray-400">No recent activity</flux:text>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
