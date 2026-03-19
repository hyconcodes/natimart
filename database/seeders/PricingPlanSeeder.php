<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

class PricingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter Plan',
                'slug' => 'starter',
                'monthly_price' => 0,
                'annual_price' => 0,
                'features' => json_encode(['Basic Storefront', 'Standardized Description', 'Community Support']),
            ],
            [
                'name' => 'Professional Plan',
                'slug' => 'professional',
                'monthly_price' => 5000,
                'annual_price' => 50000,
                'features' => json_encode(['Increased Visibility', 'Priority Support', 'Custom Branding Highlights']),
            ],
            [
                'name' => 'Enterprise / Gold Plan',
                'slug' => 'enterprise-gold',
                'monthly_price' => 15000,
                'annual_price' => 150000,
                'features' => json_encode(['Digital Hub Storefront', 'Sponsored Homepage Placement', 'Advanced Analytics']),
            ],
        ];

        foreach ($plans as $plan) {
            PricingPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
