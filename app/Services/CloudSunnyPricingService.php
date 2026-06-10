<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CloudSunnyPricingService
{
    private CloudSunnyApiService $api;

    public function __construct(CloudSunnyApiService $api)
    {
        $this->api = $api;
    }

    public function getPlans(): array
    {
        return Cache::remember('cloudsunny.products.vps', config('cloudsunny.cache_ttl'), function () {
            try {
                $account = app(CloudSunnyAccountRouter::class)->firstActive();
                if (!$account) {
                    return config('cloudsunny.plans', []);
                }

                $data = $this->api->forAccount($account)->listProducts();
                $items = $data['vps'] ?? [];

                if (!$items) {
                    return config('cloudsunny.plans', []);
                }

                $configPlans = config('cloudsunny.plans', []);
                $providerToSlug = [];
                foreach ($configPlans as $slug => $conf) {
                    $provId = $conf['provider_id'] ?? $slug;
                    $providerToSlug[$provId] = $slug;
                }

                return collect($items)->mapWithKeys(function (array $item) use ($providerToSlug, $configPlans) {
                    $providerId = $this->planKey($item);
                    $slug = $providerToSlug[$providerId] ?? $providerId;
                    
                    $localPlan = $configPlans[$slug] ?? [];
                    
                    $pricing = $item['data_pricing'] ?? [];
                    $monthly = (int) ($pricing['monthly'] ?? $item['amount'] ?? 0);
                    
                    $providerMonthly = $monthly;
                    $providerPricing = $pricing;

                    if (isset($localPlan['price_per_month'])) {
                        $monthly = (int) $localPlan['price_per_month'];
                        $pricing = []; // Clear API pricing to force calculation using custom monthly price
                    }

                    $cores = (int) ($item['cpu'] ?? 1);
                    $ram = (int) ($item['memory'] ?? 1);
                    $disk = (int) ($item['disk'] ?? 20);
                    $isHighEnd = $cores >= 4 && $ram >= 8 && $disk >= 50;

                    return [$slug => [
                        'name' => $localPlan['name'] ?? $item['title'] ?? ('VPS #' . ($item['id'] ?? $providerId)),
                        'desc' => $localPlan['desc'] ?? $item['os'] ?? 'Windows/Linux',
                        'product_id' => (int) $item['id'],
                        'cores' => $cores,
                        'ram' => $ram,
                        'disk' => $disk,
                        'transfer_tb' => 'Unlimited',
                        'network_out_mbps' => $isHighEnd ? 1000 : 100,
                        'price_per_month' => $monthly,
                        'data_pricing' => $pricing,
                        'provider_price_per_month' => $providerMonthly,
                        'provider_data_pricing' => $providerPricing,
                        'provider_plan_key' => $providerId,
                        'badge' => $localPlan['badge'] ?? null,
                    ]];
                })->all();
            } catch (\Throwable $e) {
                return config('cloudsunny.plans', []);
            }
        });
    }

    public function getPlan(string $planId): ?array
    {
        $plans = $this->getPlans();
        if (isset($plans[$planId])) {
            return $plans[$planId];
        }

        // Fallback for old VPS instances that have provider_id stored in database
        foreach ($plans as $plan) {
            if (($plan['provider_plan_key'] ?? '') === $planId) {
                return $plan;
            }
        }

        return null;
    }

    public function getRegions(): array
    {
        return ['vn' => 'Viet Nam'];
    }

    public function getImages(int $productId): array
    {
        return Cache::remember("cloudsunny.products.{$productId}.os", config('cloudsunny.cache_ttl'), function () use ($productId) {
            try {
                $account = app(CloudSunnyAccountRouter::class)->firstActive();
                if (!$account) {
                    return config('cloudsunny.images', []);
                }

                $items = $this->api->forAccount($account)->listOperatingSystems($productId);
                if (!$items) {
                    return config('cloudsunny.images', []);
                }

                return collect($items)->mapWithKeys(function (array $item) {
                    $id = (int) ($item['os-id'] ?? $item['id'] ?? 0);
                    $name = $item['os-name'] ?? $item['name'] ?? ('OS #' . $id);
                    $isWindows = stripos($name, 'windows') !== false;

                    return [$id => [
                        'label' => $name,
                        'icon' => $isWindows ? '🪟' : '🐧',
                        'group' => $isWindows ? 'Windows' : 'Linux',
                    ]];
                })->filter(fn ($item, $id) => $id > 0)->all();
            } catch (\Throwable $e) {
                return config('cloudsunny.images', []);
            }
        });
    }

    public function getDurations(): array
    {
        return collect(config('cloudsunny.billing_cycles', []))
            ->mapWithKeys(fn ($item, $key) => [$key => $item['label']])
            ->all();
    }

    public function calculatePrice(array $plan, string $billingCycle): int
    {
        // If the API provides explicit pricing for this cycle, and we haven't overridden the monthly price, use it.
        // Wait, if we use API explicit pricing, we can't apply our custom discount easily.
        // Let's just ignore the API data_pricing if we have a custom discount.
        // Actually, if we have overridden price_per_month, data_pricing is empty.
        if (isset($plan['data_pricing'][$billingCycle])) {
            return (int) $plan['data_pricing'][$billingCycle];
        }

        $months = $this->monthsForCycle($billingCycle);
        $discountPercent = (int) (config("cloudsunny.billing_cycles.{$billingCycle}.discount_percent") ?? 0);

        $basePrice = !empty($plan['on_sale'])
            ? (int) ($plan['sale_price_per_month'] ?? $plan['price_per_month'])
            : (int) $plan['price_per_month'];

        $totalPrice = $basePrice * max(1, $months);

        if ($discountPercent > 0 && $discountPercent <= 100) {
            $totalPrice = (int) ($totalPrice * (1 - $discountPercent / 100));
        }

        return $totalPrice;
    }

    public function calculateProviderCost(array $plan, string $billingCycle): int
    {
        if (isset($plan['provider_data_pricing'][$billingCycle])) {
            return (int) $plan['provider_data_pricing'][$billingCycle];
        }

        $months = $this->monthsForCycle($billingCycle);
        $baseCost = (int) ($plan['provider_price_per_month'] ?? 0);

        return $baseCost * max(1, $months);
    }

    public function monthsForCycle(string $billingCycle): int
    {
        return (int) (config("cloudsunny.billing_cycles.{$billingCycle}.months") ?? 1);
    }

    public function formatVnd(int $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' d';
    }

    private function planKey(array $item): string
    {
        $title = strtolower((string) ($item['title'] ?? 'vps-' . ($item['id'] ?? uniqid())));
        $key = preg_replace('/[^a-z0-9]+/', '-', $title);
        $key = trim((string) $key, '-');

        return $key ?: 'vps-' . (int) ($item['id'] ?? 0);
    }
}
